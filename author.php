<?php
include 'db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$author_id = intval($_GET['id']);

// Get author info
$author_result = mysqli_query($conn, "SELECT username FROM users WHERE id = $author_id");
if (mysqli_num_rows($author_result) == 0) {
    header("Location: index.php");
    exit();
}

$author = mysqli_fetch_assoc($author_result);
$author_name = htmlspecialchars($author['username']);

// Get author's posts
$posts_query = "SELECT * FROM posts WHERE user_id = $author_id ORDER BY created_at DESC";
$posts_result = mysqli_query($conn, $posts_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $author_name; ?>'s Posts</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>
    
    <div class="container">
        <div class="author-header">
            <h1><?php echo $author_name; ?>'s Posts</h1>
        </div>
        
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
                                    <span class="author">By <?php echo $author_name; ?></span>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="no-posts">
                <p>This author hasn't created any posts yet.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>