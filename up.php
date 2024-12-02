<?php
// Konfigurace
$maxFileSize = 50 * 1024 * 1024; // 50MB v bajtech
$uploadDir = "./"; // Současný adresář
$allowedTypes = array(
    "jpg" => "image/jpeg",
    "jpeg" => "image/jpeg",
    "png" => "image/png",
    "gif" => "image/gif",
    "pdf" => "application/pdf",
    "zip" => "application/zip"
);

// Funkce pro bezpečné generování názvu souboru
function generateSafeFileName($originalName, $extension) {
    return uniqid() . '_' . preg_replace("/[^a-zA-Z0-9]/", "", pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
}

if(isset($_POST["submit"])) {
    if(isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) {
        $file = $_FILES["fileToUpload"];
        
        // Kontrola velikosti souboru
        if ($file["size"] > $maxFileSize) {
            echo "<div class='error'>Soubor je příliš velký. Maximální povolená velikost je 50MB.</div>";
            exit;
        }

        // Získání skutečného MIME typu
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file["tmp_name"]);
        finfo_close($finfo);

        // Kontrola typu souboru
        $fileExt = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        if (!array_key_exists($fileExt, $allowedTypes) || $allowedTypes[$fileExt] !== $mimeType) {
            echo "<div class='error'>Nepovolený typ souboru. Povolené typy jsou: JPG, JPEG, PNG, GIF, PDF a ZIP.</div>";
            exit;
        }

        // Generování bezpečného názvu souboru
        $newFileName = generateSafeFileName($file["name"], $fileExt);
        $targetPath = $uploadDir . $newFileName;

        // Přesunutí souboru
        if (move_uploaded_file($file["tmp_name"], $targetPath)) {
            chmod($targetPath, 0644);
            
            echo "<div class='success'>Soubor byl úspěšně nahrán!</div>";
            echo "<div class='file-info'>";
            echo "<p><strong>Název souboru:</strong> " . htmlspecialchars($newFileName) . "</p>";
            echo "<p><strong>Typ souboru:</strong> " . htmlspecialchars($mimeType) . "</p>";
            echo "<p><strong>Velikost:</strong> " . round($file["size"] / 1024, 2) . " KB</p>";
            echo "</div>";
        } else {
            echo "<div class='error'>Došlo k chybě při nahrávání souboru.</div>";
        }
    } else {
        echo "<div class='error'>Došlo k chybě: " . $_FILES["fileToUpload"]["error"] . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Souboru</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .drop-zone {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            cursor: pointer;
        }
        .drop-zone.dragover {
            background-color: #e1e1e1;
            border-color: #999;
        }
        .preview {
            max-width: 200px;
            display: none;
            margin: 10px auto;
        }
        .error {
            color: red;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid red;
            background-color: #ffe6e6;
        }
        .success {
            color: green;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid green;
            background-color: #e6ffe6;
        }
        .file-info {
            background-color: #f5f5f5;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <h2>Nahrát Soubor</h2>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="drop-zone" id="dropZone">
            <p>Přetáhněte soubor sem nebo klikněte pro výběr</p>
            <input type="file" name="fileToUpload" id="fileToUpload" style="display: none;">
            <img id="preview" class="preview">
        </div>
        <input type="submit" value="Nahrát" name="submit">
    </form>

    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileToUpload');
        const preview = document.getElementById('preview');

        // Kliknutí na drop zónu
        dropZone.addEventListener('click', () => fileInput.click());

        // Drag and drop události
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            fileInput.files = e.dataTransfer.files;
            showPreview();
        });

        // Změna souboru
        fileInput.addEventListener('change', showPreview);

        // Funkce pro zobrazení náhledu
        function showPreview() {
            const file = fileInput.files[0];
            if (file) {
                const maxSize = 50 * 1024 * 1024; // 50MB
                if (file.size > maxSize) {
                    alert('Soubor je příliš velký! Maximální povolená velikost je 50MB.');
                    fileInput.value = '';
                    preview.style.display = 'none';
                    dropZone.querySelector('p').textContent = 'Přetáhněte soubor sem nebo klikněte pro výběr';
                    return;
                }

                dropZone.querySelector('p').textContent = file.name;

                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>
