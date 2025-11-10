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

// Pagination setup
$posts_per_page = 15;
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($current_page - 1) * $posts_per_page;

// Get total count for pagination
$total_posts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM posts"))['count'];
$total_pages = ceil($total_posts / $posts_per_page);

// Get all posts with pagination
$all_posts = mysqli_query($conn, "SELECT posts.id, posts.title, posts.created_at, users.username, users.id as user_id
                                  FROM posts 
                                  JOIN users ON posts.user_id = users.id 
                                  ORDER BY posts.created_at DESC 
                                  LIMIT $offset, $posts_per_page");
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Posts - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <div class="admin-header">
            <h1>All Posts</h1>
            <a href="mod.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
        
        <div class="admin-section">
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
                    <?php while ($post = mysqli_fetch_assoc($all_posts)): ?>
                    <tr>
                        <td><?php echo $post['id']; ?></td>
                        <td><?php echo htmlspecialchars($post['title']); ?></td>
                        <td>
                            <a href="author.php?id=<?php echo $post['user_id']; ?>">
                                <?php echo htmlspecialchars($post['username']); ?>
                            </a>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($post['created_at'])); ?></td>
                        <td>
                            <a href="post.php?id=<?php echo $post['id']; ?>" class="btn btn-small">View</a>
                            <a href="edit.php?id=<?php echo $post['id']; ?>" class="btn btn-small">Edit</a>
                            <a href="all-posts.php?delete_post=<?php echo $post['id']; ?>&page=<?php echo $current_page; ?>" 
                               class="btn btn-small btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this post?')">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="all-posts.php?page=<?php echo ($current_page - 1); ?>" class="page-btn">&laquo; Previous</a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="all-posts.php?page=<?php echo $i; ?>" 
                       class="page-number <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($current_page < $total_pages): ?>
                    <a href="all-posts.php?page=<?php echo ($current_page + 1); ?>" class="page-btn">Next &raquo;</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>