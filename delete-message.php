<?php
session_start();
include 'db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $message_id = intval($_GET['id']);
    mysqli_query($conn, "DELETE FROM messages WHERE id = $message_id");
}

// Redirect back to messages page or dashboard
if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'all-messages.php') !== false) {
    header("Location: all-messages.php");
} else {
    header("Location: mod.php");
}
exit();
?>