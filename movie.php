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
    <title><?php echo htmlspecialchars($movie['title']); ?> - MoviesHaven</title>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/reviews.css">
</head>
<body>

    <!-- Navbar -->
    <header class="navbar">
        <div class="logo">Details🎥</div>
        <nav>
            <a href="index.php" style="display:inline-block;transition:all 0.2s;">
                <span class="logo" style="color:#124E66;transition:all 0.2s;">Home</span>
                <span class="hover-effect" style="background-color:#124E66;width:0;height:2px;transition:all 0.2s;"></span>
            </a>
        </nav>
        <style>
            nav a:hover .hover-effect {
                width: 100%;
            }
        </style>
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
                <span class="rating-count"><?php echo $movie['rating_count']; ?> ratings)</span>
                <?php
                // Show watchlist badge if in watchlist
                $in_watchlist = false;
                if (isset($_SESSION['user_id'])) {
                    $uid = (int)$_SESSION['user_id'];
                    $check_stmt = $pdo->prepare("SELECT in_watchlist FROM user_movies WHERE user_id = ? AND movie_id = ?");
                    $check_stmt->execute([$uid, $movie['id']]);
                    $row = $check_stmt->fetch();
                    if ($row && $row['in_watchlist']) {
                        $in_watchlist = true;
                    }
                }
                if ($in_watchlist) {
                    echo '<span class="watchlist-badge" style="margin-left:10px;color:#124E66;font-weight:bold;font-size:1.1em;"> In Watchlist</span>';
                }
                ?>
            </div>

            <!-- Rating Feature -->
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
            <?php endif; ?>

            <!-- Watched Status Feature -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="watched-status-section" style="margin-top:1rem;">
                    <?php
                    $uid = (int)$_SESSION['user_id'];
                    $watched_stmt = $pdo->prepare("SELECT watched_status FROM watched_movies WHERE user_id = ? AND movie_id = ?");
                    $watched_stmt->execute([$uid, $movie['id']]);
                    $watched_row = $watched_stmt->fetch();
                    $watched_status = $watched_row ? (int)$watched_row['watched_status'] : 0;
                    ?>
                    <div id="watched-toggle-container">
                        <?php if ($watched_status): ?>
                            <span id="watched-icon" style="font-size:1.3em;color:green;">✔ Watched</span>
                        <?php else: ?>
                            <span id="watched-icon" style="font-size:1.3em;color:#c00;">❌ Not Watched</span>
                            <label style="margin-left:1em;">
                                <input type="checkbox" id="watched-checkbox">
                                Have you watched this movie?
                            </label>
                        <?php endif; ?>
                    </div>
                <?php
                // Check if movie is in user's watchlist
                $in_watchlist = false;
                if (isset($_SESSION['user_id'])) {
                    $uid = (int)$_SESSION['user_id'];
                    $check_stmt = $pdo->prepare("SELECT in_watchlist FROM user_movies WHERE user_id = ? AND movie_id = ?");
                    $check_stmt->execute([$uid, $movie['id']]);
                    $row = $check_stmt->fetch();
                    if ($row && $row['in_watchlist']) {
                        $in_watchlist = true;
                    }
                }
                ?>
                <form method="post" action="watchlist.php" style="margin-top:1rem;">
                    <input type="hidden" name="movie_id" value="<?= $movie['id'] ?>">
                    <?php if ($in_watchlist): ?>
                        <button type="submit" name="remove" class="btn">Remove from Watchlist</button>
                    <?php else: ?>
                        <button type="submit" name="add" class="btn">Add to Watchlist</button>
                    <?php endif; ?>
                </form>
            <?php else: ?>
                <div class="rating-section">
                    <p><a href="login.php">Login to rate this movie</a></p>
                </div>
            <?php endif; ?>

            <!-- Reviews Section -->
            <section id="reviews" style="margin-top:2.5rem;">
                <h3>User Reviews</h3>
                <div id="reviews-list" style="margin-bottom:1.2rem;"></div>
                <?php if (isset($_SESSION['user_id'])): ?>
                <form id="review-form" style="display:flex;gap:10px;align-items:flex-start;">
                    <textarea id="review-input" rows="2" style="flex:1;resize:vertical;" maxlength="800" placeholder="Write your review..."></textarea>
                    <button type="submit" class="btn">Submit Review</button>
                </form>
                <?php else: ?>
                    <p><a href="login.php">Login to add a review</a></p>
                <?php endif; ?>
            </section>
        </div>
        <script>
    // Reviews: fetch and submit
    document.addEventListener('DOMContentLoaded', function() {
        const reviewsList = document.getElementById('reviews-list');
        const form = document.getElementById('review-form');
        const input = document.getElementById('review-input');
        function loadReviews() {
            fetch('api/get_reviews.php?movie_id=<?= $movie['id'] ?>')
                .then(r=>r.json())
                .then(data=>{
                    reviewsList.innerHTML = data.length ? data.map(r=>
                        `<div style='margin-bottom:10px;'><b>${r.username||'User'}</b> <span style='color:#888;font-size:0.93em;'>${r.created_at}</span><br>${r.review}</div>`
                    ).join('') : '<p>No reviews yet. Be the first!</p>';
                });
        }
        loadReviews();
        if (form) {
            form.onsubmit = function(e) {
                e.preventDefault();
                if (!input.value.trim()) return;
                fetch('api/add_review.php', {
                    method:'POST',
                    headers:{'Content-Type':'application/x-www-form-urlencoded'},
                    body: `movie_id=<?= $movie['id'] ?>&review=${encodeURIComponent(input.value)}`
                })
                .then(r=>r.json())
                .then(data=>{
                    if (data.success) {
                        input.value = '';
                        loadReviews();
                    }
                });
            }
        }
    });
    </script>

    </main>

    <footer>
        <p> 2022 FlimyHeaven.RAKESH & RAHIL.</p>
    </footer>

    <script>
        window.USER_ID = <?= isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null' ?>;
        console.log('Movie page USER_ID set to:', window.USER_ID);
    </script>
    <script src="js/main.js"></script>
    <script>
    <?php if (isset($_SESSION['user_id'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
        var watchedCheckbox = document.getElementById('watched-checkbox');
        if (watchedCheckbox) {
            watchedCheckbox.addEventListener('change', function() {
                var watched = watchedCheckbox.checked ? 1 : 0;
                fetch('api/watched_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `movie_id=<?= $movie['id'] ?>&watched_status=${watched}`
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        const container = document.getElementById('watched-toggle-container');
                        if (watched) {
                            container.innerHTML = '<span id="watched-icon" style="font-size:1.3em;color:green;">✔ Watched</span>';
                        } else {
                            container.innerHTML = '<span id="watched-icon" style="font-size:1.3em;color:#c00;">❌ Not Watched</span>' +
                                '<label style="margin-left:1em;"><input type="checkbox" id="watched-checkbox"> Have you watched this movie?</label>';
                            document.getElementById('watched-checkbox').addEventListener('change', arguments.callee);
                        }
                    }
                });
            });
        }
    });
    <?php endif; ?>
    </script>
    