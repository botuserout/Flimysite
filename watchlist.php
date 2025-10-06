<?php
session_start();
require_once 'api/db.php';

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit(); }
$userId = (int)$_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove'])) {
	$movieId = (int)$_POST['movie_id'];
	$stmt = $pdo->prepare("UPDATE user_movies SET in_watchlist = 0 WHERE user_id = ? AND movie_id = ?");
	$stmt->execute([$userId, $movieId]);
	header('Location: watchlist.php');
	exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
	$movieId = (int)$_POST['movie_id'];
	$stmt = $pdo->prepare("INSERT INTO user_movies (user_id, movie_id, in_watchlist) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE in_watchlist=1");
	$stmt->execute([$userId, $movieId]);
	header('Location: watchlist.php');
	exit();
}

$stmt = $pdo->prepare("SELECT m.*, 
    (SELECT IFNULL(ROUND(AVG(rating),1),0) FROM ratings WHERE movie_id = m.id) AS avg_rating,
    (SELECT COUNT(*) FROM ratings WHERE movie_id = m.id) AS rating_count
FROM user_movies um
JOIN movies m ON um.movie_id = m.id
WHERE um.user_id = ? AND um.in_watchlist = 1
GROUP BY m.id
ORDER BY m.title");
$stmt->execute([$userId]);
$movies = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>My Watchlist - FilmyHaven</title>
	<link rel="stylesheet" href="css/style.css">
	<link rel="stylesheet" href="css/watchlist.css">
	<link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Crimson+Text:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
</head>
<body>
	<header>
		<nav>
			<a href="index.php" class="logo">FilmyHaven</a>
			<ul>
				<li><a href="index.php">Home</a></li>
				<li><a href="login.php" class="nav-link" style="background:linear-gradient(90deg,#124E66 60%,#0a3475 100%);color:#fff !important;border-radius:7px;padding:0.5em 1.3em;font-weight:600;box-shadow:0 2px 8px #124e6612;transition:background 0.22s,color 0.22s,transform 0.18s;display:inline-block;margin-right:8px;position:relative;overflow:hidden;" onmouseover="this.style.background='linear-gradient(90deg,#0a3475 60%,#124E66 100%)';this.style.transform='translateY(-2px) scale(1.03)';" onmouseout="this.style.background='linear-gradient(90deg,#124E66 60%,#0a3475 100%)';this.style.transform='none';">Login</a></li>
				<li><a href="signup.php" class="nav-link" style="background:linear-gradient(90deg,#124E66 60%,#0a3475 100%);color:#fff !important;border-radius:7px;padding:0.5em 1.3em;font-weight:600;box-shadow:0 2px 8px #124e6612;transition:background 0.22s,color 0.22s,transform 0.18s;display:inline-block;position:relative;overflow:hidden;" onmouseover="this.style.background='linear-gradient(90deg,#0a3475 60%,#124E66 100%)';this.style.transform='translateY(-2px) scale(1.03)';" onmouseout="this.style.background='linear-gradient(90deg,#124E66 60%,#0a3475 100%)';this.style.transform='none';">Sign up</a></li>
				<li><a href="watchlist.php" class="active">Watchlist</a></li>
				<?php if (!empty($_SESSION['is_admin'])): ?><li><a href="admin.php">Admin Panel</a></li><?php endif; ?>
				<li><a href="logout.php">Sign Out</a></li>
			</ul>
		</nav>
	</header>
    <main>
        <section class="hero hero-compact">
			<h1>My Watchlist</h1>
			<p>Films you've saved for later</p>
		</section>

        <section class="featured-movies compact">
			<div class="movie-grid">
				<?php foreach ($movies as $movie): ?>
					<div class="movie-card">
						<a href="movie.php?id=<?php echo $movie['id']; ?>">
							<img src="<?php echo htmlspecialchars($movie['poster_url'] ?: 'assets/images/default-poster.jpg'); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
						</a>
						<div class="movie-info">
							<div class="rating">
								<span class="stars">★★★★★</span>
								<span><?php echo number_format((float)$movie['avg_rating'], 1); ?></span>
							</div>
							<form method="POST" style="margin-top:8px;display:flex;gap:8px;">
								<input type="hidden" name="movie_id" value="<?php echo $movie['id']; ?>">
								<button class="btn" type="button" onclick="location.href='movie.php?id=<?php echo $movie['id']; ?>'">Open</button>
								<button class="btn" name="remove" value="1">Remove</button>
							</form>
						</div>
					</div>
				<?php endforeach; ?>
				<?php if (empty($movies)): ?>
					<p style="grid-column:1/-1;text-align:center">You have no films saved. Explore the <a href="index.php">collection</a>.</p>
				<?php endif; ?>
			</div>
		</section>
	</main>

	<footer>
		<p>&copy;  FilmyHaven. IU372 || IU375.</p>
	</footer>
</body>
</html>
