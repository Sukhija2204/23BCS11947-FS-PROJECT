<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = intval($_POST['id']);
$title = mysqli_real_escape_string($conn, $_POST['title']);
$content = mysqli_real_escape_string($conn, $_POST['content']);

// Check if the post exists and user is author or admin
$result = $conn->query("SELECT * FROM posts WHERE id=$id");
if ($result->num_rows == 0) {
    header("Location: index.php");
    exit();
}

$post = $result->fetch_assoc();
if ($post['user_id'] != $_SESSION['user_id'] && $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit();
}

// Handle image upload
if (!empty($_FILES['image']['name'])) {
    // Create uploads directory if it doesn't exist
    if (!file_exists('uploads')) {
        mkdir('uploads', 0777, true);
    }
    
    $target = "uploads/" . time() . "_" . basename($_FILES['image']['name']);
    
    // Check if image file is a actual image
    $check = getimagesize($_FILES['image']['tmp_name']);
    if($check !== false) {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            // If there was an old image, we could delete it here
            $stmt = $conn->prepare("UPDATE posts SET title=?, content=?, image_url=? WHERE id=?");
            $stmt->bind_param("sssi", $title, $content, $target, $id);
        }
    }
} else {
    // No new image uploaded, keep the existing one
    $stmt = $conn->prepare("UPDATE posts SET title=?, content=? WHERE id=?");
    $stmt->bind_param("ssi", $title, $content, $id);
}

$stmt->execute();

header("Location: post.php?id=$id");
exit();
?>