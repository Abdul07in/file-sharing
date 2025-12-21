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
use App\Controller\AuthController;
use App\Controller\RoomController;
use App\Controller\SignalingController;
use App\Controller\DocumentController;

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
$textController = new TextController();
$authController = new AuthController();
$roomController = new RoomController();

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
} elseif ($uri === '/p2p') {
    $view = 'p2p';
    require __DIR__ . '/views/layout.php';
} elseif ($uri === '/share-text') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $view = 'share_text';
        require __DIR__ . '/views/layout.php';
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $textController->handleUpload();
    }
} elseif ($uri === '/view-text') {
    $textController->handleView();

    // Auth Routes
} elseif ($uri === '/login') {
    $authController->login();
} elseif ($uri === '/register') {
    $authController->register();
} elseif ($uri === '/logout') {
    $authController->logout();

    // Room Routes
} elseif ($uri === '/dashboard') {
    $roomController->dashboard();
} elseif ($uri === '/create-room') {
    $roomController->createRoom();
} elseif ($uri === '/delete-room') {
    $roomController->deleteRoom();
} elseif ($uri === '/join-room') {
    $roomController->joinRoom();
} elseif ($uri === '/room') {
    $roomController->room();
} elseif ($uri === '/api/room/details') {
    $roomController->apiRoomDetails();

    // API Routes
} elseif ($uri === '/api/upload') {
    $fileController->handleApiUpload();
} elseif ($uri === '/api/receive') {
    $fileController->handleApiDownload();
} elseif ($uri === '/api/share-text') {
    $textController->handleApiUpload();
} elseif ($uri === '/api/view-text') {
    $textController->handleApiView();
} elseif ($uri === '/api/signaling') {
    $signalingController = new SignalingController();
    $signalingController->handleRequest();
} elseif ($uri === '/api/document') {
    $docController = new DocumentController();
    $docController->handleRequest();
} else {
    // Debug info
    header("HTTP/1.0 404 Not Found");
    echo "<h1>404 Not Found</h1>";
    echo "<p>Requested URI: " . htmlspecialchars($uri) . "</p>";
}
