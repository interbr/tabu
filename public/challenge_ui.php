<?php
$uploadId = $_GET['upload'] ?? '';
$stage = intval($_GET['stage'] ?? 1);

// Lade manifest
$manifestPath = __DIR__ . "/../uploads/{$uploadId}/manifest.json";
$data = json_decode(file_get_contents($manifestPath), true);
$stages = $data['stages'] ?? [];
$totalStages = count($stages);

if ($stage < 1 || $stage > $totalStages) {
    die("⚠️ Ungültige Stufe.");
}

$challenge = $stages[$stage - 1];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>tabu – Stufe <?= $stage ?>/<?= $totalStages ?></title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; padding: 2em; }
        .stage-info { background: #eef; padding: 1em; border-radius: 5px; }
        .progress { margin-top: 1em; }
        .correct { color: green; font-weight: bold; }
        .wrong { color: red; }
        input[type=number] { width: 60px; }
        select { width: 50px; }
    </style>
</head>
<body>
    <h2>tabu – Stufe <?= $stage ?> von <?= $totalStages ?></h2>
    <div class="stage-info">
        <p><strong>Beschreibung:</strong> <?= htmlspecialchars($challenge['description']) ?></p>
        <p><strong>Eingabe:</strong> <?= implode(' + ', $challenge['input']) ?></p>
        <p><strong>Ziel:</strong> <?= implode(' + ', $challenge['output']) ?></p>
    </div>

    <form id="challengeForm">
        <?php foreach ($challenge['input'] as $item):
            if (preg_match('/^(\d+)\s+(.+)$/u', $item, $m)) {
                $label = trim($m[2]);
            } elseif (is_numeric($item)) {
                $label = 'Zahl';
            } else {
                continue;
            }
        ?>
        <p>
            <?= htmlspecialchars($label) ?>:
            <select name="ops[<?= htmlspecialchars($label) ?>][op]">
                <option value="+">+</option>
                <option value="*">×</option>
            </select>
            <input type="number" name="ops[<?= htmlspecialchars($label) ?>][value]" step="0.1" required>
        </p>
        <?php endforeach; ?>

        <input type="hidden" name="upload_id" value="<?= htmlspecialchars($uploadId) ?>">
        <input type="hidden" name="stage" value="<?= $stage ?>">
        <button type="submit">Prüfen</button>
    </form>

    <div class="progress">
        <strong>Fortschritt:</strong>
        <?php for ($i = 1; $i <= $totalStages; $i++): ?>
            <?= $i == $stage ? "<strong>[$i]</strong>" : $i ?>
        <?php endfor; ?>
    </div>

    <div id="result" style="margin-top: 1em;"></div>

    <script>
    document.getElementById("challengeForm").addEventListener("submit", function(e) {
        e.preventDefault();
        const form = e.target;
        const data = new FormData(form);
        const resultBox = document.getElementById("result");

        fetch("check_transformation.php", {
            method: "POST",
            body: data
        })
        .then(res => res.json())
        .then(json => {
            if (json.correct) {
                <?php if ($stage < $totalStages): ?>
                    resultBox.innerHTML = '<p class="correct">✅ Richtig! Weiter zur nächsten Stufe...</p>';
                    setTimeout(() => {
                        window.location.href = "challenge_ui.php?upload=<?= $uploadId ?>&stage=<?= $stage + 1 ?>";
                    }, 1000);
                <?php else: ?>
                    resultBox.innerHTML = '<p class="correct">✅ Richtig! Lade finale Belohnung...</p>';
                    fetch('proxy_manifest.php?upload=<?= $uploadId ?>')
                        .then(response => {
                            if (!response.ok) {
                                throw new Error("manifest.json nicht gefunden");
                            }
                            return response.json();
                        })
                        .then(data => {
                            let html = '<div class="correct"><p><strong>🎉 Alle Stufen gelöst!</strong></p>';
                            if (data.files && data.files.length > 0) {
                                html += '<h3>📂 Freigegebene Dateien:</h3><ul>';
                                data.files.forEach(file => {
                                    const url = `proxy_file.php?upload=<?= $uploadId ?>&file=` + encodeURIComponent(file);
                                    html += `<li><a href="${url}" target="_blank">${file}</a></li>`;
                                });
                                html += '</ul>';
                            } else {
                                html += '<p>✅ Gelöst, aber keine Dateien gefunden.</p>';
                            }
                            html += '</div>';
                            resultBox.innerHTML = html;
                        })
                        .catch(error => {
                            resultBox.innerHTML = `<p class="wrong">⚠️ Fehler bei der Freigabe: ${error.message}</p>`;
                        });
                <?php endif; ?>
            } else {
                resultBox.innerHTML = '<p class="wrong">❌ Leider falsch. Versuche es erneut.</p>';
            }
        });
    });
    </script>
</body>
</html>
