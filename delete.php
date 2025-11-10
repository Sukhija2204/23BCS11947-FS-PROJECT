<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Check if the post exists and user is author or admin
    $result = $conn->query("SELECT * FROM posts WHERE id=$id");
    if ($result->num_rows > 0) {
        $post = $result->fetch_assoc();
        
        if ($post['user_id'] == $_SESSION['user_id'] || $_SESSION['is_admin'] == 1) {
            // Delete the post
            $conn->query("DELETE FROM posts WHERE id=$id");
            
            // Delete the associated image file if it exists
            if (!empty($post['image_url']) && file_exists($post['image_url'])) {
                unlink($post['image_url']);
            }
        }
    }
}

// Redirect to appropriate page
if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'profile.php') !== false) {
    header("Location: profile.php");
} else {
    header("Location: index.php");
}
exit();
?>