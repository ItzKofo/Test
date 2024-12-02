<?php
// Configuration
$maxFileSize = 50 * 1024 * 1024; // 50MB in bytes
$uploadDir = "./"; // Current directory

if(isset($_POST["submit"])) {
    if(isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) {
        $file = $_FILES["fileToUpload"];
        
        // File size check
        if ($file["size"] > $maxFileSize) {
            echo "<div class='error'>File is too large. Maximum allowed size is 50MB.</div>";
            exit;
        }

        // Get file extension
        $fileExt = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        
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
            echo "<p><strong>File type:</strong> " . htmlspecialchars($file["type"]) . "</p>";
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
