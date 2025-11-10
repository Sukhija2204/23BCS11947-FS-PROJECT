<?php
session_start();
include 'db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit();
}

// Handle post deletion if requested
if (isset($_GET['delete_post']) && is_numeric($_GET['delete_post'])) {
    $post_id = intval($_GET['delete_post']);
    $post_query = "SELECT image_url FROM posts WHERE id = $post_id";
    $post_result = mysqli_query($conn, $post_query);
    if ($post = mysqli_fetch_assoc($post_result)) {
        // Delete image file if exists
        if (!empty($post['image_url']) && file_exists($post['image_url'])) {
            unlink($post['image_url']);
        }
    }
    mysqli_query($conn, "DELETE FROM posts WHERE id = $post_id");
}

// Handle user deletion if requested
if (isset($_GET['delete_user']) && is_numeric($_GET['delete_user'])) {
    $user_id = intval($_GET['delete_user']);
    // Don't allow admins to delete themselves
    if ($user_id != $_SESSION['user_id']) {
        // Delete user's posts first
        $user_posts = mysqli_query($conn, "SELECT id, image_url FROM posts WHERE user_id = $user_id");
        while ($post = mysqli_fetch_assoc($user_posts)) {
            if (!empty($post['image_url']) && file_exists($post['image_url'])) {
                unlink($post['image_url']);
            }
            mysqli_query($conn, "DELETE FROM posts WHERE id = " . $post['id']);
        }
        // Then delete the user
        mysqli_query($conn, "DELETE FROM users WHERE id = $user_id");
    }
}

// Toggle admin status if requested
if (isset($_GET['toggle_admin']) && is_numeric($_GET['toggle_admin'])) {
    $user_id = intval($_GET['toggle_admin']);
    // Don't allow admins to remove their own admin privileges
    if ($user_id != $_SESSION['user_id']) {
        $user_query = "SELECT is_admin FROM users WHERE id = $user_id";
        $user_result = mysqli_query($conn, $user_query);
        if ($user = mysqli_fetch_assoc($user_result)) {
            $new_status = $user['is_admin'] == 1 ? 0 : 1;
            mysqli_query($conn, "UPDATE users SET is_admin = $new_status WHERE id = $user_id");
        }
    }
}

// Get stats
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users"))['count'];
$total_posts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM posts"))['count'];
$total_messages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM messages"))['count'];

// Get latest posts
$latest_posts = mysqli_query($conn, "SELECT posts.id, posts.title, posts.created_at, users.username 
                                     FROM posts 
                                     JOIN users ON posts.user_id = users.id 
                                     ORDER BY posts.created_at DESC LIMIT 5");

// Get all users
$all_users = mysqli_query($conn, "SELECT * FROM users ORDER BY id");

// Get recent messages
$recent_messages = mysqli_query($conn, "SELECT * FROM messages ORDER BY id DESC LIMIT 5");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Moderator Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <div class="admin-header">
            <h1>Moderator Dashboard</h1>
        </div>
        
        <!-- Stats Overview -->
        <div class="admin-stats">
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="stat-number"><?php echo $total_users; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Posts</h3>
                <div class="stat-number"><?php echo $total_posts; ?></div>
            </div>
            <div class="stat-card">
                <h3>Messages</h3>
                <div class="stat-number"><?php echo $total_messages; ?></div>
            </div>
        </div>
        
        <!-- Latest Posts Management -->
        <div class="admin-section">
            <h2>Latest Posts</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($post = mysqli_fetch_assoc($latest_posts)): ?>
                    <tr>
                        <td><?php echo $post['id']; ?></td>
                        <td><?php echo htmlspecialchars($post['title']); ?></td>
                        <td><?php echo htmlspecialchars($post['username']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
                        <td>
                            <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-small">View</a>
                            <a href="edit.php?id=<?php echo $post['id']; ?>" class="btn btn-small">Edit</a>
                            <a href="mod.php?delete_post=<?php echo $post['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="all-posts.php" class="view-all">View All Posts</a>
        </div>
        
        <!-- User Management -->
        <div class="admin-section">
            <h2>User Management</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = mysqli_fetch_assoc($all_users)): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></td>
                        <td>
                            <a href="author.php?id=<?php echo $user['id']; ?>" class="btn btn-small">View Posts</a>
                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <a href="mod.php?toggle_admin=<?php echo $user['id']; ?>" class="btn btn-small">
                                    <?php echo $user['is_admin'] ? 'Remove Admin' : 'Make Admin'; ?>
                                </a>
                                <a href="mod.php?delete_user=<?php echo $user['id']; ?>" class="btn btn-small btn-danger" 
                                   onclick="return confirm('Are you sure you want to delete this user and ALL their posts?')">Delete</a>
                            <?php else: ?>
                                <span class="btn btn-small" style="cursor: not-allowed; opacity: 0.7;">Current User</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Contact Messages -->
        <div class="admin-section">
            <h2>Recent Messages</h2>
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
                    <?php while ($message = mysqli_fetch_assoc($recent_messages)): ?>
                    <tr>
                        <td><?php echo $message['id']; ?></td>
                        <td><?php echo htmlspecialchars($message['name']); ?></td>
                        <td><?php echo htmlspecialchars($message['email']); ?></td>
                        <td><?php echo substr(htmlspecialchars($message['message']), 0, 50) . '...'; ?></td>
                        <td>
                            <a href="view-message.php?id=<?php echo $message['id']; ?>" class="btn btn-small">View</a>
                            <a href="delete-message.php?id=<?php echo $message['id']; ?>" class="btn btn-small btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <a href="all-messages.php" class="view-all">View All Messages</a>
        </div>
    </div>
</body>
</html>