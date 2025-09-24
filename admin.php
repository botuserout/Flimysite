<?php
session_start();
require_once "db.php";

// Delete Movie
if (isset($_GET['delete_movie'])) {
    $movie_id = intval($_GET['delete_movie']);
    $conn->query("DELETE FROM movies WHERE id=$movie_id");
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

    $stmt = $conn->prepare("UPDATE movies SET title=?, year=?, genre=?, description=?, poster_url=?, rating=? WHERE id=?");
    $stmt->bind_param("sisssdi", $title, $year, $genre, $description, $poster_url, $rating, $id);
    $stmt->execute();

    header("Location: admin.php?tab=movies&updated=1");
    exit;
}

// Fetch data
$users = $conn->query("SELECT * FROM users");
$movies = $conn->query("SELECT * FROM movies");

$currentTab = $_GET['tab'] ?? 'users';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: url('https://source.unsplash.com/1920x1080/?cinema,movies') no-repeat center center/cover;
      backdrop-filter: blur(10px);
      margin: 0; padding: 0;
      color: #fff;
    }
    .glass-container {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      padding: 20px;
      margin: 40px auto;
      width: 90%;
      max-width: 1100px;
      box-shadow: 0 4px 30px rgba(0,0,0,0.3);
    }
    nav {
      display: flex;
      justify-content: space-around;
      padding: 15px;
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(6px);
      border-radius: 12px;
      margin-bottom: 20px;
    }
    nav a {
      color: #fff;
      text-decoration: none;
      font-weight: bold;
      transition: 0.3s;
    }
    nav a:hover { color: #ffcc00; }
    table {
      width: 100%; border-collapse: collapse;
    }
    th, td {
      padding: 12px; border-bottom: 1px solid rgba(255,255,255,0.2);
    }
    th { text-align: left; }
    .btn {
      padding: 6px 12px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      margin-right: 5px;
      transition: 0.3s;
    }
    .edit-btn { background: #3498db; color: #fff; }
    .delete-btn { background: #e74c3c; color: #fff; }
    .btn:hover { opacity: 0.8; }
    /* Modal Styling */
    .modal {
      display: none; position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.7);
      justify-content: center; align-items: center;
    }
    .modal-content {
      background: rgba(255, 255, 255, 0.15);
      backdrop-filter: blur(10px);
      border-radius: 16px;
      padding: 20px;
      width: 400px;
      color: #fff;
      box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    }
    .modal input, .modal textarea {
      width: 100%; margin-bottom: 10px;
      padding: 8px; border-radius: 8px;
      border: none; outline: none;
    }
    .close-btn {
      background: #e74c3c;
      color: #fff;
      padding: 6px 12px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      float: right;
    }
  </style>
</head>
<body>
  <div class="glass-container">
    <h1>Admin Panel</h1>
    <nav>
      <a href="?tab=users">👤 User Inspection</a>
      <a href="?tab=movies">🎬 Movie Management</a>
    </nav>

    <?php if ($currentTab == 'users'): ?>
      <h2>Registered Users</h2>
      <table>
        <tr><th>ID</th><th>Name</th><th>Email</th></tr>
        <?php while($u = $users->fetch_assoc()): ?>
          <tr>
            <td><?= $u['id'] ?></td>
            <td><?= $u['name'] ?></td>
            <td><?= $u['email'] ?></td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php elseif ($currentTab == 'movies'): ?>
      <h2>Movie List</h2>
      <table>
        <tr><th>ID</th><th>Title</th><th>Year</th><th>Genre</th><th>Actions</th></tr>
        <?php while($m = $movies->fetch_assoc()): ?>
          <tr>
            <td><?= $m['id'] ?></td>
            <td><?= $m['title'] ?></td>
            <td><?= $m['year'] ?></td>
            <td><?= $m['genre'] ?></td>
            <td>
              <button class="btn edit-btn" onclick="openModal(<?= htmlspecialchars(json_encode($m)) ?>)">Edit</button>
              <a href="?delete_movie=<?= $m['id'] ?>" class="btn delete-btn" onclick="return confirm('Delete this movie?')">Delete</a>
            </td>
          </tr>
        <?php endwhile; ?>
      </table>
    <?php endif; ?>
  </div>

  <!-- Modal for Editing -->
  <div class="modal" id="editModal">
    <div class="modal-content">
      <button class="close-btn" onclick="closeModal()">X</button>
      <h3>Edit Movie</h3>
      <form method="POST">
        <input type="hidden" name="id" id="movieId">
        <input type="text" name="title" id="movieTitle" placeholder="Title" required>
        <input type="number" name="year" id="movieYear" placeholder="Year" required>
        <input type="text" name="genre" id="movieGenre" placeholder="Genre" required>
        <textarea name="description" id="movieDesc" placeholder="Description" rows="4" required></textarea>
        <input type="text" name="poster_url" id="moviePoster" placeholder="Poster URL" required>
        <input type="number" step="0.1" name="rating" id="movieRating" placeholder="Rating" required>
        <button type="submit" name="update_movie" class="btn edit-btn">Update</button>
      </form>
    </div>
  </div>

  <script>
    function openModal(movie) {
      document.getElementById('editModal').style.display = 'flex';
      document.getElementById('movieId').value = movie.id;
      document.getElementById('movieTitle').value = movie.title;
      document.getElementById('movieYear').value = movie.year;
      document.getElementById('movieGenre').value = movie.genre;
      document.getElementById('movieDesc').value = movie.description;
      document.getElementById('moviePoster').value = movie.poster_url;
      document.getElementById('movieRating').value = movie.rating;
    }
    function closeModal() {
      document.getElementById('editModal').style.display = 'none';
    }
  </script>
</body>
</html>
