<?php
session_start();

// Destroy the session data
session_destroy();

// Redirect to the login page or any other desired page
header("Location: index.php"); // Replace "login.php" with the appropriate URL
exit();
?>