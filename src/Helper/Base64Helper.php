<?php

namespace App\Helper;

class Base64Helper
{
    /**
     * Decodes a base64 string safely.
     * Expects input like "data:image/png;base64,....." or just raw base64.
     */
    public static function decode(string $input): array
    {
        // Check if it has a data URI scheme
        if (preg_match('/^data:([a-zA-Z0-9]+\/[a-zA-Z0-9-.+]+);base64,/', $input, $matches)) {
            $mimeType = $matches[1];
            $base64 = substr($input, strpos($input, ',') + 1);
        } else {
            $mimeType = 'application/octet-stream';
            $base64 = $input;
        }

        $decoded = base64_decode($base64, true);

        if ($decoded === false) {
            throw new \Exception("Invalid Base64 encoded data.");
        }

        return [
            'data' => $decoded,
            'mime_type' => $mimeType
        ];
    }

    public static function encode(string $data, string $mimeType = 'application/octet-stream'): string
    {
        return 'data:' . $mimeType . ';base64,' . base64_encode($data);
    }
}
