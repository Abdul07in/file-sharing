<?php

namespace App\Service;

use App\Config\Config;
use Exception;

class EncryptionService
{
    /**
     * Encrypts data using AES-256-CBC.
     * Returns an array with ['data' => encryptedString, 'iv' => hexIv]
     */
    public function encrypt(string $data): array
    {
        $ivLength = openssl_cipher_iv_length(Config::$CIPHER_METHOD);
        $iv = openssl_random_pseudo_bytes($ivLength);

        if ($iv === false) {
            throw new Exception("Failed to generate IV");
        }

        $encrypted = openssl_encrypt($data, Config::$CIPHER_METHOD, Config::$ENCRYPTION_KEY, 0, $iv);

        if ($encrypted === false) {
            throw new Exception("Encryption failed");
        }

        return [
            'data' => $encrypted,
            'iv' => bin2hex($iv)
        ];
    }

    /**
     * Decrypts data using AES-256-CBC.
     */
    public function decrypt(string $encryptedData, string $hexIv): string
    {
        $iv = hex2bin($hexIv);

        $decrypted = openssl_decrypt($encryptedData, Config::$CIPHER_METHOD, Config::$ENCRYPTION_KEY, 0, $iv);

        if ($decrypted === false) {
            throw new Exception("Decryption failed");
        }

        return $decrypted;
    }
}
