<?php

require_once __DIR__ . '/../src/bootstrap.php';

use App\Model\User;
use App\Model\Room;
use App\Model\RoomParticipant;
use App\Config\Config;

$dsn = "mysql:host=" . Config::$DB_HOST . ";dbname=" . Config::$DB_NAME . ";charset=" . Config::$DB_CHARSET;
$pdo = new PDO($dsn, Config::$DB_USER, Config::$DB_PASS);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$userModel = new User($pdo);
$roomModel = new Room($pdo);
$participantModel = new RoomParticipant($pdo);

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "Running Tests...\n";

// 1. Test User Creation
$testUser = 'testuser_' . time();
$testPass = 'password123';
try {
    echo "Creating user: $testUser\n";
    $userId = $userModel->create($testUser, $testPass);
    echo "[PASS] User created with ID: $userId\n";
} catch (Exception $e) {
    echo "[FAIL] User creation failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

// 2. Test Login
$user = $userModel->findByUsername($testUser);
if ($user && $userModel->verifyPassword($testPass, $user['password_hash'])) {
    echo "[PASS] User login verified\n";
} else {
    echo "[FAIL] User login failed\n";
    exit(1);
}

// 3. Test Room Creation
$roomName = "Test Room " . time();
echo "Creating room: $roomName\n";
try {
    $roomKey = $roomModel->create($userId, $roomName);
    echo "[PASS] Room created with Key: $roomKey\n";
} catch (Exception $e) {
    echo "[FAIL] Room creation failed: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

// 4. Test Participants
$room = $roomModel->findByKey($roomKey);
$participantModel->addParticipant($room['id'], $userId, 'owner');
$p = $participantModel->getParticipant($room['id'], $userId);

if ($p && $p['role'] === 'owner') {
    echo "[PASS] Participant added as owner\n";
} else {
    echo "[FAIL] Participant check failed\n";
    exit(1);
}

echo "All backend tests passed!\n";
