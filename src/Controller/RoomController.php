<?php

namespace App\Controller;

use App\Config\Config;
use App\Model\Room;
use App\Model\RoomParticipant;
use App\Model\User;
use PDO;
use RuntimeException;

class RoomController
{
    private $pdo;
    private $roomModel;
    private $participantModel;
    private $userModel;

    public function __construct()
    {
        $dsn = "mysql:host=" . Config::$DB_HOST . ";dbname=" . Config::$DB_NAME . ";charset=" . Config::$DB_CHARSET;
        $this->pdo = new PDO($dsn, Config::$DB_USER, Config::$DB_PASS);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->roomModel = new Room($this->pdo);
        $this->participantModel = new RoomParticipant($this->pdo);
        $this->userModel = new User($this->pdo);
    }

    public function dashboard()
    {
        $this->requireAuth();
        $userId = $_SESSION['user_id'];

        $myRooms = $this->roomModel->getRoomsByOwner($userId);
        $sharedRooms = $this->participantModel->getUserRooms($userId);

        $view = 'dashboard';
        require __DIR__ . '/../../views/layout.php';
    }

    public function createRoom()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? 'New Room';
            $roomKey = $this->roomModel->create($_SESSION['user_id'], $name);

            // Add creator as owner in participants table for easier querying, though optional given strict schema
            $id = $this->roomModel->findByKey($roomKey)['id'];
            $this->participantModel->addParticipant($id, $_SESSION['user_id'], 'owner');

            header("Location: ./room?key=$roomKey");
            exit;
        }
    }

    public function joinRoom()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $roomKey = $_POST['room_key'] ?? '';
            $room = $this->roomModel->findByKey($roomKey);

            if (!$room) {
                header('Location: ./dashboard?error=Room not found');
                exit;
            }

            // Add user as viewer by default if not already there
            $this->participantModel->addParticipant($room['id'], $_SESSION['user_id'], 'viewer');

            header("Location: ./room?key=$roomKey");
            exit;
        }
    }

    public function room()
    {
        $this->requireAuth();
        $roomKey = $_GET['key'] ?? '';

        $room = $this->roomModel->findByKey($roomKey);
        if (!$room) {
            header('Location: ./dashboard?error=Room not found');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $participant = $this->participantModel->getParticipant($room['id'], $userId);

        // If not participant and not owner (fallback), deny or auto-join?
        // Let's safe guard: strictly must be participant. 
        // Owner added themselves in createRoom so this covers everyone.
        if (!$participant) {
            // Optional: Auto-join? No, explicit join prefers.
            header('Location: ./dashboard?error=You must join the room first');
            exit;
        }

        if ($participant['status'] === 'banned') {
            header('Location: ./dashboard?error=You are banned from this room');
            exit;
        }

        $view = 'room';
        require __DIR__ . '/../../views/layout.php';
    }

    // API for fetching room details/authenticating socket connection
    public function apiRoomDetails()
    {
        header('Content-Type: application/json');
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        $roomKey = $_GET['key'] ?? '';
        $room = $this->roomModel->findByKey($roomKey);

        if (!$room) {
            http_response_code(404);
            echo json_encode(['error' => 'Room not found']);
            exit;
        }

        $participant = $this->participantModel->getParticipant($room['id'], $_SESSION['user_id']);
        if (!$participant || $participant['status'] === 'banned') {
            http_response_code(403);
            echo json_encode(['error' => 'Access denied']);
            exit;
        }

        echo json_encode([
            'room' => $room,
            'me' => [
                'id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'],
                'role' => $participant['role']
            ]
        ]);
        exit;
    }

    private function requireAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ./login');
            exit;
        }
    }
}
