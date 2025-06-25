<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>tabu – Nachricht vorbereiten</title>
    <style>
        body {
            font-family: system-ui, sans-serif;
            max-width: 600px;
            margin: 2em auto;
            padding: 1em;
            background: #f9f9f9;
        }
        form {
            background: #fff;
            border-radius: 8px;
            padding: 1.5em;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        label {
            display: block;
            margin-top: 1.2em;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        input[type="file"] {
            margin-top: 0.3em;
            width: 100%;
            padding: 0.5em;
            font-size: 1em;
        }
        .file-entry {
            margin-bottom: 0.5em;
        }
        .remove-btn {
            margin-left: 0.5em;
            color: red;
            font-size: 0.9em;
            cursor: pointer;
        }
        button {
            margin-top: 1.5em;
            background: #2f76d2;
            color: white;
            font-size: 1.1em;
            padding: 0.6em 1.2em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #205ea6;
        }
    </style>
    <script>
        function addFileInput() {
            const container = document.getElementById('file-inputs');
            const div = document.createElement('div');
            div.className = 'file-entry';

            const input = document.createElement('input');
            input.type = 'file';
            input.name = 'uploads[]';
            input.multiple = true;
            input.required = true;

            const remove = document.createElement('span');
            remove.className = 'remove-btn';
            remove.textContent = '🗑️ entfernen';
            remove.onclick = () => container.removeChild(div);

            div.appendChild(input);
            div.appendChild(remove);
            container.appendChild(div);
        }

        function updateEquationFields() {
            const container = document.getElementById('equation-fields');
            const count = parseInt(document.getElementById('equation_count').value) || 0;
            container.innerHTML = '';
            for (let i = 0; i < count; i++) {
                const label = document.createElement('label');
                label.textContent = `Gleichung ${i + 1} – Beschreibung:`;

                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'equation_texts[]';
                input.required = true;

                container.appendChild(label);
                container.appendChild(input);
            }
        }

        window.onload = () => {
            addFileInput(); // mindestens ein Feld anzeigen
        };
    </script>
</head>
<body>

    <h1>tabu – Nachricht vorbereiten</h1>

    <form method="POST" action="upload.php" enctype="multipart/form-data">
        <label>Dateien auswählen:</label>
        <div id="file-inputs"></div>
        <button type="button" onclick="addFileInput()">➕ Weitere Datei(en) hinzufügen</button>

        <label>Anzahl der vorgeschalteten Gleichungen:</label>
        <input type="number" id="equation_count" name="equation_count" min="1" max="10" required onchange="updateEquationFields()">

        <div id="equation-fields"></div>

        <button type="submit">Upload starten</button>
    </form>

</body>
</html>
