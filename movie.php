<?php
session_start();
require_once "api/db.php";

// Get movie ID from query parameter
$movie_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch movie details
$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();
$movie = $result->fetch_assoc();

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
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <!-- Navbar -->
    <header class="navbar">
        <div class="logo">Flimysite</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="index.php#genres">Genres</a>
            <a href="contact.php">Contact</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="watchlist.php">My Watchlist</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login/Signup</a>
            <?php endif; ?>
        </nav>
        <form method="get" action="index.php" class="search-bar">
            <input type="text" name="search" placeholder="Search movies...">
        </form>
    </header>

    <!-- Movie Detail Section -->
    <main class="movie-detail">
        <div class="poster">
            <img src="<?php echo htmlspecialchars($movie['poster_url']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
        </div>
        <div class="info">
            <h1><?php echo htmlspecialchars($movie['title']); ?></h1>
            <p><span class="year"><?php echo $movie['year']; ?></span> • <?php echo htmlspecialchars($movie['genre']); ?></p>
            <p class="description"><?php echo nl2br(htmlspecialchars($movie['description'])); ?></p>

            <div class="rating">
                <strong>Rating:</strong>
                <span class="stars">⭐ <?php echo number_format($movie['rating'], 1); ?>/10</span>
            </div>

            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="post" action="api/add_watchlist.php">
                    <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
                    <button type="submit" class="btn">+ Add to Watchlist</button>
                </form>
                <form method="post" action="api/rate_movie.php" class="rate-form">
                    <input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
                    <label for="rating">Rate this movie:</label>
                    <select name="rating" required>
                        <option value="">--Select--</option>
                        <?php for ($i=1; $i<=10; $i++): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                    <button type="submit" class="btn">Submit</button>
                </form>
            <?php else: ?>
                <p><a href="login.php">Login</a> to add to watchlist or rate this movie.</p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>© <?php echo date("Y"); ?> CineSphere. All rights reserved.</p>
        <div class="socials">
            <a href="#"><i class="fab fa-facebook"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-linkedin"></i></a>
        </div>
    </footer>

</body>
</html>
