<?php
session_start();
require_once "api/db.php";

// Add Movie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_movie'])) {
    $title = $_POST['title'];
    $year = $_POST['year'];
    $genre = $_POST['genre'];
    $description = $_POST['description'];
    $poster_url = $_POST['poster_url'];

    $stmt = $pdo->prepare("INSERT INTO movies (title, release_year, genre, description, poster_url) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $year, $genre, $description, $poster_url]);

    header("Location: admin.php?tab=movies&added=1");
    exit;
}

// Delete Movie
if (isset($_GET['delete_movie'])) {
    $movie_id = intval($_GET['delete_movie']);
    $stmt = $pdo->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->execute([$movie_id]);
    header("Location: admin.php?tab=movies");
    exit;
}

// Update Movie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_movie'])) {
    $id = intval($_POST['id']);
    $title = $_POST['title'];
    $year = $_POST['year'];
    $genre = $_POST['genre'];
    $description = $_POST['description'];
    $poster_url = $_POST['poster_url'];
    $rating = $_POST['rating'];

    $stmt = $pdo->prepare("UPDATE movies SET title=?, release_year=?, genre=?, description=?, poster_url=? WHERE id=?");
    $stmt->execute([$title, $year, $genre, $description, $poster_url, $id]);

    header("Location: admin.php?tab=movies&updated=1");
    exit;
}

// Fetch data
$users_stmt = $pdo->query("SELECT * FROM users");
$users = $users_stmt->fetchAll();

$movies_stmt = $pdo->query("SELECT * FROM movies");
$movies = $movies_stmt->fetchAll();

$currentTab = $_GET['tab'] ?? 'users';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <link rel="stylesheet" href="css/admin.css">
</head>
<body>
  <div class="glass-container">
    <div class="admin-header">
      <h1>Admin Panel</h1>
      <a href="index.php" class="home-btn">🏠 Return to Home</a>
    </div>
    <nav>
      <a href="?tab=users">👤 User Inspection</a>
      <a href="?tab=movies">🎬 Movie Management</a>
    </nav>

    <?php if ($currentTab == 'users'): ?>
      <h2>Registered Users</h2>
      <table>
        <tr><th>ID</th><th>Name</th><th>Email</th></tr>
        <?php foreach($users as $u): ?>
          <tr>
            <td><?= $u['id'] ?></td>
            <td><?= $u['username'] ?></td>
            <td><?= $u['email'] ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php elseif ($currentTab == 'movies'): ?>
      <h2>Movie Management</h2>
      
      <?php if (isset($_GET['added'])): ?>
        <div class="success-message">Movie added successfully!</div>
      <?php endif; ?>
      
      <?php if (isset($_GET['updated'])): ?>
        <div class="success-message">Movie updated successfully!</div>
      <?php endif; ?>
      
      <div class="movie-actions">
        <button class="btn add-btn" onclick="openAddModal()">+ Add New Movie</button>
      </div>
      
      <table>
        <tr><th>ID</th><th>Title</th><th>Year</th><th>Genre</th><th>Actions</th></tr>
        <?php foreach($movies as $m): ?>
          <tr>
            <td><?= $m['id'] ?></td>
            <td><?= $m['title'] ?></td>
            <td><?= $m['release_year'] ?></td>
            <td><?= $m['genre'] ?></td>
            <td>
              <button class="btn edit-btn" onclick="openModal(<?= htmlspecialchars(json_encode($m)) ?>)">Edit</button>
              <a href="?delete_movie=<?= $m['id'] ?>" class="btn delete-btn" onclick="return confirm('Delete this movie?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php endif; ?>
  </div>

  <!-- Modal for Adding New Movie -->
  <div class="modal" id="addModal">
    <div class="modal-content">
      <button id="closeAddModal" class="close-btn" onclick="closeAddModal()">×</button>
      <h3>Add New Movie</h3>
      <form method="POST">
        <input type="text" name="title" placeholder="Movie Title" required>
        <input type="number" name="year" placeholder="Release Year" required>
        <input type="text" name="genre" placeholder="Genre" required>
        <textarea name="description" placeholder="Description" rows="4" required></textarea>
        <input type="url" name="poster_url" placeholder="Poster URL" required>
        <button type="submit" name="add_movie" class="btn add-btn">Add Movie</button>
      </form>
    </div>
  </div>

  <!-- Modal for Editing -->
  <div class="modal" id="editModal">
    <div class="modal-content">
      <button id="closeModal" class="close-btn" onclick="closeModal()">X</button>
      <h3>Edit Movie</h3>
      <form method="POST">
        <input type="hidden" name="id" id="movieId">
        <input type="text" name="title" id="movieTitle" placeholder="Title" required>
        <input type="number" name="year" id="movieYear" placeholder="Year" required>
        <input type="text" name="genre" id="movieGenre" placeholder="Genre" required>
        <textarea name="description" id="movieDesc" placeholder="Description" rows="4" required></textarea>
        <input type="text" name="poster_url" id="moviePoster" placeholder="Poster URL" required>
        <button type="submit" name="update_movie" class="btn edit-btn">Update</button>
      </form>
    </div>
  </div>

  <script>
    function openAddModal() {
      document.getElementById('addModal').style.display = 'flex';
    }
    
    function closeAddModal() {
      document.getElementById('addModal').style.display = 'none';
    }
    
    function openModal(movie) {
      document.getElementById('editModal').style.display = 'flex';
      document.getElementById('movieId').value = movie.id;
      document.getElementById('movieTitle').value = movie.title;
      document.getElementById('movieYear').value = movie.release_year;
      document.getElementById('movieGenre').value = movie.genre;
      document.getElementById('movieDesc').value = movie.description;
      document.getElementById('moviePoster').value = movie.poster_url;
    }
    
    function closeModal() {
      document.getElementById('editModal').style.display = 'none';
    }
    
    // Close modals when clicking outside
    window.onclick = function(event) {
      const addModal = document.getElementById('addModal');
      const editModal = document.getElementById('editModal');
      
      if (event.target === addModal) {
        addModal.style.display = 'none';
      }
      if (event.target === editModal) {
        editModal.style.display = 'none';
      }
    }
  </script>
</body>
</html>
