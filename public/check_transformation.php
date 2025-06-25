<?php
$uploadId = $_POST['upload_id'] ?? '';
$stageNum = intval($_POST['stage'] ?? 1);
$userOps = $_POST['ops'] ?? [];

$manifestPath = __DIR__ . "/../uploads/$uploadId/manifest.json";
if (!file_exists($manifestPath)) {
    echo json_encode(['correct' => false, 'error' => 'manifest.json fehlt']);
    exit;
}

$data = json_decode(file_get_contents($manifestPath), true);
$stage = $data['stages'][$stageNum - 1] ?? null;
if (!$stage) {
    echo json_encode(['correct' => false, 'error' => 'Stufe nicht gefunden']);
    exit;
}

function parseItem($value) {
    if (preg_match('/^(\d+)\s+(.+)$/u', $value, $matches)) {
        return ['amount' => (int)$matches[1], 'label' => trim($matches[2])];
    } elseif (is_numeric($value)) {
        return ['amount' => (int)$value, 'label' => 'Zahl'];
    }
    return null;
}

$inputParsed = array_map('parseItem', $stage['input']);
$outputParsed = array_map('parseItem', $stage['output']);
$correct = true;

foreach ($inputParsed as $i => $inputItem) {
    $label = $inputItem['label'];
    $inputAmount = $inputItem['amount'];
    $targetAmount = $outputParsed[$i]['amount'] ?? null;

    $userOp = $userOps[$label] ?? null;
    if (!$userOp || !isset($userOp['op'], $userOp['value'])) {
        $correct = false;
        break;
    }

    $val = floatval($userOp['value']);
    $result = match ($userOp['op']) {
        '+' => $inputAmount + $val,
        '*' => $inputAmount * $val,
        default => null
    };

    if ($result === null || abs($result - $targetAmount) > 0.01) {
        $correct = false;
        break;
    }
}

echo json_encode(['correct' => $correct]);
