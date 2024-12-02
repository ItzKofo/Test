<?php
// Configuration
$maxFileSize = 50 * 1024 * 1024; // 50MB in bytes
$uploadDir = "./"; // Current directory
$allowedTypes = array(
    "jpg" => "image/jpeg",
    "jpeg" => "image/jpeg",
    "png" => "image/png",
    "gif" => "image/gif",
    "pdf" => "application/pdf",
    "zip" => "application/zip"
);

if(isset($_POST["submit"])) {
    if(isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) {
        $file = $_FILES["fileToUpload"];
        
        // File size check
        if ($file["size"] > $maxFileSize) {
            echo "<div class='error'>File is too large. Maximum allowed size is 50MB.</div>";
            exit;
        }

        // Get real MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file["tmp_name"]);
        finfo_close($finfo);

        // File type check
        $fileExt = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        if (!array_key_exists($fileExt, $allowedTypes) || $allowedTypes[$fileExt] !== $mimeType) {
            echo "<div class='error'>Invalid file type. Allowed types are: JPG, JPEG, PNG, GIF, PDF and ZIP.</div>";
            exit;
        }

        // Use original filename
        $fileName = $file["name"];
        
        // If rename option is checked, generate safe filename
        if (isset($_POST["rename"]) && $_POST["rename"] == "1") {
            $fileName = uniqid() . '_' . preg_replace("/[^a-zA-Z0-9]/", "", pathinfo($file["name"], PATHINFO_FILENAME)) . '.' . $fileExt;
        }
        
        $targetPath = $uploadDir . $fileName;

        // Move file
        if (move_uploaded_file($file["tmp_name"], $targetPath)) {
            chmod($targetPath, 0644);
            
            echo "<div class='success'>File uploaded successfully!</div>";
            echo "<div class='file-info'>";
            echo "<p><strong>Filename:</strong> " . htmlspecialchars($fileName) . "</p>";
            echo "<p><strong>File type:</strong> " . htmlspecialchars($mimeType) . "</p>";
            echo "<p><strong>Size:</strong> " . round($file["size"] / 1024, 2) . " KB</p>";
            echo "</div>";
        } else {
            echo "<div class='error'>Error uploading file.</div>";
        }
    } else {
        echo "<div class='error'>Error: " . $_FILES["fileToUpload"]["error"] . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .upload-container {
            background-color: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .drop-zone {
            border: 2px dashed #3498db;
            padding: 40px;
            text-align: center;
            margin: 20px 0;
            cursor: pointer;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .drop-zone:hover {
            background-color: #f8f9fa;
            border-color: #2980b9;
        }
        .drop-zone.dragover {
            background-color: #e8f4f8;
            border-color: #2980b9;
        }
        .preview {
            max-width: 200px;
            display: none;
            margin: 10px auto;
            border-radius: 5px;
        }
        .error {
            color: #e74c3c;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            background-color: #fde8e7;
        }
        .success {
            color: #27ae60;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
            background-color: #e8f5e9;
        }
        .file-info {
            background-color: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .progress-bar {
            width: 100%;
            height: 20px;
            background-color: #f0f0f0;
            border-radius: 10px;
            margin: 20px 0;
            display: none;
        }
        .progress {
            width: 0%;
            height: 100%;
            background-color: #3498db;
            border-radius: 10px;
            transition: width 0.3s ease;
        }
        .submit-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .submit-btn:hover {
            background-color: #2980b9;
        }
        .options {
            margin: 20px 0;
        }
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        .checkbox-wrapper input[type="checkbox"] {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="upload-container">
        <h2>File Upload</h2>
        <form action="" method="post" enctype="multipart/form-data" id="uploadForm">
            <div class="drop-zone" id="dropZone">
                <p>Drag & drop files here or click to select</p>
                <input type="file" name="fileToUpload" id="fileToUpload" style="display: none;">
                <img id="preview" class="preview">
            </div>
            
            <div class="progress-bar" id="progressBar">
                <div class="progress" id="progress"></div>
            </div>

            <div class="options">
                <div class="checkbox-wrapper">
                    <input type="checkbox" id="rename" name="rename" value="1">
                    <label for="rename">Generate safe filename</label>
                </div>
            </div>

            <input type="submit" value="Upload" name="submit" class="submit-btn">
        </form>
    </div>

    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileToUpload');
        const preview = document.getElementById('preview');
        const progressBar = document.getElementById('progressBar');
        const progress = document.getElementById('progress');
        const form = document.getElementById('uploadForm');

        dropZone.addEventListener('click', () => fileInput.click());

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

        fileInput.addEventListener('change', showPreview);

        function showPreview() {
            const file = fileInput.files[0];
            if (file) {
                const maxSize = 50 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('File is too large! Maximum allowed size is 50MB.');
                    fileInput.value = '';
                    preview.style.display = 'none';
                    dropZone.querySelector('p').textContent = 'Drag & drop files here or click to select';
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

        form.onsubmit = function(e) {
            const file = fileInput.files[0];
            if (!file) return;

            progressBar.style.display = 'block';
            
            const xhr = new XMLHttpRequest();
            const formData = new FormData(form);

            xhr.upload.onprogress = function(e) {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    progress.style.width = percentComplete + '%';
                }
            };

            xhr.onload = function() {
                // Handle response here if needed
                setTimeout(() => {
                    progressBar.style.display = 'none';
                    progress.style.width = '0%';
                }, 1000);
            };

            return true;
        };
    </script>
</body>
</html>
