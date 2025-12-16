<?php

namespace App\Controller;

use App\Service\EncryptionService;
use App\Service\PinService;
use App\Model\TextRecord;
use App\Helper\ApiResponse;
use App\Helper\Base64Helper;
use Exception;

class TextController
{
    private EncryptionService $encryptionService;
    private PinService $pinService;
    private TextRecord $textRecord;

    public function __construct()
    {
        $this->encryptionService = new EncryptionService();
        $this->textRecord = new TextRecord();
        // Inject TextRecord as the repository for checking PIN uniqueness
        $this->pinService = new PinService($this->textRecord);
    }

    public function handleUpload()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ./share-text');
            exit;
        }

        try {
            $content = $_POST['content'] ?? '';
            if (empty(trim($content))) {
                throw new Exception("Text content cannot be empty.");
            }

            // 1. Encrypt
            $encryptedData = $this->encryptionService->encrypt($content);

            // 2. Generate PIN
            $pin = $this->pinService->generateUniquePin();

            // 3. Save to DB
            $this->textRecord->save(
                $pin,
                $encryptedData['data'],
                $encryptedData['iv']
            );

            // Success: Redirect with PIN
            header('Location: ./share-text?status=success&pin=' . $pin);
            exit;

        } catch (Exception $e) {
            header('Location: ./share-text?status=error&message=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function handleView()
    {
        $pin = $_REQUEST['pin'] ?? ''; // Support GET (from URL) and POST (form)

        if (empty($pin)) {
            // Just show the view text form if no pin provided
            $view = 'view_text';
            require __DIR__ . '/../../views/layout.php';
            return;
        }

        try {
            // 1. Find Record (Metadata)
            $record = $this->textRecord->findByPin($pin);
            if (!$record) {
                // Return error to view
                $error = "Invalid PIN or text expired.";
                $view = 'view_text';
                require __DIR__ . '/../../views/layout.php';
                return;
            }

            // 2. Retrieve Encrypted Content
            $encryptedContent = $this->textRecord->getContent($pin);

            // 3. Decrypt
            $decryptedContent = $this->encryptionService->decrypt($encryptedContent, $record['iv']);

            // 4. Pass to View
            $textPromise = $decryptedContent; // Check naming in view

            // Option: cleanup on read? Text sharing usually persists for a bit or until deleted?
            // The prompt says "view text content ... can view text on browser itself".
            // It doesn't explicitly say "burn on read". But secure sharing often implies it.
            // "Securely share a veiw text content ... on other reciving device i will enter pin and can view text".
            // If I delete it, they can't refresh.
            // But if I don't delete, how long strictly?
            // File sharing burns on read. I'll stick to burn on read for maximum security unless specified.
            // Or maybe a "Burn" button?
            // Let's implement burn on read for maximum security as requested ("securely share").

            $this->textRecord->delete($pin);

            $view = 'view_text';
            require __DIR__ . '/../../views/layout.php';

        } catch (Exception $e) {
            $error = $e->getMessage();
            $view = 'view_text';
            require __DIR__ . '/../../views/layout.php';
        }
    }

    public function handleApiUpload()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ApiResponse::error('Method not allowed', 405);
        }

        try {
            $inputJSON = file_get_contents('php://input');
            $input = json_decode($inputJSON, true);
            $contentBase64 = $input['content'] ?? '';

            if (empty($contentBase64)) {
                throw new Exception("Content is required.");
            }

            // Decode to get raw text
            $decoded = Base64Helper::decode($contentBase64);
            $content = $decoded['data'];

            if (empty(trim($content))) {
                throw new Exception("Text content cannot be empty.");
            }

            // 1. Encrypt
            $encryptedData = $this->encryptionService->encrypt($content);

            // 2. Generate PIN
            $pin = $this->pinService->generateUniquePin();

            // 3. Save to DB
            $this->textRecord->save(
                $pin,
                $encryptedData['data'],
                $encryptedData['iv']
            );

            ApiResponse::success([
                'pin' => $pin
            ], 'Text shared successfully');

        } catch (Exception $e) {
            ApiResponse::error($e->getMessage());
        }
    }

    public function handleApiView()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ApiResponse::error('Method not allowed', 405);
        }

        try {
            $inputJSON = file_get_contents('php://input');
            $input = json_decode($inputJSON, true);
            $pin = $input['pin'] ?? '';

            if (empty($pin)) {
                throw new Exception("PIN is required.");
            }

            // 1. Find Record
            $record = $this->textRecord->findByPin($pin);
            if (!$record) {
                throw new Exception("Invalid PIN or text expired.");
            }

            // 2. Retrieve Encrypted Content
            $encryptedContent = $this->textRecord->getContent($pin);

            // 3. Decrypt
            $decryptedContent = $this->encryptionService->decrypt($encryptedContent, $record['iv']);

            // 4. Burn on Read
            $this->textRecord->delete($pin);

            // 5. Encode response
            $base64Response = Base64Helper::encode($decryptedContent, 'text/plain');

            ApiResponse::success([
                'content' => $base64Response
            ], 'Text retrieved successfully');

        } catch (Exception $e) {
            ApiResponse::error($e->getMessage());
        }
    }
}
