<?php

namespace App\Model;

use PDO;

class RoomParticipant
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function addParticipant(int $roomId, int $userId, string $role = 'viewer'): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO room_participants (room_id, user_id, role) 
            VALUES (:room_id, :user_id, :role)
            ON DUPLICATE KEY UPDATE role = :role, status = 'active'
        ");
        $stmt->execute([
            'room_id' => $roomId,
            'user_id' => $userId,
            'role' => $role
        ]);
    }

    public function getParticipants(int $roomId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT u.id, u.username, rp.role, rp.status, rp.created_at
            FROM room_participants rp
            JOIN users u ON rp.user_id = u.id
            WHERE rp.room_id = :room_id
        ");
        $stmt->execute(['room_id' => $roomId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getParticipant(int $roomId, int $userId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT * FROM room_participants 
            WHERE room_id = :room_id AND user_id = :user_id
        ");
        $stmt->execute(['room_id' => $roomId, 'user_id' => $userId]);
        $participant = $stmt->fetch(PDO::FETCH_ASSOC);

        return $participant ?: null;
    }

    public function updateStatus(int $roomId, int $userId, string $status): void
    {
        $stmt = $this->pdo->prepare("
            UPDATE room_participants 
            SET status = :status 
            WHERE room_id = :room_id AND user_id = :user_id
        ");
        $stmt->execute([
            'status' => $status,
            'room_id' => $roomId,
            'user_id' => $userId
        ]);
    }

    public function removeParticipant(int $roomId, int $userId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM room_participants WHERE room_id = :room_id AND user_id = :user_id");
        $stmt->execute(['room_id' => $roomId, 'user_id' => $userId]);
    }

    public function getUserRooms(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT r.*, rp.role 
            FROM room_participants rp
            JOIN rooms r ON rp.room_id = r.id
            WHERE rp.user_id = :user_id AND rp.role != 'owner'
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
