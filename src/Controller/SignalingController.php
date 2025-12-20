<?php

namespace App\Controller;

use App\Config\Config;
use PDO;

class SignalingController
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
        // Simple router for signaling
        $action = $_GET['action'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'publish') {
            $this->publish();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'poll') {
            $this->poll();
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
        }
    }

    private function publish()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        $roomKey = $input['room_key'] ?? '';
        $message = $input['message'] ?? null;
        $senderId = $_SESSION['user_id'] ?? 0; // Optional, can be 0 for anon if allowed (but we require auth)

        if (!$roomKey || !$message) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing room_key or message']);
            return;
        }

        // Clean old messages occasionally (1% chance)
        if (rand(1, 100) === 1) {
            $this->pdo->exec("DELETE FROM signaling_messages WHERE created_at < NOW() - INTERVAL 5 MINUTE");
        }

        $stmt = $this->pdo->prepare("INSERT INTO signaling_messages (room_key, sender_id, message) VALUES (:room_key, :sender_id, :message)");
        $stmt->execute([
            'room_key' => $roomKey,
            'sender_id' => $senderId,
            'message' => json_encode($message)
        ]);

        echo json_encode(['status' => 'ok']);
    }

    private function poll()
    {
        // Short Polling: Return immediately
        $roomKey = $_GET['room_key'] ?? '';
        $lastId = (int) ($_GET['last_id'] ?? 0);

        if (!$roomKey) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing room_key']);
            return;
        }

        // Close session to prevent session locking (crucial for concurrency in PHP)
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        // Check for new messages ONCE
        $stmt = $this->pdo->prepare("
            SELECT id, message, created_at 
            FROM signaling_messages 
            WHERE room_key = :room_key AND id > :last_id 
            ORDER BY id ASC 
            LIMIT 50
        ");
        $stmt->execute(['room_key' => $roomKey, 'last_id' => $lastId]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($messages)) {
            $output = [];
            foreach ($messages as $row) {
                $output[] = [
                    'id' => $row['id'],
                    'data' => json_decode($row['message']),
                    'created_at' => $row['created_at']
                ];
            }
            echo json_encode(['messages' => $output]);
        } else {
            echo json_encode(['messages' => []]);
        }
    }
}
