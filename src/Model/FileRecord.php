<?php

namespace App\Model;

use App\Config\Database;
use PDO;

class FileRecord implements PinRepositoryInterface
{
    private ?PDO $pdo = null;
    private bool $useJson = false;
    private string $jsonPath;

    public function __construct()
    {
        $this->jsonPath = __DIR__ . '/../../db.json';
        try {
            $this->pdo = Database::connect();
        } catch (\Exception $e) {
            // Fallback to JSON if DB fails
            $this->useJson = true;
            if (!file_exists($this->jsonPath)) {
                file_put_contents($this->jsonPath, json_encode([]));
            }
        }
    }

    public function save(string $pin, string $fileName, string $content, string $iv): bool
    {
        if ($this->useJson) {
            throw new \Exception("Database connection failed. Cannot save chunks to JSON.");
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO uploads (file_pin, file_name, file_content, encryption_iv, storage_path)
            VALUES (:pin, :name, :content, :iv, 'db')
        ");

        $stmt->bindParam(':pin', $pin);
        $stmt->bindParam(':name', $fileName);
        $stmt->bindParam(':content', $content, PDO::PARAM_LOB);
        $stmt->bindParam(':iv', $iv);

        return $stmt->execute();
    }

    public function findByPin(string $pin): ?array
    {
        if ($this->useJson) {
            return null; // Not supported
        }

        // Do not fetch content here to save memory, only metadata
        $stmt = $this->pdo->prepare("SELECT id, file_pin, file_name, encryption_iv, created_at FROM uploads WHERE file_pin = :pin LIMIT 1");
        $stmt->execute([':pin' => $pin]);

        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function getContent(string $pin): ?string
    {
        $stmt = $this->pdo->prepare("SELECT file_content FROM uploads WHERE file_pin = :pin LIMIT 1");
        $stmt->execute([':pin' => $pin]);
        $stmt->bindColumn(1, $content, PDO::PARAM_LOB);
        $stmt->fetch(PDO::FETCH_BOUND);

        if (is_resource($content)) {
            return stream_get_contents($content);
        }

        return $content;
    }

    public function delete(string $idOrPin): bool
    {
        if ($this->useJson) {
            return false;
        }

        $stmt = $this->pdo->prepare("DELETE FROM uploads WHERE id = :id OR file_pin = :pin");
        return $stmt->execute([':id' => $idOrPin, ':pin' => $idOrPin]);
    }
}
