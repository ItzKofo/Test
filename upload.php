DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
        }
        .drop-zone {
            border: 2px dashed #ddd;
            border-radius: 4px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
            cursor: pointer;
        }
        .drop-zone:hover {
            border-color: #2196F3;
        }
        .file-input {
            display: none;
        }
        .success {
            color: green;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid green;
            border-radius: 4px;
        }
        .error {
            color: red;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid red;
            border-radius: 4px;
        }
        .file-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .rename-container {
            margin: 10px 0;
        }
        input[type="text"] {
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 100%;
            box-sizing: border-box;
        }
        button {
            background: #2196F3;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #1976D2;
        }
    </style>
</head>
<body>
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    if(isset($_POST["submit"])) {
        if(isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) {
            $targetDir = "./";
            
            $originalFileName = basename($_FILES["fileToUpload"]["name"]);
            $fileType = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
            
            if(isset($_POST["rename"]) && !empty($_POST["newFileName"])) {
                $fileName = $_POST["newFileName"] . "." . $fileType;
            } else {
                $fileName = $originalFileName;
            }
            
            $targetPath = $targetDir . $fileName;
            
            error_log("Attempting to upload file: " . $targetPath);
            error_log("Temp file location: " . $_FILES["fileToUpload"]["tmp_name"]);
            
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetPath)) {
                echo "<div class='success'>File uploaded successfully!</div>";
                echo "<div class='file-info'>";
                echo "<p><strong>File location:</strong> " . realpath($targetPath) . "</p>";
                echo "<p><strong>File name:</strong> " . $fileName . "</p>";
                echo "<p><strong>File type:</strong> " . $fileType . "</p>";
                echo "<p><strong>Size:</strong> " . round($_FILES["fileToUpload"]["size"] / 1024, 2) . " KB</p>";
                echo "</div>";
            } else {
                $uploadError = error_get_last();
                echo "<div class='error'>Error uploading file: " . $uploadError['message'] . "</div>";
                error_log("Upload error: " . print_r($uploadError, true));
            }
        } else {
            echo "<div class='error'>Error: " . $_FILES["fileToUpload"]["error"] . "</div>";
        }
    }
    ?>

    <form action="" method="POST" enctype="multipart/form-data" id="uploadForm">
        <div class="drop-zone" id="dropZone">
            <p>Drag and drop a file here or click to select</p>
            <input type="file" name="fileToUpload" id="fileInput" class="file-input">
        </div>
        
        <div class="rename-container">
            <label>
                <input type="checkbox" id="renameCheckbox" name="rename"> Rename file
            </label>
            <input type="text" id="newFileName" name="newFileName" placeholder="Enter new file name (without extension)" style="display: none;">
        </div>

        <button type="submit" name="submit">Upload File</button>
    </form>

    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const renameCheckbox = document.getElementById('renameCheckbox');
        const newFileNameInput = document.getElementById('newFileName');

        dropZone.addEventListener('click', () => {
            fileInput.click();
        });

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
            updateFileName();
        });

        fileInput.addEventListener('change', updateFileName);

        renameCheckbox.addEventListener('change', function() {
            newFileNameInput.style.display = this.checked ? 'block' : 'none';
            if (this.checked) {
                newFileNameInput.required = true;
            } else {
                newFileNameInput.required = false;
                newFileNameInput.value = '';
            }
        });

        function updateFileName() {
            const file = fileInput.files[0];
            if (file) {
                dropZone.querySelector('p').textContent = `Selected file: ${file.name}`;
            }
        }
    </script>
</body>
</html>
