<?php

namespace App\Model;

use App\Config\Database;
use PDO;

class TextRecord implements PinRepositoryInterface
{
    private ?PDO $pdo = null;

    public function __construct()
    {
        $this->pdo = Database::connect();
    }

    public function save(string $pin, string $content, string $iv): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO text_shares (pin, content, iv)
            VALUES (:pin, :content, :iv)
        ");

        $stmt->bindParam(':pin', $pin);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':iv', $iv);

        return $stmt->execute();
    }

    public function findByPin(string $pin): ?array
    {
        $stmt = $this->pdo->prepare("SELECT id, pin, iv, created_at FROM text_shares WHERE pin = :pin LIMIT 1");
        $stmt->execute([':pin' => $pin]);

        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getContent(string $pin): ?string
    {
        $stmt = $this->pdo->prepare("SELECT content FROM text_shares WHERE pin = :pin LIMIT 1");
        $stmt->execute([':pin' => $pin]);

        $result = $stmt->fetchColumn();
        return $result ?: null;
    }

    public function delete(string $pin): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM text_shares WHERE pin = :pin");
        return $stmt->execute([':pin' => $pin]);
    }
}
