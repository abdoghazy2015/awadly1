<?php
session_start();
require_once("config.php");

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   // Get the image ID and new filename from the POST data
$image_id = $_POST["image_id"];
$new_filename = $_POST["new_filename"];
$user_id = $_SESSION["user_id"];

// Retrieve the old filename from the database
$stmt = $pdo->prepare("SELECT filename FROM images WHERE image_id = ? AND user_id = ?");
$stmt->execute([$image_id, $user_id]);

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $old_filename = $row["filename"];


    //Update the new_filename in db
    $update_stmt = $pdo->prepare("UPDATE images SET filename = ? WHERE image_id = ? AND user_id = ?");
    $update_stmt->execute([$new_filename, $image_id, $user_id]);


    // Rename the file on the server
    $BlackListed = array("..", "../", "./", ":", ".ph", ".sh", ".inc", ".htaccess", ".mod", ".hp", ".cpt", ".pgif");
    foreach ($BlackListed as $blacklistedString) {
        if (strpos($new_filename, $blacklistedString) !== false) {
            echo "File name contains a blacklisted string: $blacklistedString";
            //restore old_filename
            $update_stmt = $pdo->prepare("UPDATE images SET filename = ? WHERE image_id = ? AND user_id = ?");
            $update_stmt->execute([$old_filename, $image_id, $user_id]);
            exit();
        }
    }


    $user_folder = $_SESSION["folder_name"];
    $old_file_path = __DIR__ . '/uploads/' . $user_folder . '/' . $old_filename;
    $new_file_path = __DIR__ . '/uploads/' . $user_folder . '/' . $new_filename;

    if (rename($old_file_path, $new_file_path)) {
        // File renamed and database updated successfully
        header("Location: homepage.php");
        exit();
    } else {

        echo "Failed to rename the file on the server.";
        //restore old_filename
        $update_stmt = $pdo->prepare("UPDATE images SET filename = ? WHERE image_id = ? AND user_id = ?");
        $update_stmt->execute([$old_filename, $image_id, $user_id]);
        exit();
    }
} else {
    echo "Image not found or you don't have permission to edit it.";
}

}
