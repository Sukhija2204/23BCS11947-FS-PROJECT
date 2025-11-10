<?php
include 'db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Blog</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <h1>Latest Blog Posts</h1>
        
        <div class="card-grid">
            <?php
            // Pagination setup
            $posts_per_page = 12; // 4 cards per row * 3 rows
            $current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
            $offset = ($current_page - 1) * $posts_per_page;
            
            // Query with pagination
            if (isset($_GET['search'])) {
                $search = mysqli_real_escape_string($conn, $_GET['search']);
                $count_sql = "SELECT COUNT(*) as total FROM posts WHERE title LIKE '%$search%' OR content LIKE '%$search%'";
                $sql = "SELECT posts.*, users.username FROM posts 
                       JOIN users ON posts.user_id = users.id
                       WHERE title LIKE '%$search%' OR content LIKE '%$search%' 
                       ORDER BY posts.created_at DESC LIMIT $offset, $posts_per_page";
            } else {
                $count_sql = "SELECT COUNT(*) as total FROM posts";
                $sql = "SELECT posts.*, users.username FROM posts 
                       JOIN users ON posts.user_id = users.id
                       ORDER BY posts.created_at DESC LIMIT $offset, $posts_per_page";
            }
            
            // Get total posts for pagination
            $count_result = mysqli_query($conn, $count_sql);
            $count_row = mysqli_fetch_assoc($count_result);
            $total_posts = $count_row['total'];
            $total_pages = ceil($total_posts / $posts_per_page);
            
            $result = mysqli_query($conn, $sql);
            
            if (mysqli_num_rows($result) > 0) {
                while ($row = $result->fetch_assoc()) {
                    $title = htmlspecialchars($row['title']);
                    $content_excerpt = substr(strip_tags($row['content']), 0, 100) . '...';
                    $author = htmlspecialchars($row['username']);
                    
                    if (isset($search)) {
                        $highlight = "<mark>$search</mark>";
                        $title = preg_replace("/($search)/i", $highlight, $title);
                        $content_excerpt = preg_replace("/($search)/i", $highlight, $content_excerpt);
                    }
                    
                    // Default image if none is provided
                    $image_url = !empty($row['image_url']) ? $row['image_url'] : 'default-post-image.jpg';
                    
                    echo '<div class="card">';
                    echo '<a href="post.php?id=' . $row['id'] . '">';
                    echo '<div class="card-image" style="background-image: url(\'' . $image_url . '\')"></div>';
                    echo '<div class="card-content">';
                    echo "<h3>$title</h3>";
                    echo "<p class='card-excerpt'>$content_excerpt</p>";
                    echo '<div class="card-footer">';
                    echo '<span class="read-more">See more</span>';
                    echo '<span class="author">By ' . $author . '</span>';
                    echo '</div>';
                    echo '</div>';
                    echo '</a>';
                    echo '</div>';
                }
            } else {
                echo "<p class='no-posts'>No posts found.</p>";
            }
            ?>
        </div>
        
        <!-- Pagination controls -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php 
            // Previous button
            if ($current_page > 1) {
                $prev_link = '?page=' . ($current_page - 1);
                if (isset($_GET['search'])) {
                    $prev_link .= '&search=' . urlencode($_GET['search']);
                }
                echo '<a href="' . $prev_link . '" class="page-btn">&laquo; Previous</a>';
            }
            
            // Page numbers
            for ($i = 1; $i <= $total_pages; $i++) {
                $page_link = '?page=' . $i;
                if (isset($_GET['search'])) {
                    $page_link .= '&search=' . urlencode($_GET['search']);
                }
                echo '<a href="' . $page_link . '" class="page-number ' . ($i == $current_page ? 'active' : '') . '">' . $i . '</a>';
            }
            
            // Next button
            if ($current_page < $total_pages) {
                $next_link = '?page=' . ($current_page + 1);
                if (isset($_GET['search'])) {
                    $next_link .= '&search=' . urlencode($_GET['search']);
                }
                echo '<a href="' . $next_link . '" class="page-btn">Next &raquo;</a>';
            }
            ?>
        </div>
        <?php endif; ?>
    </div>
    <script>
const toggleButton = document.getElementById('darkModeToggle');
const body = document.body;

// Load saved mode
if (localStorage.getItem('darkMode') === 'enabled') {
    body.classList.add('dark-mode');
}

toggleButton.addEventListener('click', () => {
    body.classList.toggle('dark-mode');
    if (body.classList.contains('dark-mode')) {
        localStorage.setItem('darkMode', 'enabled');
    } else {
        localStorage.removeItem('darkMode');
    }
});
</script>
</body>
</html>