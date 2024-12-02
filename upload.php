<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload</title>
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

        .rename-option {
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .rename-option label {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            color: #333;
        }

        .rename-option input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .rename-option input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-top: 10px;
            display: none;
        }

        .rename-option input[type="text"]:focus {
            border-color: #2196F3;
            outline: none;
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
        <h2>Upload File</h2>

        <form action="" method="POST" enctype="multipart/form-data" id="uploadForm">
            <div class="upload-area" id="dropZone">
                <p>Drag and drop files here or click to select</p>
                <input type="file" name="fileToUpload" id="fileToUpload" required>
            </div>

            <div class="rename-option">
                <label>
                    <input type="checkbox" id="renameCheckbox" name="rename">
                    Save with different name
                </label>
                <input type="text" id="newFileName" name="newFileName" placeholder="Enter new file name (without extension)">
            </div>

            <button type="submit" name="submit" class="upload-btn">Upload</button>
        </form>
        <p class="allowed-types">Allowed formats: JPG, JPEG, PNG, GIF, PDF, ZIP</p>
        <img id="preview" alt="Preview">

        <?php
        if(isset($_POST["submit"])) {
            if(isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) {
                $targetDir = "./";
                $originalFileName = basename($_FILES["fileToUpload"]["name"]);
                $fileType = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
                
                // Handle renamed file
                if(isset($_POST["rename"]) && !empty($_POST["newFileName"])) {
                    $fileName = $_POST["newFileName"] . "." . $fileType;
                } else {
                    $fileName = $originalFileName;
                }
                
                $targetPath = $targetDir . $fileName;
                
                $allowedTypes = array("jpg", "jpeg", "png", "gif", "pdf", "zip");
                
                if (!in_array($fileType, $allowedTypes)) {
                    echo "<div class='error'>Only JPG, JPEG, PNG, GIF, PDF, and ZIP files are allowed.</div>";
                } else {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetPath)) {
                        echo "<div class='success'>File uploaded successfully!</div>";
                        echo "<div class='file-info'>";
                        echo "<p><strong>File location:</strong> " . realpath($targetPath) . "</p>";
                        echo "<p><strong>File name:</strong> " . $fileName . "</p>";
                        echo "<p><strong>File type:</strong> " . $_FILES["fileToUpload"]["type"] . "</p>";
                        echo "<p><strong>Size:</strong> " . round($_FILES["fileToUpload"]["size"] / 1024, 2) . " KB</p>";
                        echo "</div>";
                    } else {
                        echo "<div class='error'>Error uploading file.</div>";
                    }
                }
            } else {
                echo "<div class='error'>Error: " . $_FILES["fileToUpload"]["error"] . "</div>";
            }
        }
        ?>
    </div>

    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileToUpload');
        const preview = document.getElementById('preview');
        const renameCheckbox = document.getElementById('renameCheckbox');
        const newFileNameInput = document.getElementById('newFileName');

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

        renameCheckbox.addEventListener('change', function() {
            newFileNameInput.style.display = this.checked ? 'block' : 'none';
            if (this.checked) {
                newFileNameInput.required = true;
            } else {
                newFileNameInput.required = false;
                newFileNameInput.value = '';
            }
        });

        function showPreview() {
            const file = fileInput.files[0];
            if (file) {
                dropZone.querySelector('p').textContent = `Selected file: ${file.name}`;
                
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
