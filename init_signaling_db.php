<?php
require_once __DIR__ . '/src/bootstrap.php';
use App\Config\Config;

$dsn = "mysql:host=" . Config::$DB_HOST . ";dbname=" . Config::$DB_NAME . ";charset=" . Config::$DB_CHARSET;
$pdo = new PDO($dsn, Config::$DB_USER, Config::$DB_PASS);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Creating signaling_messages table...\n";
$sql = "
CREATE TABLE IF NOT EXISTS signaling_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_key VARCHAR(50) NOT NULL,
    sender_id INT NOT NULL,
    message JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (room_key),
    INDEX (created_at)
);
";
$pdo->exec($sql);

echo "Clearing old messages...\n";
$pdo->exec("DELETE FROM signaling_messages WHERE created_at < NOW() - INTERVAL 1 HOUR");

echo "Done.\n";
