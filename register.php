<?php
require_once("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Validate and insert user data into the database
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);

    // If a row is returned, the username is already taken
    if ($stmt->fetch()) {
        $registrationError = "Username already exists. Please choose a different one.";
    } else {
        $user_folder = md5($username);
        // Insert the new user's data into the database
        $stmt = $pdo->prepare("INSERT INTO users (username, password,folder_name) VALUES (?, ?, ?)");
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->execute([$username, $hashedPassword,$user_folder]);
        

        try {
            // Create a folder for the user
            $user_directory = "uploads/$user_folder";
            if (!file_exists($user_directory)) {
                mkdir($user_directory, 0755, true);
            }
            $download_php_source = 'download.php'; // Adjust the path to your download.php
            $download_php_destination = "$user_directory/download.php";
            if (file_exists($download_php_source)) {
                copy($download_php_source, $download_php_destination);
            }
            $registrationSuccess = "Registration successful. You can now log in. <a href=login.php>Login</a>";
        } catch (Exception $e) {
            // Handle the exception here, you can log it or display an error message
            $errorMessage = "An error occurred: " . $e->getMessage();
        }
        
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Registration</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Registration</h2>
        <?php if (isset($registrationError)) : ?>
            <div class="alert alert-danger"><?php echo $registrationError; ?></div>
        <?php endif; ?>
        <?php if (isset($registrationSuccess)) : ?>
            <div class="alert alert-success"><?php echo $registrationSuccess; ?></div>
        <?php endif; ?>
        <form method="post" action="register.php">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
</body>

</html>