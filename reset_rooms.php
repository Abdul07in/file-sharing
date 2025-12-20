<?php
require_once __DIR__ . '/src/bootstrap.php';
use App\Config\Config;

$dsn = "mysql:host=" . Config::$DB_HOST . ";dbname=" . Config::$DB_NAME . ";charset=" . Config::$DB_CHARSET;
$pdo = new PDO($dsn, Config::$DB_USER, Config::$DB_PASS);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Dropping rooms table...\n";
$pdo->exec("DROP TABLE IF EXISTS room_participants"); // Drop dependent first
$pdo->exec("DROP TABLE IF EXISTS rooms");

echo "Recreating rooms table...\n";
$sqlRooms = "
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_key VARCHAR(10) NOT NULL UNIQUE,
    owner_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE
);
";
$pdo->exec($sqlRooms);

echo "Recreating room_participants table...\n";
$sqlParticipants = "
    CREATE TABLE IF NOT EXISTS room_participants (
        id INT AUTO_INCREMENT PRIMARY KEY,
        room_id INT NOT NULL,
        user_id INT NOT NULL,
        role ENUM('owner', 'editor', 'viewer') DEFAULT 'viewer',
        status ENUM('active', 'banned') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_participation (room_id, user_id),
        FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );
    ";
$pdo->exec($sqlParticipants);

echo "Done.\n";
