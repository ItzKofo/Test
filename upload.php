Zde je jednoduchý PHP kód pro upload souborů:

```php
<!DOCTYPE html>
<html>
<head>
    <title>File Upload</title>
    <style>
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>Upload souboru</h2>

    <!-- Formulář pro upload -->
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="file" name="fileToUpload" required>
        <input type="submit" name="submit" value="Nahrát soubor">
    </form>

    <?php
    if(isset($_POST["submit"])) {
        // Kontrola, zda byl soubor úspěšně nahrán
        if(isset($_FILES["fileToUpload"]) && $_FILES["fileToUpload"]["error"] == 0) {
            
            $targetDir = "./uploads/"; // Složka pro nahrané soubory
            
            // Vytvoření složky, pokud neexistuje
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            
            // Získání informací o souboru
            $fileName = basename($_FILES["fileToUpload"]["name"]);
            $targetPath = $targetDir . $fileName;
            $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
            
            // Můžete přidat kontrolu typu souboru
            $allowedTypes = array("jpg", "jpeg", "png", "gif", "pdf", "doc", "docx");
            
            if (!in_array($fileType, $allowedTypes)) {
                echo "<p class='error'>Pouze soubory typu JPG, JPEG, PNG, GIF, PDF, DOC a DOCX jsou povoleny.</p>";
            } else {
                // Pokus o nahrání souboru
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetPath)) {
                    echo "<p class='success'>Soubor byl úspěšně nahrán.</p>";
                    echo "<p>Soubor je uložen v: " . realpath($targetPath) . "</p>";
                    
                    // Zobrazení dalších informací o souboru
                    echo "<h3>Informace o souboru:</h3>";
                    echo "Název souboru: " . $fileName . "<br>";
                    echo "Typ souboru: " . $_FILES["fileToUpload"]["type"] . "<br>";
                    echo "Velikost souboru: " . round($_FILES["fileToUpload"]["size"] / 1024, 2) . " KB<br>";
                } else {
                    echo "<p class='error'>Došlo k chybě při nahrávání souboru.</p>";
                }
            }
        } else {
            echo "<p class='error'>Došlo k chybě: " . $_FILES["fileToUpload"]["error"] . "</p>";
        }
    }
    ?>
</body>
</html>
