<?php
session_start();
include 'db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit();
}

// Handle message deletion if requested
if (isset($_GET['delete_message']) && is_numeric($_GET['delete_message'])) {
    $message_id = intval($_GET['delete_message']);
    mysqli_query($conn, "DELETE FROM messages WHERE id = $message_id");
}

// Pagination setup
$messages_per_page = 15;
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($current_page - 1) * $messages_per_page;

// Get total count for pagination
$total_messages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM messages"))['count'];
$total_pages = ceil($total_messages / $messages_per_page);

// Get all messages with pagination
$all_messages = mysqli_query($conn, "SELECT * FROM messages ORDER BY id DESC LIMIT $offset, $messages_per_page");
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Messages - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <div class="admin-header">
            <h1>All Messages</h1>
            <a href="mod.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
        
        <div class="admin-section">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Message</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($message = mysqli_fetch_assoc($all_messages)): ?>
                    <tr>
                        <td><?php echo $message['id']; ?></td>
                        <td><?php echo htmlspecialchars($message['name']); ?></td>
                        <td><?php echo htmlspecialchars($message['email']); ?></td>
                        <td><?php echo substr(htmlspecialchars($message['message']), 0, 100) . (strlen($message['message']) > 100 ? '...' : ''); ?></td>
                        <td>
                            <a href="view-message.php?id=<?php echo $message['id']; ?>" class="btn btn-small">View</a>
                            <a href="all-messages.php?delete_message=<?php echo $message['id']; ?>&page=<?php echo $current_page; ?>" 
                               class="btn btn-small btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="all-messages.php?page=<?php echo ($current_page - 1); ?>" class="page-btn">&laquo; Previous</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="all-messages.php?page=<?php echo $i; ?>" 
                       class="page-number <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages): ?>
                    <a href="all-messages.php?page=<?php echo ($current_page + 1); ?>" class="page-btn">Next &raquo;</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>