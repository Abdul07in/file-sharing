<?php

namespace App\Controller;

use App\Service\EncryptionService;
use App\Service\StorageService;
use App\Service\PinService;
use App\Model\FileRecord;
use App\Config\Config;
use App\Helper\ApiResponse;
use App\Helper\Base64Helper;
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

    public function handleApiUpload()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ApiResponse::error('Method not allowed', 405);
        }

        try {
            $inputJSON = file_get_contents('php://input');
            $input = json_decode($inputJSON, true);

            // Chunked Upload Handling
            if (isset($input['action'])) {
                $this->handleChunkedUpload($input);
                return;
            }

            // Legacy Single-Shot Upload
            if (!isset($input['filename']) || !isset($input['content'])) {
                throw new Exception("Missing filename or content.");
            }

            $filename = $input['filename'];
            $base64Content = $input['content'];

            // 1. Decode Base64
            $decoded = Base64Helper::decode($base64Content);
            $content = $decoded['data'];

            if (strlen($content) > Config::$MAX_FILE_SIZE) {
                throw new Exception("File too large. Max limit is " . (Config::$MAX_FILE_SIZE / 1024 / 1024) . "MB.");
            }

            // 2. Encrypt
            $encryptedData = $this->encryptionService->encrypt($content);

            // 3. Generate PIN
            $pin = $this->pinService->generateUniquePin();

            // 4. Save to DB
            $this->fileRecord->save(
                $pin,
                $filename,
                $encryptedData['data'],
                $encryptedData['iv']
            );

            ApiResponse::success([
                'pin' => $pin,
                'filename' => $filename
            ], 'File uploaded successfully');

        } catch (Exception $e) {
            ApiResponse::error($e->getMessage());
        }
    }

    private function handleChunkedUpload(array $input): void
    {
        $action = $input['action'];
        $tempDir = Config::$UPLOAD_DIR . 'temp/';

        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0777, true);
        }

        switch ($action) {
            case 'init':
                $uploadId = uniqid('upload_', true);
                // Create empty file
                if (file_put_contents($tempDir . $uploadId, '') === false) {
                    throw new Exception("Failed to initialize upload.");
                }
                ApiResponse::success(['upload_id' => $uploadId]);
                break;

            case 'chunk':
                $uploadId = $input['upload_id'] ?? '';
                $chunkData = $input['chunk_data'] ?? ''; // Base64

                if (!$uploadId || !file_exists($tempDir . $uploadId)) {
                    throw new Exception("Invalid upload ID.");
                }

                $decoded = Base64Helper::decode($chunkData);
                $data = $decoded['data'];

                // Append to temp file
                if (file_put_contents($tempDir . $uploadId, $data, FILE_APPEND) === false) {
                    throw new Exception("Failed to write chunk.");
                }
                ApiResponse::success(['status' => 'chunk_received']);
                break;

            case 'complete':
                $uploadId = $input['upload_id'] ?? '';
                $filename = $input['filename'] ?? 'unknown_file';

                if (!$uploadId || !file_exists($tempDir . $uploadId)) {
                    throw new Exception("Invalid upload ID.");
                }

                $tempFile = $tempDir . $uploadId;
                $content = file_get_contents($tempFile);

                if (strlen($content) > Config::$MAX_FILE_SIZE) {
                    unlink($tempFile);
                    throw new Exception("File too large. Max limit is " . (Config::$MAX_FILE_SIZE / 1024 / 1024) . "MB.");
                }

                // Encrypt
                $encryptedData = $this->encryptionService->encrypt($content);

                // Generate PIN
                $pin = $this->pinService->generateUniquePin();

                // Save
                $this->fileRecord->save(
                    $pin,
                    $filename,
                    $encryptedData['data'],
                    $encryptedData['iv']
                );

                // Cleanup
                unlink($tempFile);

                ApiResponse::success([
                    'pin' => $pin,
                    'filename' => $filename
                ], 'File uploaded successfully');
                break;

            default:
                throw new Exception("Invalid action.");
        }
    }

    public function handleApiDownload()
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
            $record = $this->fileRecord->findByPin($pin);
            if (!$record) {
                throw new Exception("Invalid PIN or file expired.");
            }

            // 2. Retrieve Encrypted Content
            $encryptedContent = $this->fileRecord->getContent($pin);
            if (!$encryptedContent) {
                throw new Exception("File content missing.");
            }

            // 3. Decrypt
            $decryptedContent = $this->encryptionService->decrypt($encryptedContent, $record['encryption_iv']);

            // 4. Encode as Base64 for transport
            $base64Response = Base64Helper::encode($decryptedContent);

            // 5. Cleanup (Burn on Read)
            $this->fileRecord->delete($pin);

            ApiResponse::success([
                'filename' => $record['file_name'],
                'content' => $base64Response,
                'mime_type' => 'application/octet-stream'
            ], 'File retrieved successfully');

        } catch (Exception $e) {
            ApiResponse::error($e->getMessage());
        }
    }
}
