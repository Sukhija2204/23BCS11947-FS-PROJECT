
<!DOCTYPE html>
<html>
<head>
  <title>Contact Us</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <?php include 'navbar.php'; ?>
  <?php include 'db.php'; ?>

  <div class="container">
    <h1>Contact Us</h1>
    <p>Have feedback, suggestions, or want to collaborate? Drop us a message!</p>

    <form method="POST" class="contact-form">
      <input type="text" name="name" placeholder="Your Name" required>
      <input type="email" name="email" placeholder="Your Email" required>
      <textarea name="message" placeholder="Your Message" required></textarea>
      <button type="submit">Send Message</button>
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      $name = mysqli_real_escape_string($conn, $_POST["name"]);
      $email = mysqli_real_escape_string($conn, $_POST["email"]);
      $message = mysqli_real_escape_string($conn, $_POST["message"]);

      $sql = "INSERT INTO messages (name, email, message) VALUES ('$name', '$email', '$message')";
      
      if (mysqli_query($conn, $sql)) {
          echo "<p style='color:green; text-align:center;'>Thank you for reaching out! We’ll get back to you soon.</p>";
      } else {
          echo "<p style='color:red; text-align:center;'>Error: " . mysqli_error($conn) . "</p>";
      }
    }
    ?>
  </div>
</body>
</html>

