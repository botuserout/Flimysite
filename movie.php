<?php
session_start();
require_once "api/db.php";

// Get movie ID from query parameter
$movie_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch movie details with rating
$stmt = $pdo->prepare("
    SELECT m.*, 
           (SELECT IFNULL(ROUND(AVG(rating),1),0) FROM ratings WHERE movie_id = m.id) AS avg_rating,
           (SELECT COUNT(*) FROM ratings WHERE movie_id = m.id) AS rating_count
    FROM movies m 
    WHERE m.id = ?
");
$stmt->execute([$movie_id]);
$movie = $stmt->fetch();

if (!$movie) {
    echo "<h2>Movie not found!</h2>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($movie['title']); ?> - MovieHaven</title>
    <link rel="stylesheet" href="css/main.css">
</head>
<body>

    <!-- Navbar -->
    <header class="navbar">
        <div class="logo">FlimyhHeaven🎥</div>
        <nav>
            <a href="index.php">Home</a
        </nav>
    </header>

    <!-- Movie Detail Section -->
    <main class="movie-detail">
        <div class="poster">
            <img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
        </div>
        <div class="info">
            <h1><?php echo htmlspecialchars($movie['title']); ?></h1>
            <p><span class="year"><?php echo $movie['release_year']; ?></span> • <?php echo htmlspecialchars($movie['genre']); ?></p>
            <p class="description"><?php echo nl2br(htmlspecialchars($movie['description'])); ?></p>

            <div class="current-rating">
                <strong>Current Rating:</strong> <?php echo number_format($movie['avg_rating'], 1); ?>/5 
                <span class="rating-count">(<?php echo $movie['rating_count']; ?> ratings)</span>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="rating-section">
                    <h3>Rate this movie:</h3>
                    <div class="star-rating">
                        <button class="star-btn" onclick="rateMovie(<?= $movie['id'] ?>, 1)">⭐</button>
                        <button class="star-btn" onclick="rateMovie(<?= $movie['id'] ?>, 2)">⭐</button>
                        <button class="star-btn" onclick="rateMovie(<?= $movie['id'] ?>, 3)">⭐</button>
                        <button class="star-btn" onclick="rateMovie(<?= $movie['id'] ?>, 4)">⭐</button>
                        <button class="star-btn" onclick="rateMovie(<?= $movie['id'] ?>, 5)">⭐</button>
                    </div>
                    <p class="rating-help">Click a star to rate (1-5 stars)</p>
                </div>
            <?php else: ?>
                <div class="rating-section">
                    <p><a href="login.php">Login to rate this movie</a></p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>© <?php echo date("Y"); ?> FlimyHeaven.RAKESH & RAHIL.</p>
    </footer>s

    <script>
        window.USER_ID = <?= isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null' ?>;
        console.log('Movie page USER_ID set to:', window.USER_ID);
    </script>
    <script src="js/main.js"></script>
</body>
</html>
