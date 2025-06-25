<?php
function generateKey($input) {
    return hash('sha256', implode('-', $input));
}

function unlockMessage($key) {
    $vault = json_decode(file_get_contents(__DIR__ . '/../data/vault.json'), true);
    return $vault[$key] ?? null;
}
