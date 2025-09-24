<?php session_start(); ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>MovieHaven</title>
  <link rel="stylesheet" href="css/main.css">
</head>
<body>
  <header class="topbar">
    <h1 class="brand">MovieHaven</h1>
    <div class="controls">
      <input id="searchInput" type="search" placeholder="Search movies by title...">
      <select id="genreSelect">
        <option>All</option>
        <option>Sci-Fi</option>
        <option>Drama</option>
        <option>Action</option>
      </select>
      <?php if(isset($_SESSION['user_id'])): ?>
        <a href="logout.php" class="btn">Logout</a>
      <?php else: ?>
        <a href="login.php" class="btn">Login</a>
        <a href="signup.php" class="btn">Sign up</a>
      <?php endif; ?>
    </div>
  </header>

  <main>
    <section id="recommendations" class="recommendations">
      <h2>Recommended by genre</h2>
      <div class="recommendation-list">
        <!-- optional static recommendations, or generated dynamically -->
        <div class="rec-card">Top Sci-Fi Picks</div>
        <div class="rec-card">Feel-good Dramas</div>
        <div class="rec-card">Action Essentials</div>
      </div>
    </section>

    <section id="movieGrid" class="grid"></section>
  </main>

  <!-- movie detail modal -->
  <div id="modal" class="modal hidden">
    <div class="modal-content">
      <button id="closeModal">Close</button>
      <div id="modalBody"></div>
    </div>
  </div>

  <script>const USER_ID = <?= isset($_SESSION['user_id'])?json_encode($_SESSION['user_id']):'null' ?>;</script>
  <script src="js/main.js"></script>
</body>
</html>
