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
            $isPublic = isset($_POST['is_public']);
            $roomKey = $this->roomModel->create($_SESSION['user_id'], $name, $isPublic);

            // Add creator as owner in participants table for easier querying, though optional given strict schema
            $id = $this->roomModel->findByKey($roomKey)['id'];
            $this->participantModel->addParticipant($id, $_SESSION['user_id'], 'owner');

            header("Location: ./room?key=$roomKey");
            exit;
        }
    }

    public function deleteRoom()
    {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $roomId = $_POST['room_id'] ?? null;
            if ($roomId) {
                // Verify owner
                $room = $this->roomModel->findByKey($_POST['room_key'] ?? ''); // Wait, getting ID from POST? 
                // Better to fetch room by ID and check owner. 
                // Currently Model only has delete(id).
                // Let's rely on room_key if easier or strict ID.
                // Ideally, we fetch the room first.

                // Let's assume we pass room_id. Ideally we should double check ownership efficiently.
                // Assuming we pass key for consistency in UI routes.
                $key = $_POST['room_key'] ?? '';
                $room = $this->roomModel->findByKey($key);

                if ($room && (int) $room['owner_id'] === (int) $_SESSION['user_id']) {
                    $this->roomModel->delete($room['id']);
                    header('Location: ./dashboard?msg=Room deleted');
                    exit;
                }
            }
            header('Location: ./dashboard?error=Failed to delete room');
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
        $roomKey = $_GET['key'] ?? '';
        $room = $this->roomModel->findByKey($roomKey);

        if (!$room) {
            header('Location: ./dashboard?error=Room not found');
            exit;
        }

        // Public Access Check
        if (!isset($_SESSION['user_id'])) {
            if ($room['is_public']) {
                // Guest Access
                $isGuest = true;
                // Generate a temporary session ident if needed, or handle in JS.
                // For layout.php to not break, we might need dummy user vars?
                // actually layout uses session for navbar.
                // let's pass guest flag to view.
            } else {
                header('Location: ./login');
                exit;
            }
        } else {
            $this->requireAuth(); // Redundant but safe
            $userId = $_SESSION['user_id'];

            // Check if user is participant, if not and room is public, auto-join as viewer?
            // Or just allow access? 
            // If public, we want registered users to also "join" so they appear in list properly?
            // Yes, auto-join for registered users in public rooms is good UX.

            $participant = $this->participantModel->getParticipant($room['id'], $userId);
            if (!$participant) {
                if ($room['is_public']) {
                    $this->participantModel->addParticipant($room['id'], $userId, 'viewer');
                    $participant = $this->participantModel->getParticipant($room['id'], $userId);
                } else {
                    header('Location: ./dashboard?error=You must join the room first');
                    exit;
                }
            }

            if ($participant['status'] === 'banned') {
                header('Location: ./dashboard?error=You are banned from this room');
                exit;
            }
        }

        $view = 'room';
        require __DIR__ . '/../../views/layout.php';
    }

    // API for fetching room details/authenticating socket connection
    public function apiRoomDetails()
    {
        header('Content-Type: application/json');

        $roomKey = $_GET['key'] ?? '';
        $room = $this->roomModel->findByKey($roomKey);

        if (!$room) {
            http_response_code(404);
            echo json_encode(['error' => 'Room not found']);
            exit;
        }

        // Auth Logic
        if (!isset($_SESSION['user_id'])) {
            if ($room['is_public']) {
                // Return Guest Identity
                $guestId = 'guest_' . uniqid(); // This should ideally be consistent per session
                // Use cookie for consistency? For now, random is okay for simple.
                echo json_encode([
                    'room' => $room,
                    'me' => [
                        'id' => 0, // 0 for guest?
                        'username' => 'Guest_' . substr($guestId, -4),
                        'role' => 'viewer',
                        'is_guest' => true
                    ]
                ]);
                exit;
            } else {
                http_response_code(401);
                echo json_encode(['error' => 'Unauthorized']);
                exit;
            }
        }

        // Authenticated User
        $participant = $this->participantModel->getParticipant($room['id'], $_SESSION['user_id']);
        if (!$participant || $participant['status'] === 'banned') {
            // If public and not joined, auto-join? 
            // We handled auto-join in the View route, but for API only access?
            // Let's just deny if not joined, expecting flow to go through View first.
            // OR allow if public.
            if ($room['is_public']) {
                echo json_encode([
                    'room' => $room,
                    'me' => [
                        'id' => $_SESSION['user_id'],
                        'username' => $_SESSION['username'],
                        'role' => 'viewer' // Temporary role if not in DB
                    ]
                ]);
                exit;
            }
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
