<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Post</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Create a New Blog Post</h1>
        <form action="store.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Post Title</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" class="content" name="content" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Feature Image</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            
            <button type="submit" class="btn btn-primary">Publish Post</button>
        </form>
    </div>
</body>
</html>