<?php
require 'src/bootstrap.php';
use App\Config\Config;

echo "Upload Dir (Raw): " . Config::$UPLOAD_DIR . "\n";
echo "Resolved Upload Dir: " . realpath(Config::$UPLOAD_DIR) . "\n";

$tempDir = Config::$UPLOAD_DIR . 'temp/';
echo "Temp Dir: $tempDir\n";

if (!is_dir($tempDir)) {
    echo "Temp dir does not exist. Attempting creation...\n";
    if (!mkdir($tempDir, 0777, true)) {
        echo "Failed to create temp dir.\n";
        print_r(error_get_last());
    } else {
        echo "Created temp dir successfully.\n";
    }
} else {
    echo "Temp dir exists.\n";
}

$testFile = $tempDir . 'test_write_' . uniqid() . '.txt';
echo "Attempting to write to: $testFile\n";

if (file_put_contents($testFile, 'test') === false) {
    echo "Failed to write to temp file.\n";
    print_r(error_get_last());
} else {
    echo "Successfully wrote to temp file.\n";
    unlink($testFile);
}
