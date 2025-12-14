<?php

namespace App\Controller;

use App\Service\EncryptionService;
use App\Service\StorageService;
use App\Service\PinService;
use App\Model\FileRecord;
use App\Config\Config;
use Exception;

class FileController
{
    private EncryptionService $encryptionService;
    private StorageService $storageService;
    private PinService $pinService;
    private FileRecord $fileRecord;

    public function __construct()
    {
        $this->encryptionService = new EncryptionService();
        $this->storageService = new StorageService();
        $this->fileRecord = new FileRecord();
        $this->pinService = new PinService($this->fileRecord);
    }

    public function handleUpload()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ./');
            exit;
        }

        try {
            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("File upload failed or no file selected.");
            }

            $file = $_FILES['file'];

            if ($file['size'] > Config::$MAX_FILE_SIZE) {
                throw new Exception("File too large. Max limit is 10MB.");
            }

            $content = file_get_contents($file['tmp_name']);
            if ($content === false) {
                throw new Exception("Failed to read uploaded file.");
            }

            // 1. Encrypt
            $encryptedData = $this->encryptionService->encrypt($content);

            // 2. Generate PIN
            $pin = $this->pinService->generateUniquePin();

            // 3. Save to DB (Content + Metadata)
            $this->fileRecord->save(
                $pin,
                $file['name'],
                $encryptedData['data'],
                $encryptedData['iv']
            );

            // Success: Redirect with PIN
            header('Location: ./?status=success&pin=' . $pin . '&filename=' . urlencode($file['name']));
            exit;

        } catch (Exception $e) {
            header('Location: ./?status=error&message=' . urlencode($e->getMessage()));
            exit;
        }
    }

    public function handleDownload()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ./receive');
            exit;
        }

        try {
            $pin = $_POST['pin'] ?? '';
            if (empty($pin)) {
                throw new Exception("PIN is required.");
            }

            // 1. Find Record (Metadata)
            $record = $this->fileRecord->findByPin($pin);
            if (!$record) {
                throw new Exception("Invalid PIN or file expired.");
            }

            // 2. Retrieve Encrypted Content from DB
            $encryptedContent = $this->fileRecord->getContent($pin);

            if (!$encryptedContent) {
                throw new Exception("File content missing.");
            }

            // 3. Decrypt
            $decryptedContent = $this->encryptionService->decrypt($encryptedContent, $record['encryption_iv']);

            // 4. Force Download
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $record['file_name'] . '"');
            header('Content-Length: ' . strlen($decryptedContent));

            // 5. Cleanup (Burn on Read)
            $this->fileRecord->delete($pin);

            echo $decryptedContent;
            exit;

        } catch (Exception $e) {
            header('Location: ./receive?status=error&message=' . urlencode($e->getMessage()));
            exit;
        }
    }
}
