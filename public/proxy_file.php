<?php
$uploadId = $_GET['upload'] ?? '';
$file = $_GET['file'] ?? '';

if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $uploadId) || !preg_match('/^[^\/]+$/', $file)) {
    http_response_code(400);
    echo "Ungültiger Zugriff.";
    exit;
}

$path = __DIR__ . "/../uploads/{$uploadId}/{$file}";

if (!file_exists($path)) {
    http_response_code(404);
    echo "Datei nicht gefunden.";
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $path);
finfo_close($finfo);

header("Content-Type: $mime");
header("Content-Disposition: inline; filename=\"" . basename($file) . "\"");
readfile($path);
