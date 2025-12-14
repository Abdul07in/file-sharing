<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $pdo = null;

    private function __construct()
    {
        // Private constructor to prevent direct instantiation
    }

    public static function connect(): PDO
    {
        if (self::$pdo === null) {
            try {
                $dsn = sprintf(
                    "mysql:host=%s;dbname=%s;charset=%s",
                    Config::$DB_HOST,
                    Config::$DB_NAME,
                    Config::$DB_CHARSET
                );
                // echo "DEBUG: Connecting to DSN: $dsn\n";

                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];

                self::$pdo = new PDO($dsn, Config::$DB_USER, Config::$DB_PASS, $options);
            } catch (PDOException $e) {
                // Throw exception so the caller can handle fallback (e.g. JSON mode)
                throw new \Exception("Database Connection Failed: " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
