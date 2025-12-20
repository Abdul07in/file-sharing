<?php
require_once __DIR__ . '/src/bootstrap.php';
use App\Config\Config;

$dsn = "mysql:host=" . Config::$DB_HOST . ";dbname=" . Config::$DB_NAME . ";charset=" . Config::$DB_CHARSET;
$pdo = new PDO($dsn, Config::$DB_USER, Config::$DB_PASS);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Updating rooms table...\n";
try {
    $pdo->exec("ALTER TABLE rooms ADD COLUMN content LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL");
    echo "Added content column.\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), "Duplicate column name") !== false) {
        echo "Column content already exists.\n";
    } else {
        throw $e;
    }
}
echo "Done.\n";
