<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get post data
$result = $conn->query("SELECT * FROM posts WHERE id=$id");

if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$post = $result->fetch_assoc();

// Check if user is the author or admin
if ($post['user_id'] != $_SESSION['user_id'] && $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Post</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Edit Post</h1>
        <form action="update.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $post['id'] ?>">
            
            <div class="form-group">
                <label for="title">Post Title</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($post['title']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="content">Content</label>
                <textarea id="content" class="content" name="content" required><?= htmlspecialchars($post['content']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Feature Image</label>
                <?php if (!empty($post['image_url'])): ?>
                    <div class="current-image">
                        <img src="<?= $post['image_url'] ?>" alt="Current image" style="max-width:200px;">
                        <p>Current image</p>
                    </div>
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/*">
                <small>Leave empty to keep current image</small>
            </div>
            
            <button type="submit" class="btn btn-primary">Update Post</button>
        </form>
    </div>
</body>
</html>