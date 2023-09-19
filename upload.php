<?php
session_start();
require_once("config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$uploadMessage = "";

$folder_name = $_SESSION["folder_name"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $targetDir = "uploads/$folder_name/";
    $targetFile = $targetDir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if the file is an actual image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check === false) {
        $uploadOk = 0;
    }

    // Check file size (max 2MB)
    if ($_FILES["image"]["size"] > 2 * 1024 * 1024) {
        $uploadOk = 0;
    }

    // Allow only specific image file formats
    $allowedExtensions = array("jpg", "jpeg", "png", "gif", "svg");
    if (!in_array($imageFileType, $allowedExtensions)) {
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            // Generate a unique image ID
            $imageId = uniqid();

            // Insert the image data into the database with the image_id
            $stmt = $pdo->prepare("INSERT INTO images (user_id, image_id, filename) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $imageId, basename($_FILES["image"]["name"])]);

            if ($stmt->rowCount() > 0) {
                $uploadMessage = "Image uploaded successfully.";
            } else {
                $uploadMessage = "Image upload failed. Please try again.";
            }
        } else {
            $uploadMessage = "There was an error uploading your image.";
        }
    } else {
        $uploadMessage = "Invalid image file. Please choose a valid image (jpg, jpeg, png, gif, svg) of max 2MB size.";
    }
    
    // Redirect back to homepage with the upload message
    $_SESSION["upload_message"] = $uploadMessage;
    header("Location: homepage.php");
    exit();
}
?>
