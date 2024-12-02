<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Moderní File Upload</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }

        body {
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
        }

        h2 {
            color: #333;
            margin-bottom: 25px;
            text-align: center;
        }

        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .upload-area:hover {
            border-color: #2196F3;
            background: #f8f9fa;
        }

        .upload-area p {
            color: #666;
            margin: 10px 0;
        }

        input[type="file"] {
            display: none;
        }

        .upload-btn {
            background: #2196F3;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s ease;
            width: 100%;
            margin-top: 15px;
        }

        .upload-btn:hover {
            background: #1976D2;
        }

        .success {
            color: #4CAF50;
            padding: 15px;
            border-radius: 5px;
            background: #E8F5E9;
            margin: 20px 0;
        }

        .error {
            color: #f44336;
            padding: 15px;
            border-radius: 5px;
            background: #FFEBEE;
            margin: 20px 0;
        }

        .file-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .file-info p {
            margin: 5px 0;
            color: #666;
        }

        .allowed-types {
            font-size: 14px;
            color: #666;
            margin-top: 10px;
            text-align: center;
        }

        #preview {
            margin-top: 20px;
            max-width: 100%;
            display: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Nahrát soubor</h2>

        <form action="" method="POST" enctype="multipart/form-data" id="uploadForm">
            <div class="upload-area" id="dropZone">
                <p>Přetáhněte soubor sem nebo klikněte pro výběr</p>
                <input type="file" name="fileToUpload" id="fileToUpload" required>
            </div>
            <button type="submit" name="submit" class="upload-btn">Nahrát soubor</button>
        </form>
        <p class="allowed-types">Povolené formáty: JPG, JPEG, PNG, GIF, PDF, ZIP</p>
        <img id="preview" alt="Náhled">

        <?php
        if(isset($_POST["submit"])) {
            if(isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) {
                $targetDir = "./uploads/";
                
                if (!file_exists($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                
                $fileName = basename($_FILES["fileToUpload"]["name"]);
                $targetPath = $targetDir . $fileName;
                $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
                
                // Přidány PDF a ZIP do povolených typů
                $allowedTypes = array("jpg", "jpeg", "png", "gif", "pdf", "zip");
                
                if (!in_array($fileType, $allowedTypes)) {
                    echo "<div class='error'>Pouze soubory typu JPG, JPEG, PNG, GIF, PDF a ZIP jsou povoleny.</div>";
                } else {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetPath)) {
                        echo "<div class='success'>Soubor byl úspěšně nahrán!</div>";
                        echo "<div class='file-info'>";
                        echo "<p><strong>Umístění souboru:</strong> " . realpath($targetPath) . "</p>";
                        echo "<p><strong>Název souboru:</strong> " . $fileName . "</p>";
                        echo "<p><strong>Typ souboru:</strong> " . $_FILES["fileToUpload"]["type"] . "</p>";
                        echo "<p><strong>Velikost:</strong> " . round($_FILES["fileToUpload"]["size"] / 1024, 2) . " KB</p>";
                        echo "</div>";
                    } else {
                        echo "<div class='error'>Došlo k chybě při nahrávání souboru.</div>";
                    }
                }
            } else {
                echo "<div class='error'>Došlo k chybě: " . $_FILES["fileToUpload"]["error"] . "</div>";
            }
        }
        ?>
    </div>

    <script>
        // JavaScript pro drag & drop a preview
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileToUpload');
        const preview = document.getElementById('preview');

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#2196F3';
            dropZone.style.background = '#f8f9fa';
        });

        dropZone.addEventListener('dragleave', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#ddd';
            dropZone.style.background = 'white';
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.style.borderColor = '#ddd';
            dropZone.style.background = 'white';
            fileInput.files = e.dataTransfer.files;
            showPreview();
        });

        fileInput.addEventListener('change', showPreview);

        function showPreview() {
            const file = fileInput.files[0];
            if (file) {
                dropZone.querySelector('p').textContent = `Vybraný soubor: ${file.name}`;
                
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
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
