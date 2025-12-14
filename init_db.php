<?php

// Setup using bootstrap (includes autoloader and config loading)
require_once __DIR__ . '/src/bootstrap.php';

use App\Config\Config;

try {
    // 1. Connect to MySQL Server (No DB selected yet)
    $dsn = "mysql:host=" . Config::$DB_HOST . ";charset=" . Config::$DB_CHARSET;
    $pdo = new PDO($dsn, Config::$DB_USER, Config::$DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to MySQL server successfully.\n";

    // 2. Create Database
    $dbName = Config::$DB_NAME;
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName`");
    echo "Database '$dbName' checked/created.\n";

    // 3. Select Database
    $pdo->exec("USE `$dbName`");

    // 4. Create Table
    $sql = "
    CREATE TABLE IF NOT EXISTS uploads (
        id INT AUTO_INCREMENT PRIMARY KEY,
        file_pin VARCHAR(4) NOT NULL UNIQUE,
        file_name VARCHAR(255) NOT NULL,
        storage_path VARCHAR(255) NOT NULL,
        encryption_iv VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";
    $pdo->exec($sql);
    echo "Table 'uploads' checked/created successfully.\n";

    // 5. Create Text Shares Table
    $sqlText = "
    CREATE TABLE IF NOT EXISTS text_shares (
        id INT AUTO_INCREMENT PRIMARY KEY,
        pin VARCHAR(4) NOT NULL UNIQUE,
        content LONGTEXT NOT NULL,
        iv VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );
    ";
    $pdo->exec($sqlText);
    echo "Table 'text_shares' checked/created successfully.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (strpos($e->getMessage(), 'could not find driver') !== false) {
        echo "\n[!] It seems your PHP CLI environment is missing the 'pdo_mysql' driver.\n";
        echo "    Please run this script using your web server's PHP executable,\n";
        echo "    or import 'database.sql' manually into your database tool.\n";
    }
    exit(1);
}
