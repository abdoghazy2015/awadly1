<?php
require_once("config.php");

session_start();

if (isset($_SESSION["user_id"])) {
    header("Location: homepage.php");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Image Storage - The Fastest Image Stoarge</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Welcome to the ImageStorage App</h2>
        <div class="row">
            <div class="col-md-6">
                <h3>Log In</h3>
                <p>Already have an account? Log in below:</p>
                <a href="login.php" class="btn btn-primary">Log In</a>
            </div>
            <div class="col-md-6">
                <h3>Register</h3>
                <p>Don't have an account? Register here:</p>
                <a href="register.php" class="btn btn-success">Register</a>
            </div>
        </div>
    </div>
</body>
</html>
