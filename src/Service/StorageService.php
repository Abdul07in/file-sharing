<?php

namespace App\Service;

use App\Config\Config;
use Exception;

class StorageService
{
    public function save(string $filename, string $content): string
    {
        // Ensure uploads directory exists
        if (!is_dir(Config::$UPLOAD_DIR)) {
            if (!mkdir(Config::$UPLOAD_DIR, 0777, true)) {
                $err = error_get_last();
                throw new Exception("Failed to create uploads directory: " . ($err['message'] ?? ''));
            }
        }

        // Try to fix permissions if not writable
        if (!is_writable(Config::$UPLOAD_DIR)) {
            @chmod(Config::$UPLOAD_DIR, 0777);
        }

        if (!is_writable(Config::$UPLOAD_DIR)) {
            throw new Exception("Uploads directory is NOT writable. Please CHMOD 777 the 'uploads' folder via FTP.");
        }

        // Generate a secure unique filename to prevent overwrites or traversal
        $uniqueName = bin2hex(random_bytes(16)) . '_' . basename($filename);
        $path = Config::$UPLOAD_DIR . $uniqueName;

        if (file_put_contents($path, $content) === false) {
            $err = error_get_last();
            $msg = $err['message'] ?? 'Unknown error';
            throw new Exception("Failed to save file: $msg. Path: $path");
        }

        return $path;
    }

    public function retrieve(string $path): string
    {
        if (!file_exists($path)) {
            throw new Exception("File not found on disk");
        }

        $content = file_get_contents($path);

        if ($content === false) {
            throw new Exception("Failed to read file from disk");
        }

        return $content;
    }

    public function delete(string $path): void
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
