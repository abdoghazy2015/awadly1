<?php
require_once("../../config.php");

if (isset($_GET["id"])) {
    $imageId = $_GET["id"];

    // Retrieve the filename associated with the image ID
    $stmt = $pdo->prepare("SELECT filename FROM images WHERE image_id = ?");
    $stmt->execute([$imageId]);

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $filename = $row["filename"];

        // Set the appropriate headers for file download
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Length: " . filesize("$filename"));

        // Output the file content
        readfile($filename);
        exit();
    }
}

// Handle the case when the image ID is not found
echo "Image not found.";
