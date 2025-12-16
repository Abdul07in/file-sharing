<?php
require 'src/bootstrap.php';
use App\Config\Config;
Config::load();
$tempDir = Config::$UPLOAD_DIR . 'temp/';

function deleteDir($dirPath)
{
    if (!is_dir($dirPath)) {
        return;
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}

if (is_dir($tempDir)) {
    echo "Removing $tempDir...\n";
    deleteDir($tempDir);
    echo "Removed.\n";
} else {
    echo "Temp dir does not exist.\n";
}
