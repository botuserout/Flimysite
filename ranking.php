<?php
session_start();
require_once 'api/db.php';

// Pagination setup
$perPage = 16;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $perPage;

// Minimum votes required (m)
$m = 10;

// Get C = mean vote across all movies
$cStmt = $pdo->query("SELECT AVG(avg_rating) as C FROM (
    SELECT movie_id, AVG(rating) as avg_rating FROM ratings GROUP BY movie_id
) as sub");
$C = round($cStmt->fetchColumn(), 2);

// Fetch all movies with their average rating and vote count
$sql = "SELECT m.*, 
            IFNULL(AVG(r.rating),0) as R, 
            COUNT(r.rating) as v
        FROM movies m
        LEFT JOIN ratings r ON m.id = r.movie_id
        GROUP BY m.id
        HAVING v >= 1
        ORDER BY m.title";
$stmt = $pdo->query($sql);
$movies = $stmt->fetchAll();

// Calculate weighted rating for each movie
foreach ($movies as &$movie) {
    $R = floatval($movie['R']);
    $v = intval($movie['v']);
    // Weighted Rating (IMDB formula)
    $movie['weighted_rating'] = ($v / ($v + $m)) * $R + ($m / ($v + $m)) * $C;
}
// Sort movies by weighted_rating desc
usort($movies, function($a, $b) {
    return $b['weighted_rating'] <=> $a['weighted_rating'];
});

// Pagination
$totalMovies = count($movies);
$totalPages = ceil($totalMovies / $perPage);
$movies = array_slice($movies, $offset, $perPage);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Movie Rankings - MoviesHeaven</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/nav_anim.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="topbar">
        <h1 class="brand">🎬 FlimyHeaven</h1>
        <nav class="nav-links" style="
    display: flex;
    gap: 2rem;
    align-items: center;
">
    <a href="index.php" class="nav-link" style="
        color: #fff;
        text-decoration: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        background: linear-gradient(90deg, #124E66 60%, #0a3475 100%);
        box-shadow: 0 4px 15px #124e6612;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        border: none;
        font-weight: 500;
    ">Home</a>
    
    <a href="ranking.php" class="nav-link active" style="
        color: #fff;
        text-decoration: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        background: linear-gradient(90deg, #124E66 60%, #0a3475 100%);
        box-shadow: 0 4px 15px #124e6612;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        border: none;
        font-weight: 500;
    ">Rankings</a>
    
    <?php if(isset($_SESSION['user_id'])): ?>
        <a href="watchlist.php" class="nav-link" style="
            color: #fff;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            background: linear-gradient(90deg, #124E66 60%, #0a3475 100%);
            box-shadow: 0 4px 15px #124e6612;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            border: none;
            font-weight: 500;
        ">Watchlist</a>
    <?php endif; ?>
    
    <?php if(isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
        <a href="admin.php" class="nav-link" style="
            color: #fff;
            text-decoration: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            background: linear-gradient(90deg, #124E66 60%, #0a3475 100%);
            box-shadow: 0 4px 15px #124e6612;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            border: none;
            font-weight: 500;
        ">Admin</a>
    <?php endif; ?>
</nav>

<style>
.nav-link::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, #42aaff60 0%, #124E66 100%);
    transition: left 0.5s ease;
    z-index: 1;
}

.nav-link:hover::before {
    left: 0;
}

.nav-link:hover {
    background: linear-gradient(90deg, #0a3475 60%, #124E66 100%);
    transform: translateY(-2px) scale(1.05);
    box-shadow: 0 6px 20px #124e6620;
}

.nav-link span {
    position: relative;
    z-index: 2;
}

.nav-link.active {
    background: linear-gradient(90deg, #0a3475 60%, #124E66 100%);
    box-shadow: 0 4px 15px #124e6620;
}
</style>
        <div class="controls">
            <?php if(isset($_SESSION['user_id'])): ?>
                <span class="user-greeting">Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>!</span>
                <a href="logout.php" class="btn">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn">Login</a>
                <a href="signup.php" class="btn">Sign up</a>
            <?php endif; ?>
        </div>
    </header>
    <main class="container" style="margin-top:2rem;">
        <h2 style="text-align:center; margin-bottom:2rem;">🏆 Movie Rankings</h2>
        <div class="movie-grid">
            <?php foreach($movies as $movie): ?>
                <div class="movie-card" style="background:rgba(10,52,117,0.21);max-width:260px;min-width:220px;">
                    <img src="<?= htmlspecialchars($movie['poster_url'] ?: 'https://via.placeholder.com/270x380/1a1a1a/ffffff?text=No+Image') ?>"
                         alt="<?= htmlspecialchars($movie['title']) ?>"
                         style="width:100%;height:340px;object-fit:cover;border-radius:12px 12px 0 0;">
                    <div class="movie-info" style="padding:1rem;">
                        <h3 class="movie-title" style="font-size:1.1rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            <?= htmlspecialchars($movie['title']) ?>
                        </h3>
                        <div class="rating" style="margin-top:0.5rem;font-size:1rem;">
                            <span style="color:#FFD700;">★</span> <?= number_format($movie['R'], 1) ?>
                            <span style="color:#888;font-size:0.93em;">(<?= $movie['v'] ?> ratings)</span>
                        </div>
                        <div class="weighted-rating" style="margin-top:0.3rem;font-size:1em;font-weight:600;color:#124E66;">
                            Ranking Score: <?= number_format($movie['weighted_rating'], 2) ?>
                        </div>
                        <a href="movie.php?id=<?= $movie['id'] ?>" class="btn" style="margin-top:1rem;background:#124E66;color:#fff;">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav style="margin-top:2rem;text-align:center;">
                <?php for($p=1; $p<=$totalPages; $p++): ?>
                    <a href="ranking.php?page=<?= $p ?>" class="btn" style="margin:0 5px;<?= $p==$page?'background:#124E66;color:#fff;':'' ?>"><?= $p ?></a>
                <?php endfor; ?>
            </nav>
        <?php endif; ?>
    </main>
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-links">
                <a href="#" class="footer-link">MoviesHeaven</a>
                <a href="#" class="footer-link">Rakesh & Rahil</a>
                <a href="#" class="footer-link">IU372 || IU375</a>
            </div>
        </div>
    </footer>
</body>
</html>
