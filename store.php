<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$title = mysqli_real_escape_string($conn, $_POST['title']);
$content = mysqli_real_escape_string($conn, $_POST['content']);
$imagePath = '';

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
            $imagePath = $target;
        }
    }
}

$stmt = $conn->prepare("INSERT INTO posts (user_id, title, content, image_url) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $user_id, $title, $content, $imagePath);
$stmt->execute();

header("Location: index.php");
exit();
?>