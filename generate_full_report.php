<?php
// generate_full_report.php

$baseUrl = 'http://127.0.0.1:8001';
$reportFile = 'test_report.md';
$results = [];

function logResult($name, $status, $details = '')
{
    global $results;
    $results[] = [
        'name' => $name,
        'status' => $status,
        'details' => $details
    ];
    echo "[$status] $name\n";
}

function post($endpoint, $data)
{
    global $baseUrl;
    $ch = curl_init($baseUrl . $endpoint);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $resp = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if ($error)
        return ['status' => 'error', 'message' => $error];
    return json_decode($resp, true) ?? ['status' => 'error', 'message' => 'Invalid JSON: ' . $resp];
}

// --- Test 1: Chunked File Upload ---
$testFile = 'report_test_file.txt';
file_put_contents($testFile, str_repeat('DATA', 1024 * 10)); // 40KB
$content = file_get_contents($testFile);
$size = strlen($content);
$chunkSize = 10 * 1024; // 10KB chunks
$totalChunks = ceil($size / $chunkSize);
$pin = '';

try {
    // 1. Init
    $init = post('/api/upload', ['action' => 'init']);
    if (($init['status'] ?? '') !== 'success')
        throw new Exception("Init failed: " . json_encode($init));
    $uploadId = $init['data']['upload_id'];

    // 2. Chunks
    for ($i = 0; $i < $totalChunks; $i++) {
        $chunk = substr($content, $i * $chunkSize, $chunkSize);
        $resp = post('/api/upload', [
            'action' => 'chunk',
            'upload_id' => $uploadId,
            'chunk_data' => base64_encode($chunk)
        ]);
        if (($resp['status'] ?? '') !== 'success')
            throw new Exception("Chunk $i failed");
    }

    // 3. Complete
    $complete = post('/api/upload', [
        'action' => 'complete',
        'upload_id' => $uploadId,
        'filename' => 'report_test_file.txt'
    ]);
    if (($complete['status'] ?? '') !== 'success')
        throw new Exception("Complete failed");

    $pin = $complete['data']['pin'];
    logResult("Chunked Upload API", "PASS", "PIN: $pin");

} catch (Exception $e) {
    logResult("Chunked Upload API", "FAIL", $e->getMessage());
}

// --- Test 2: File Download ---
if ($pin) {
    try {
        $download = post('/api/receive', ['pin' => $pin]);
        if (($download['status'] ?? '') !== 'success') {
            throw new Exception("Download failed: " . json_encode($download));
        }

        // Verify Content
        $downloadedB64 = $download['data']['content'];
        // Remove data uri prefix if present
        if (strpos($downloadedB64, ',') !== false) {
            $downloadedB64 = explode(',', $downloadedB64)[1];
        }
        $downloadedContent = base64_decode($downloadedB64);

        if ($downloadedContent === $content) {
            logResult("File Download API", "PASS", "Content matches strictly.");
        } else {
            logResult("File Download API", "FAIL", "Content mismatch. Length: " . strlen($downloadedContent));
        }

    } catch (Exception $e) {
        logResult("File Download API", "FAIL", $e->getMessage());
    }
} else {
    logResult("File Download API", "SKIP", "No PIN from upload");
}

// --- Test 3: Text Share & View ---
$textPin = '';
$secretText = "This is a secret message.";
try {
    // Share
    // Text needs to be base64 data uri
    $b64Text = 'data:text/plain;base64,' . base64_encode($secretText);
    $share = post('/api/share-text', ['content' => $b64Text]);

    if (($share['status'] ?? '') !== 'success')
        throw new Exception("Share failed");
    $textPin = $share['data']['pin'];
    logResult("Share Text API", "PASS", "PIN: $textPin");

    // View
    $view = post('/api/view-text', ['pin' => $textPin]);
    if (($view['status'] ?? '') !== 'success')
        throw new Exception("View failed");

    $viewB64 = $view['data']['content'];
    if (strpos($viewB64, ',') !== false) {
        $viewB64 = explode(',', $viewB64)[1];
    }
    $decryptedText = base64_decode($viewB64);

    if ($decryptedText === $secretText) {
        logResult("View Text API", "PASS", "Content matches.");
    } else {
        logResult("View Text API", "FAIL", "Content mismatch.");
    }

} catch (Exception $e) {
    logResult("Text Share/View API", "FAIL", $e->getMessage());
}

// Generate MD
$md = "# API Test Report\n";
$md .= "Date: " . date('Y-m-d H:i:s') . "\n\n";
$md .= "| Test Case | Status | Details |\n";
$md .= "|---|---|---|\n";
foreach ($results as $r) {
    $md .= "| {$r['name']} | **{$r['status']}** | {$r['details']} |\n";
}

file_put_contents($reportFile, $md);
echo "\nReport generated at $reportFile\n";
unlink($testFile);
