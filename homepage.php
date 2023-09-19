<?php
require_once("config.php");

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];

// Fetch image IDs and filenames associated with the logged-in user
$stmt = $pdo->prepare("SELECT image_id, filename FROM images WHERE user_id = ?");
$stmt->execute([$user_id]);

$images = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!isset($_SESSION["folder_name"])) {
    $stmt = $pdo->prepare("SELECT folder_name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $folder_name = $stmt->fetchColumn();
    $_SESSION["folder_name"] = $folder_name;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>ImageStorage</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        /* Custom CSS to style the page */
        .top-right {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .upload-form {
            margin-top: 20px;
        }

        .image-card {
            margin-bottom: 10px;
            padding: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Welcome to ImageStorage -The fastest Image Stoarge</h2>

        <!-- Logout link in the top right corner -->
        <div class="top-right">
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <!-- Display the upload message if it exists -->
        <?php if (isset($_SESSION["upload_message"])) : ?>
            <div class="alert alert-info">
                <?php echo $_SESSION["upload_message"]; ?>
            </div>
            <?php unset($_SESSION["upload_message"]); // Clear the session variable after displaying ?>
        <?php endif; ?>
        <br><br>
        <!-- Image upload form -->
        <div class="upload-form">
            <h3>Upload an Image</h3>
            <form method="post" action="upload.php" enctype="multipart/form-data">
                <div class="form-group row">
                    <label for="image" class="col-sm-2 col-form-label">Choose an Image:</label>
                    <div class="col-sm-6">
                        <input type="file" class="form-control-file" name="image" id="image" accept=".jpg, .jpeg, .png, .gif, .svg" required>
                    </div>
                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </div>
            </form>
        </div>
        <br><br>

        <!-- Display uploaded images -->
        <div class="mt-4">
            <h3>Your Uploaded Images</h3>

            <?php if (!empty($images)) : ?>
                <?php foreach ($images as $image) : ?>
                    <div class="card image-card">
                        <div class="card-body">
                            <h5 class="d-inline card-title"><?php echo $image['filename']; ?></h5>
                            <a href="uploads/<?php echo $_SESSION["folder_name"] ?>/download.php?id=<?php echo $image['image_id']; ?>" class="btn btn-primary btn-sm">Download</a>
                            <button type="button" class="btn btn-primary btn-sm edit-name-button" data-toggle="modal" data-target="#editNameModal_<?php echo $image['image_id']; ?>">Edit Name</button>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>You haven't uploaded any images yet.</p>
            <?php endif; ?>
        </div>
    </div>
    <!-- Edit Name Modal -->
    <?php foreach ($images as $image) : ?>
        <div class="modal fade" id="editNameModal_<?php echo $image['image_id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editNameModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="edit_filename.php" method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editNameModalLabel">Edit Name</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="image_id" value="<?php echo $image['image_id']; ?>">
                            <div class="form-group">
                                <label for="new_filename_<?php echo $image['image_id']; ?>">New Name:</label>
                                <input type="text" class="form-control" name="new_filename" id="new_filename_<?php echo $image['image_id']; ?>" placeholder="New Name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <script src="js/jquery-3.5.1.slim.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // No need to manually extract the modal ID; Bootstrap will handle it
        });
    </script>

</body>
</html>
