<?php

namespace App\Model;

use PDO;
use RuntimeException;

class Room
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function create(int $ownerId, string $name): string
    {
        $roomKey = $this->generateRoomKey();

        $stmt = $this->pdo->prepare("INSERT INTO rooms (room_key, owner_id, name) VALUES (:room_key, :owner_id, :name)");
        $stmt->execute([
            'room_key' => $roomKey,
            'owner_id' => $ownerId,
            'name' => $name
        ]);

        return $roomKey;
    }

    public function findByKey(string $roomKey): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM rooms WHERE room_key = :room_key");
        $stmt->execute(['room_key' => $roomKey]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        return $room ?: null;
    }

    public function getRoomsByOwner(int $ownerId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM rooms WHERE owner_id = :owner_id ORDER BY created_at DESC");
        $stmt->execute(['owner_id' => $ownerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete(int $roomId): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM rooms WHERE id = :id");
        $stmt->execute(['id' => $roomId]);
    }

    private function generateRoomKey(int $length = 10): string
    {
        return substr(bin2hex(random_bytes($length)), 0, $length);
    }
}
