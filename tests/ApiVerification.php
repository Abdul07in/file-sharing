<?php

/**
 * Custom API Verification Suite for File Sharing App
 * Usage: php tests/ApiVerification.php
 */

class ApiTester
{
    private string $baseUrl;
    private array $results = [];

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    public function log(string $message, string $type = 'INFO')
    {
        $timestamp = date('H:i:s');
        echo "[$timestamp] [$type] $message" . PHP_EOL;
    }

    private function request(string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . $endpoint;
        $ch = curl_init($url);

        $payload = json_encode($data);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $this->log("Curl Error: " . curl_error($ch), 'ERROR');
            return ['status' => 'error', 'http_code' => 0];
        }

        curl_close($ch);

        $decoded = json_decode($response, true);

        $this->lastResponse = [
            'http_code' => $httpCode,
            'body' => $decoded,
            'raw_body' => $response
        ];

        return $this->lastResponse;
    }

    private $lastResponse = [];

    public function assert(bool $condition, string $message)
    {
        if ($condition) {
            $this->log("PASS: $message", 'SUCCESS');
            $this->results[] = ['status' => 'PASS', 'message' => $message];
        } else {
            $this->log("FAIL: $message", 'FAIL');
            if (!empty($this->lastResponse)) {
                $this->log("Response Body: " . substr($this->lastResponse['raw_body'], 0, 500), 'FAIL');
            }
            $this->results[] = ['status' => 'FAIL', 'message' => $message];
            // Don't exit on fail, keep running other tests
        }
    }

    public function run()
    {
        $this->log("Starting API Verification Tests against " . $this->baseUrl);

        // --- Test 1: File Upload ---
        $this->log("--- Test Case 1: File Upload ---");
        $filename = "test_file.txt";
        $content = "Hello World Enterprise Testing";
        $base64Content = base64_encode($content); // Raw base64, helper handles inputs with or without data uri

        $uploadResponse = $this->request('/api/upload', [
            'filename' => $filename,
            'content' => $base64Content
        ]);

        $uploadSuccess = ($uploadResponse['http_code'] === 200 && ($uploadResponse['body']['status'] ?? '') === 'success');
        $this->assert($uploadSuccess, "File Upload endpoint returns 200 OK and success status");

        $pin = $uploadResponse['body']['data']['pin'] ?? null;
        $this->assert(!empty($pin), "File Upload should return a PIN");

        if (!$pin) {
            $this->log("Skipping Download test due to missing PIN", 'WARN');
        } else {
            // --- Test 2: File Download ---
            $this->log("--- Test Case 2: File Download ---");
            $downloadResponse = $this->request('/api/receive', [
                'pin' => $pin
            ]);

            $downloadSuccess = ($downloadResponse['http_code'] === 200 && ($downloadResponse['body']['status'] ?? '') === 'success');
            $this->assert($downloadSuccess, "File Download endpoint returns 200 OK and success status");

            $receivedBase64 = $downloadResponse['body']['data']['content'] ?? '';

            // Helper might return data URI, strip if needed for comparison
            if (strpos($receivedBase64, 'base64,') !== false) {
                $receivedBase64 = explode('base64,', $receivedBase64)[1];
            }

            $receivedContent = base64_decode($receivedBase64);
            $this->assert($receivedContent === $content, "Downloaded content must match original content exactly");

            $receivedFilename = $downloadResponse['body']['data']['filename'] ?? '';
            $this->assert($receivedFilename === $filename, "Downloaded filename must match original filename");

            // --- Test 3: Burn on Read (File) ---
            $this->log("--- Test Case 3: File Burn-on-Read ---");
            $retryResponse = $this->request('/api/receive', ['pin' => $pin]);
            $isGone = ($retryResponse['http_code'] !== 200 || ($retryResponse['body']['status'] ?? '') === 'error');
            $this->assert($isGone, "File should not be retrievable a second time");
        }

        // --- Test 4: Text Share ---
        $this->log("--- Test Case 4: Text Share ---");
        $textContent = "Secret Password 123";
        $textBase64 = base64_encode($textContent);

        $shareResponse = $this->request('/api/share-text', [
            'content' => $textBase64
        ]);

        $shareSuccess = ($shareResponse['http_code'] === 200 && ($shareResponse['body']['status'] ?? '') === 'success');
        $this->assert($shareSuccess, "Text Share endpoint returns 200 OK");

        $textPin = $shareResponse['body']['data']['pin'] ?? null;
        $this->assert(!empty($textPin), "Text Share should return a PIN");

        if ($textPin) {
            // --- Test 5: Text View ---
            $this->log("--- Test Case 5: Text View ---");
            $viewResponse = $this->request('/api/view-text', [
                'pin' => $textPin
            ]);

            $viewSuccess = ($viewResponse['http_code'] === 200 && ($viewResponse['body']['status'] ?? '') === 'success');
            $this->assert($viewSuccess, "Text View endpoint returns 200 OK");

            $receivedTextBase64 = $viewResponse['body']['data']['content'] ?? '';
            if (strpos($receivedTextBase64, 'base64,') !== false) {
                $receivedTextBase64 = explode('base64,', $receivedTextBase64)[1];
            }
            $receivedText = base64_decode($receivedTextBase64);
            $this->assert($receivedText === $textContent, "Retrieved text must match original text");

            // --- Test 6: Burn on Read (Text) ---
            $this->log("--- Test Case 6: Text Burn-on-Read ---");
            $retryTextResponse = $this->request('/api/view-text', ['pin' => $textPin]);
            $textGone = ($retryTextResponse['http_code'] !== 200 || ($retryTextResponse['body']['status'] ?? '') === 'error');
            $this->assert($textGone, "Text should not be retrievable a second time");
        }

        // --- Summary ---
        $this->printSummary();
    }

    private function printSummary()
    {
        $total = count($this->results);
        $passed = count(array_filter($this->results, fn($r) => $r['status'] === 'PASS'));
        $failed = $total - $passed;

        echo PHP_EOL . "=== TEST SUMMARY ===" . PHP_EOL;
        echo "Total Tests: $total" . PHP_EOL;
        echo "Passed: $passed" . PHP_EOL;
        echo "Failed: $failed" . PHP_EOL;

        $reportFile = __DIR__ . '/../test_report.md';
        $reportContent = "# Test Report\n\nDate: " . date('Y-m-d H:i:s') . "\n\n";
        $reportContent .= "| Status | Message |\n|---|---|\n";
        foreach ($this->results as $res) {
            $icon = $res['status'] === 'PASS' ? '✅' : '❌';
            $reportContent .= "| $icon {$res['status']} | {$res['message']} |\n";
        }
        $reportContent .= "\n**Summary**: $passed passed, $failed failed.";

        file_put_contents($reportFile, $reportContent);
        echo "Report generated at $reportFile" . PHP_EOL;

        if ($failed > 0)
            exit(1);
    }
}

// Check if running from CLI
if (php_sapi_name() !== 'cli') {
    die('This script must be run from the command line.');
}

$port = 8888;
$baseUrl = "http://localhost:$port";

// Check if server is running, if not, we assume the user of this script (the agent) handled it or we can try to check connectivity
// Simple check
$headers = @get_headers($baseUrl);
if (!$headers || strpos($headers[0], '200') === false) {
    // Try to rely on the fact that if we just started it, it might take a ms. 
    // Ideally the runner spawns the server, but for simplicity we'll assume it's running or we just check connectivity failure inside tests.
    echo "Warning: Server might not be reachable at $baseUrl. Ensure 'php -S localhost:$port' is running." . PHP_EOL;
}

$tester = new ApiTester($baseUrl);
$tester->run();
