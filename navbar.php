<div class="navbar">
    <div class="nav-left">
        <a href="index.php" class="logo">B-LOGS</a>
    </div>
    <div class="nav-right">
        <a href="index.php">Home</a>
        <?php if (isset($_SESSION['user_id'])): ?>
        <a href="create.php">Create Post</a>
        <?php endif; ?>
        <a href="explore-ai.php">Explore AI ✨</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['is_admin'] == 1): ?>
                <a href="mod.php" class="admin-link">Mod Panel</a>
            <?php endif; ?>
            <a href="profile.php"><?php echo htmlspecialchars($_SESSION['username']); ?></a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
        <form action="index.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search blogs...">
            <button type="submit">Search</button>
        </form>
        <!-- Dark Mode Toggle Button -->
        <button id="theme-toggle" class="btn">Toggle Dark Mode</button>
    </div>
</div>

<script>
  // Function to apply dark mode class
  function applyDarkMode(enabled) {
    if (enabled) {
      document.body.classList.add('dark-mode');
    } else {
      document.body.classList.remove('dark-mode');
    }
  }

  // Load saved preference from localStorage
  const savedPreference = localStorage.getItem('dark-mode');

  if (savedPreference === 'true') {
    applyDarkMode(true);
  } else if (savedPreference === 'false') {
    applyDarkMode(false);
  } else {
    // No saved preference, detect system preference
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    applyDarkMode(prefersDark);
  }

  // Toggle the dark mode on button click, save preference
  document.getElementById('theme-toggle').addEventListener('click', function() {
    const isDark = document.body.classList.toggle('dark-mode');
    localStorage.setItem('dark-mode', isDark ? 'true' : 'false');
  });
</script>
