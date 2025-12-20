<?php
require_once __DIR__ . '/src/bootstrap.php';
use App\Config\Config;

try {
    $dsn = "mysql:host=" . Config::$DB_HOST . ";dbname=" . Config::$DB_NAME . ";charset=" . Config::$DB_CHARSET;
    $pdo = new PDO($dsn, Config::$DB_USER, Config::$DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if column exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM rooms LIKE 'is_public'");
    $stmt->execute();
    if ($stmt->fetch()) {
        echo "Column 'is_public' already exists.\n";
    } else {
        $pdo->exec("ALTER TABLE rooms ADD COLUMN is_public BOOLEAN DEFAULT 0 AFTER name");
        echo "Column 'is_public' added successfully.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
