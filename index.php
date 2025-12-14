<?php

// Serve static files directly if using the built-in PHP server
if (php_sapi_name() === 'cli-server') {
    $file = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($file)) {
        return false;
    }
}

// Enable Error Reporting for Debugging
// Moved to src/bootstrap.php which also handles autoloading
require_once __DIR__ . '/src/bootstrap.php';

use App\Controller\HomeController;
use App\Controller\FileController;
use App\Controller\TextController;

// Smart Router handles Subdirectories
$scriptName = $_SERVER['SCRIPT_NAME']; // e.g. /folder/index.php
$scriptDir = dirname($scriptName);     // e.g. /folder

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Remove script directory from URI to get relative path
if ($scriptDir !== '/' && $scriptDir !== '\\' && strpos($uri, $scriptDir) === 0) {
    $uri = substr($uri, strlen($scriptDir));
}

// Ensure URI starts with /
if (empty($uri)) {
    $uri = '/';
}

$homeController = new HomeController();
$fileController = new FileController();
$textController = new TextController(); // Instantiate TextController

if ($uri === '/' || $uri === '/index.php') {
    $homeController->index();
} elseif ($uri === '/upload' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $fileController->handleUpload();
} elseif ($uri === '/receive') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $homeController->receive();
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $fileController->handleDownload();
    }
} elseif ($uri === '/share-text') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $view = 'share_text';
        require __DIR__ . '/views/layout.php';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $textController->handleUpload();
    }
} elseif ($uri === '/view-text') {
    $textController->handleView();
} else {
    // Debug info
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "<p>Requested URI: " . htmlspecialchars($uri) . "</p>";
}
