<?php
session_start();
include 'db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: mod.php");
    exit();
}

$message_id = intval($_GET['id']);
$message_result = mysqli_query($conn, "SELECT * FROM messages WHERE id = $message_id");

if (mysqli_num_rows($message_result) == 0) {
    header("Location: mod.php");
    exit();
}

$message = mysqli_fetch_assoc($message_result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>View Message</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <div class="admin-header">
            <h1>Message Details</h1>
            <div>
                <a href="all-messages.php" class="btn">Back to Messages</a>
                <a href="mod.php" class="btn btn-primary">Back to Dashboard</a>
            </div>
        </div>

        <div class="admin-section message-details">
            <div class="message-meta">
                <p><strong>From:</strong> <?php echo htmlspecialchars($message['name']); ?> (<?php echo htmlspecialchars($message['email']); ?>)</p>
            </div>

            <div class="message-content">
                <h3>Message:</h3>
                <div class="message-body">
                    <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                </div>
            </div>

            <div class="message-actions">
                <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>" class="btn btn-primary">Reply via Email</a>
                <a href="delete-message.php?id=<?php echo $message['id']; ?>" class="btn btn-danger" 
                   onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
            </div>
        </div>
    </div>
</body>
</html>