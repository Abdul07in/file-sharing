<?php

namespace App\Controller;

use App\Config\Config;
use PDO;

class DocumentController
{
    private $pdo;

    public function __construct()
    {
        $dsn = "mysql:host=" . Config::$DB_HOST . ";dbname=" . Config::$DB_NAME . ";charset=" . Config::$DB_CHARSET;
        $this->pdo = new PDO($dsn, Config::$DB_USER, Config::$DB_PASS);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function handleRequest()
    {
        $action = $_GET['action'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'save') {
            $this->save();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get') {
            $this->get();
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
        }
    }

    private function save()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $roomKey = $input['room_key'] ?? '';
        $content = $input['content'] ?? '';

        if (!$roomKey) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing room_key']);
            return;
        }

        // Validate user is in the room (Simple check, assumes AuthController verified session)
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }

        $stmt = $this->pdo->prepare("UPDATE rooms SET content = :content WHERE room_key = :room_key");
        $stmt->execute(['content' => $content, 'room_key' => $roomKey]);

        echo json_encode(['status' => 'ok']);
    }

    private function get()
    {
        $roomKey = $_GET['room_key'] ?? '';

        if (!$roomKey) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing room_key']);
            return;
        }

        $stmt = $this->pdo->prepare("SELECT content FROM rooms WHERE room_key = :room_key");
        $stmt->execute(['room_key' => $roomKey]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['content' => $row['content'] ?? '']);
    }
}
