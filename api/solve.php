<?php
require_once '../logic/challenge.php';
require_once '../utils/security.php';

$input = [
    (int) ($_POST['value1'] ?? 0),
    (int) ($_POST['value2'] ?? 0),
    (int) ($_POST['value3'] ?? 0)
];

$challenge = getCurrentChallenge();
$solved = $challenge['solution']($input);

if ($solved) {
    $key = generateKey($input);
    $message = unlockMessage($key);
    echo json_encode([
        "status" => "ok",
        "unlocked" => $message ?? "Keine Nachricht – Zugang korrekt, aber leer."
    ]);
} else {
    echo json_encode(["status" => "error", "hint" => "Falsch. Versuche es erneut."]);
}
