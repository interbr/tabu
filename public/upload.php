<?php
date_default_timezone_set('Europe/Berlin');

// Zielverzeichnis vorbereiten
$baseDir = __DIR__ . '/../uploads/';
$uploadId = uniqid('tabu_', true);
$targetDir = $baseDir . $uploadId;

if (!mkdir($targetDir, 0777, true)) {
    die("❌ Fehler beim Erstellen des Upload-Ordners.");
}

// Dateien speichern
$savedFiles = [];

if (isset($_FILES['uploads'])) {
    for ($i = 0; $i < count($_FILES['uploads']['name']); $i++) {
        $filename = $_FILES['uploads']['name'][$i];
        $tmpName  = $_FILES['uploads']['tmp_name'][$i];

        if (!empty($tmpName) && is_uploaded_file($tmpName)) {
            $target = $targetDir . '/' . basename($filename);
            if (move_uploaded_file($tmpName, $target)) {
                $savedFiles[] = $filename;
            }
        }
    }
}


// Challenge-Stufen generieren
require_once '../logic/semantic_challenge.php';

$equationCount = intval($_POST['equation_count'] ?? 0);
$equationTexts = $_POST['equation_texts'] ?? [];
$stages = [];

for ($i = 0; $i < $equationCount; $i++) {
    $desc = trim($equationTexts[$i] ?? 'Stufe ' . ($i + 1));
    $challenge = generateSemanticChallenge($i + 1);

    $stages[] = [
        'stage' => $i + 1,
        'description' => $desc,
        'input' => $challenge['input'],
        'output' => $challenge['output']
    ];
}

// Manifest schreiben
$metadata = [
    'upload_id' => $uploadId,
    'timestamp' => date('c'),
    'files' => $savedFiles,
    'stages' => $stages
];

file_put_contents("$targetDir/manifest.json", json_encode($metadata, JSON_PRETTY_PRINT));

$challengeLink = "challenge_ui.php?upload=" . urlencode($uploadId) . "&stage=1";

echo <<<HTML
✅ Upload erfolgreich!<br>
<strong>Upload-ID:</strong> $uploadId<br>
<a href="$challengeLink">➡️ Zur ersten Challenge-Stufe</a>
HTML;
