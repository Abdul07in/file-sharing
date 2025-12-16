<?php

namespace App\Config;

class Config
{
    // Database Credentials
    public static string $DB_HOST = 'localhost';
    public static string $DB_NAME = '1383838';
    public static string $DB_USER = '1383838';
    public static string $DB_PASS = 'abdulhoon123';
    public static string $DB_CHARSET = 'utf8mb4';

    // Encryption Settings
    public static string $ENCRYPTION_KEY = '12345678901234567890123456789012';
    public static string $CIPHER_METHOD = 'AES-256-CBC';

    // File Constraints
    public static int $MAX_FILE_SIZE = 67108864; // 64MB
    public static string $UPLOAD_DIR;

    public static function load(): void
    {
        // No longer loading from .env
        // Paths are relative to this file: src/Config/Config.php
        // We want: src/Config/../../uploads/ -> root/uploads/
        self::$UPLOAD_DIR = __DIR__ . '/../../uploads/';
    }
}
