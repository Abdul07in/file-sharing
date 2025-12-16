<?php
// Verify Chunked Upload
$baseUrl = 'http://localhost:8000/api/upload';
$file = 'test-large-file.txt';

// Ensure file exists (if fsutil failed or async issue, create it here)
if (!file_exists($file)) {
    file_put_contents($file, str_repeat('A', 2 * 1024 * 1024)); // 2MB
}

$content = file_get_contents($file);
$size = strlen($content);
$chunkSize = 512 * 1024;
$totalChunks = ceil($size / $chunkSize);

echo "Uploading $size bytes in $totalChunks chunks...\n";

// 1. Init
$ch = curl_init($baseUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['action' => 'init']));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$resp = curl_exec($ch);
curl_close($ch);

echo "Init Response: $resp\n";
$json = json_decode($resp, true);
if (($json['status'] ?? '') !== 'success')
    die("Init failed\n");
$uploadId = $json['data']['upload_id'];
echo "Upload ID: $uploadId\n";

// 2. Chunks
for ($i = 0; $i < $totalChunks; $i++) {
    $chunk = substr($content, $i * $chunkSize, $chunkSize);
    $b64 = base64_encode($chunk);

    $ch = curl_init($baseUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
        'action' => 'chunk',
        'upload_id' => $uploadId,
        'chunk_data' => $b64
    ]));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $resp = curl_exec($ch);
    curl_close($ch);

    $json = json_decode($resp, true);
    if (($json['status'] ?? '') !== 'success')
        die("Chunk $i failed: $resp\n");
    echo "Chunk $i uploaded.\n";
}

// 3. Complete
$ch = curl_init($baseUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'action' => 'complete',
    'upload_id' => $uploadId,
    'filename' => 'chunked_test.txt'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
$resp = curl_exec($ch);
curl_close($ch);

echo "Complete Response: $resp\n";
$json = json_decode($resp, true);
if (($json['status'] ?? '') !== 'success')
    die("Complete failed\n");

echo "Success! PIN: " . $json['data']['pin'] . "\n";
?>