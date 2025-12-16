<?php
require 'src/bootstrap.php';
use App\Config\Database;

try {
    $pdo = Database::connect();
    $sql = "
    CREATE TABLE IF NOT EXISTS file_chunks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        upload_id VARCHAR(255) NOT NULL,
        chunk_index INT NOT NULL,
        chunk_data LONGBLOB NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (upload_id),
        INDEX (upload_id, chunk_index)
    );";

    $pdo->exec($sql);
    echo "Migration successful: file_chunks table created.\n";
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
