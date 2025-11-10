<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get user posts
$posts_query = "SELECT * FROM posts WHERE user_id = $user_id ORDER BY created_at DESC";
$posts_result = mysqli_query($conn, $posts_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <div class="profile-header">
            <h1>Welcome, <?php echo htmlspecialchars($username); ?></h1>
            <a href="create.php" class="btn btn-primary">Create New Post</a>
        </div>
        
        <div class="profile-section">
            <h2>My Posts</h2>
            
            <?php if (mysqli_num_rows($posts_result) > 0): ?>
                <div class="card-grid">
                    <?php while ($post = mysqli_fetch_assoc($posts_result)): ?>
                        <div class="card">
                            <a href="post.php?id=<?php echo $post['id']; ?>">
                                <div class="card-image" style="background-image: url('<?php echo !empty($post['image_url']) ? $post['image_url'] : 'default-post-image.jpg'; ?>')"></div>
                                <div class="card-content">
                                    <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                                    <p class="card-excerpt"><?php echo substr(htmlspecialchars($post['content']), 0, 100) . '...'; ?></p>
                                    <div class="card-footer">
                                        <span class="read-more">See more</span>
                                    </div>
                                </div>
                            </a>
                            <div class="card-actions">
                                <a href="edit.php?id=<?php echo $post['id']; ?>" class="btn btn-small">Edit</a>
                                <a href="delete.php?id=<?php echo $post['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-posts">
                    <p>You haven't created any posts yet.</p>
                    <a href="create.php" class="btn btn-primary">Create Your First Post</a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="profile-footer">
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</body>
</html>