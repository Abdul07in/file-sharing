<?php

/**
 * Cleanup Script
 * Run this via cron job every hour.
 * Example: 0 * * * * php /path/to/cleanup.php
 */

require_once __DIR__ . '/src/bootstrap.php';

use App\Config\Database;
use App\Service\StorageService;

$pdo = Database::connect();
$storageService = new StorageService();

echo "Starting cleanup...\n";

// Find files older than 24 hours
$sql = "SELECT id, storage_path FROM uploads WHERE created_at < NOW() - INTERVAL 1 DAY";
$stmt = $pdo->query($sql);
$files = $stmt->fetchAll();

$deletedCount = 0;

foreach ($files as $file) {
    try {
        // Delete from disk
        $storageService->delete($file['storage_path']);

        // Delete from DB
        $deleteStmt = $pdo->prepare("DELETE FROM uploads WHERE id = :id");
        $deleteStmt->execute([':id' => $file['id']]);

        $deletedCount++;
        echo "Deleted file ID: {$file['id']}\n";
    } catch (Exception $e) {
        echo "Error deleting file ID: {$file['id']} - " . $e->getMessage() . "\n";
    }
}

echo "Cleanup complete. Deleted $deletedCount files.\n";
