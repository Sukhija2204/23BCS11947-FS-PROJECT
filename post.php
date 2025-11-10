<?php
include 'db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Blog Post</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <?php
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $result = $conn->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.id=$id");
            
            if ($result && $result->num_rows > 0) {
                $post = $result->fetch_assoc();
                $title = htmlspecialchars($post['title']);
                $raw_content = str_replace(["\\r\\n", "\\n", "\\r\n", "\r\n"], "\n", $post['content']);
                $content = nl2br(htmlspecialchars($raw_content));
                $author = htmlspecialchars($post['username']);
                $author_id = $post['user_id'];
                $created_at = date('F d, Y', strtotime($post['created_at']));
                
                echo "<div class='full-post'>";
                echo "<h1>$title</h1>";
                echo "<div class='post-meta'>By <a href='author.php?id=$author_id'>$author</a> • $created_at</div>";
                
                if (!empty($post['image_url'])) {
                    echo "<img src='" . $post['image_url'] . "' class='post-image'>";
                }
                
                echo "<div class='post-content'>$content</div>";
                
                // Show edit/delete only if user is author or admin
                if (isset($_SESSION['user_id']) && ($_SESSION['user_id'] == $author_id || $_SESSION['is_admin'] == 1)) {
                    echo "<div class='post-actions'>";
                    echo "<a href='edit.php?id=" . $post['id'] . "' class='btn'>Edit</a>";
                    echo "<a href='delete.php?id=" . $post['id'] . "' class='btn btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>";
                    echo "</div>";
                }
                
                echo "</div>";
            } else {
                echo "<div class='error-message'>Post not found</div>";
                echo "<a href='index.php' class='btn'>Return to Home</a>";
            }
        } else {
            header("Location: index.php");
            exit;
        }
        ?>
    </div>
</body>
</html>