<?php
$uploadId = $_GET['upload'] ?? '';
$manifestPath = __DIR__ . "/../uploads/{$uploadId}/manifest.json";

if (!preg_match('/^[a-zA-Z0-9_\.\-]+$/', $uploadId)) {
    http_response_code(400);
    echo "Ungültige Upload-ID.";
    exit;
}

if (!file_exists($manifestPath)) {
    http_response_code(404);
    echo "manifest.json nicht gefunden.";
    exit;
}

header('Content-Type: application/json');
readfile($manifestPath);
