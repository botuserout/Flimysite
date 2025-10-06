<?php
session_start();
require_once 'api/db.php';

// Fetch top 10 popular movies by average rating
$topStmt = $pdo->query("SELECT m.*, IFNULL(AVG(r.rating),0) AS avg_rating
    FROM movies m
    LEFT JOIN ratings r ON r.movie_id = m.id
    GROUP BY m.id
    ORDER BY avg_rating DESC
    LIMIT 10");
$topMovies = $topStmt->fetchAll();

// Get search query and genre filter from URL parameters
$search_query = $_GET['search'] ?? '';
$genre_filter = $_GET['genre'] ?? '';

// Fetch movies from database with search and genre filtering
$sql = "SELECT m.*, 
        (SELECT IFNULL(ROUND(AVG(rating),1),0) FROM ratings WHERE movie_id = m.id) AS avg_rating,
        (SELECT COUNT(*) FROM ratings WHERE movie_id = m.id) AS rating_count
        FROM movies m WHERE 1";

$params = [];
if (!empty($search_query)) {
    $sql .= " AND m.title LIKE ?";
    $params[] = "%$search_query%";
}
if (!empty($genre_filter) && $genre_filter !== 'All') {
    $sql .= " AND m.genre = ?";
    $params[] = $genre_filter;
}
$sql .= " ORDER BY m.title LIMIT 20";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movies = $stmt->fetchAll();

// Get unique genres for filter dropdown
$genres_stmt = $pdo->query("SELECT DISTINCT genre FROM movies WHERE genre IS NOT NULL ORDER BY genre");
$genres = $genres_stmt->fetchAll(PDO::FETCH_COLUMN);

// Debug: Log genres and movie count
error_log("Available genres: " . implode(', ', $genres));
error_log("Total movies found: " . count($movies));
?>
<!doctype html>
<html lang="en">
<head>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Gtv9Q9F0rJ4p5r9U1t5gZJb8B7e1JpN96CB2A+qsYqS+8CA0nVddOZXS6jttuPAo" crossorigin="anonymous">

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MoviesHeaven - Discover Your Next Favorite Movie</title>
  <link rel="stylesheet" href="css/main.css">
</head>
<body>
  <header class="topbar">
    <h1 class="brand">🎬 FlimyHeaven</h1>
    <nav class="nav-links">
        <a href="index.php" class="nav-link">Home</a>
        <a href="ranking.php" class="nav-link">Rankings</a>
      <?php if(isset($_SESSION['user_id'])): ?>
        <a href="watchlist.php" class="nav-link">Watchlist</a>
      <?php endif; ?>
             <?php if(isset($_SESSION['user_id']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
               <a href="admin.php" class="nav-link">Admin</a>

             <?php endif; ?>
    </nav>
    <div class="controls">
      <form method="GET" action="index.php" class="search-form">
        <input name="search" type="search" placeholder="Search movies..." value="<?= htmlspecialchars($search_query) ?>">
        <button type="submit">🔍</button>
      </form>
      <?php if(isset($_SESSION['user_id'])): ?>
        <span class="user-greeting">Welcome, <?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>!</span>
        <a href="logout.php" class="btn">Logout</a>
      <?php else: ?>
        <a href="login.php" class="nav-link" style="background:linear-gradient(90deg,#124E66 60%,#0a3475 100%);color:#fff !important;border-radius:7px;padding:0.5em 1.3em;font-weight:600;box-shadow:0 2px 8px #124e6612;transition:background 0.22s,color 0.22s,transform 0.18s;display:inline-block;margin-right:8px;position:relative;overflow:hidden;" onmouseover="this.style.background='linear-gradient(90deg,#0a3475 60%,#124E66 100%)';this.style.transform='translateY(-2px) scale(1.03)';" onmouseout="this.style.background='linear-gradient(90deg,#124E66 60%,#0a3475 100%)';this.style.transform='none';">Login</a>
        <a href="signup.php" class="nav-link" style="background:linear-gradient(90deg,#124E66 60%,#0a3475 100%);color:#fff !important;border-radius:7px;padding:0.5em 1.3em;font-weight:600;box-shadow:0 2px 8px #124e6612;transition:background 0.22s,color 0.22s,transform 0.18s;display:inline-block;position:relative;overflow:hidden;" onmouseover="this.style.background='linear-gradient(90deg,#0a3475 60%,#124E66 100%)';this.style.transform='translateY(-2px) scale(1.03)';" onmouseout="this.style.background='linear-gradient(90deg,#124E66 60%,#0a3475 100%)';this.style.transform='none';">Sign up</a>
      <?php endif; ?>
    </div>
  </header>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-content">
      <h1 class="hero-title">Discover Your Next Favorite Movie</h1>
      <p class="hero-subtitle">Explore a curated collection of cinematic masterpieces and find your next binge-worthy film.</p>
      <button class="hero-btn" onclick="scrollToMovies()">Explore Movies</button>
    </div>
    <div class="hero-overlay"></div>
  </section>

  <main>
    <!-- Top 10 Popular Movies Carousel -->
    <div class="container" style="margin-top:2rem;">
      <h2 style="text-align:center; margin-bottom:1.2rem;">🔥 Top 10 Popular Movies</h2>
      <div class="container" style="margin-top:2rem;">
  <div class="top10-movie-row" style="display:flex;overflow-x:auto;gap:2rem;padding:1rem 0 1.5rem 0;scroll-snap-type:x mandatory;">
    <?php foreach($topMovies as $movie): ?>
      <div class="card shadow-lg border-0" style="min-width:240px;max-width:270px;background:rgba(10, 52, 117, 0.21);border-radius:18px;overflow:hidden;scroll-snap-align:start;">
        <img src="<?= htmlspecialchars($movie['poster_url'] ?: 'https://via.placeholder.com/270x380/1a1a1a/ffffff?text=No+Image') ?>"
             class="card-img-top" style="height:380px;object-fit:cover;border-radius:18px 18px 0 0;">
             <div class="card-body d-flex flex-column align-items-center justify-content-center text-center" 
             style="padding:1rem;min-height:160px;gap:0.6rem;">
  <h5 class="card-title mb-2" style="font-size:1.1rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
    <?= htmlspecialchars($movie['title']) ?>
    <span class="watched-badge" data-movie-id="<?= $movie['id'] ?>" style="margin-left:8px;"></span>
  </h5>
  <div class="rating mb-2" style="font-size:1.18rem;display:flex;align-items:center;justify-content:center;gap:0.4rem;">
    <span class="stars" style="color:#FFD700;font-size:1.3rem;letter-spacing:1px;">
      <?php for($s=1;$s<=5;$s++): ?>
        <span><?= $movie['avg_rating'] >= $s-0.25 ? '★' : '☆' ?></span>
      <?php endfor; ?>
    </span>
    <span style="color:#124E66;font-weight:600;">
      <?= number_format((float)$movie['avg_rating'], 1) ?>
    </span>
  </div>
  <div class="w-100 d-flex justify-content-center mt-2">
    <a href="movie.php?id=<?= $movie['id'] ?>" class="btn" style="background:linear-gradient(135deg,#124E66 0%,#748D92 100%);color:#fff;border-radius:8px;padding:0.45rem 1.1rem;font-size:1rem;font-weight:500;box-shadow:0 2px 8px rgba(33,42,49,0.06);transition:background 0.2s,box-shadow 0.2s;">View Details</a>
  </div>
</div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
    </div>

    <!-- Search and Filter Section -->
    <section class="search-section">
      <div class="search-container">
        <form method="GET" action="index.php" class="search-form-large">
          <input name="search" type="search" placeholder="Search movies..." value="<?= htmlspecialchars($search_query) ?>">
          <select name="genre" id="genreSelect" onchange="filterByGenre()">
            <option value="">All Genres</option>
            <?php foreach($genres as $genre): ?>
              <option value="<?= htmlspecialchars($genre) ?>" <?= $genre_filter === $genre ? 'selected' : '' ?>>
                <?= htmlspecialchars($genre) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button type="submit">Search</button>
        </form>
      </div>
    </section>

    <!-- Movies Section -->
    <section class="recommended-movies" id="movies">
      <h2 class="section-title">
        <?php if(!empty($search_query)): ?>
          Search Results for "<?= htmlspecialchars($search_query) ?>"
        <?php elseif(!empty($genre_filter)): ?>
          <?= htmlspecialchars($genre_filter) ?> Movies
        <?php else: ?>
          Recommended Movies
        <?php endif; ?>
      </h2>
      
      <?php if(empty($movies)): ?>
        <div class="no-results">
          <p>No movies found matching your criteria.</p>
          <a href="index.php" class="btn">View All Movies</a>
        </div>
      <?php else: ?>
        <div class="movie-grid">
          <?php foreach($movies as $movie): ?>
            <div class="movie-card" data-movie-id="<?= $movie['id'] ?>">
              <div class="movie-poster">
                <img src="<?= htmlspecialchars($movie['poster_url'] ?: 'https://via.placeholder.com/300x400/1a1a1a/ffffff?text=No+Image') ?>" 
                     alt="<?= htmlspecialchars($movie['title']) ?>" 
                     loading="lazy">
                <div class="movie-overlay">
                  <button class="view-details-btn" onclick="viewMovieDetails(<?= $movie['id'] ?>)">
                    View Details
                  </button>
                </div>
              </div>
              <div class="movie-info">
                <h3 class="movie-title"><?= htmlspecialchars($movie['title']) ?><span class="watched-badge" data-movie-id="<?= $movie['id'] ?>" style="margin-left:8px;"></span></h3>
                <p class="movie-year"><?= $movie['release_year'] ?></p>
                <p class="movie-genre"><?= htmlspecialchars($movie['genre']) ?></p>
                <div class="movie-rating">
                  <span class="star">⭐</span>
                  <span class="rating-value"><?= number_format($movie['avg_rating'], 1) ?></span>
                  <span class="rating-count">(<?= $movie['rating_count'] ?> ratings)</span>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <!-- Watchlist Section for logged-in user -->
  <?php if(isset($_SESSION['user_id'])): ?>
    <?php
      $watchlist_stmt = $pdo->prepare("SELECT m.* FROM user_movies um JOIN movies m ON um.movie_id = m.id WHERE um.user_id = ? AND um.in_watchlist = 1");
      $watchlist_stmt->execute([$_SESSION['user_id']]);
      $watchlist_movies = $watchlist_stmt->fetchAll();
    ?>
    <?php if(count($watchlist_movies) > 0): ?>
      <section class="recommended-movies" id="user-watchlist">
        <h2 class="section-title">My Watchlist</h2>
        <div class="movie-grid">
          <?php foreach($watchlist_movies as $movie): ?>
            <div class="movie-card" data-movie-id="<?= $movie['id'] ?>">
              <div class="movie-poster">
                <img src="<?= htmlspecialchars($movie['poster_url'] ?: 'https://via.placeholder.com/300x400/1a1a1a/ffffff?text=No+Image') ?>" 
                     alt="<?= htmlspecialchars($movie['title']) ?>" 
                     loading="lazy">
              </div>
              <div class="movie-info">
                <h3 class="movie-title">
                  <a href="movie.php?id=<?= $movie['id'] ?>"><?= htmlspecialchars($movie['title']) ?></a>
                </h3>
                <p class="movie-year"><?= $movie['release_year'] ?></p>
                <p class="movie-genre"><?= htmlspecialchars($movie['genre']) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>
  <?php endif; ?>

  <!-- Footer -->
  <footer class="footer">
    <div class="footer-content">
      <div class="footer-links">
        <a href="#" class="footer-link">MoviesHeaven</a>
        <a href="#" class="footer-link">Rakesh & Rahil</a>
        <a href="#" class="footer-link">IU372 || IU375</a>
      </div>
    </div>
  </footer>

  <!-- Movie detail modal -->
  <div id="modal" class="modal hidden">
    <div class="modal-content">
      <button id="closeModal" class="close-btn">×</button>
      <div id="modalBody"></div>
    </div>
  </div>

  <script>
    // Set USER_ID in global scope before loading main.js
    window.USER_ID = <?= isset($_SESSION['user_id']) ? json_encode($_SESSION['user_id']) : 'null' ?>;
    console.log('USER_ID set to:', window.USER_ID);
if (window.USER_ID) {
  document.addEventListener('DOMContentLoaded', function() {
    // Collect all movie IDs from badge spans
    const badgeSpans = document.querySelectorAll('.watched-badge[data-movie-id]');
    const ids = Array.from(badgeSpans).map(span => span.getAttribute('data-movie-id'));
    if (ids.length === 0) return;
    fetch('api/bulk_watched_status.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: 'movie_ids=' + ids.join(',')
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        badgeSpans.forEach(span => {
          const id = span.getAttribute('data-movie-id');
          if (data.status_map[id] == 1) {
            span.textContent = '👁 Watched';
            span.style.color = 'green';
          } else {
            span.textContent = '⏳ Not Watched';
            span.style.color = '#c00';
          }
        });
      }
    });
  });
}
  </script>
  <script src="js/main.js"></script>
  <!-- Bootstrap 5 JS -->
<?php include 'chatbot.html'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoA6VKHr8BL7gk5r2iE9QcE+zWw5yXv4yFaF5r5Kbrb8+4V" crossorigin="anonymous"></script>
  <script>
    // Verify functions are loaded after main.js
    console.log('Functions available:', {
      viewMovieDetails: typeof window.viewMovieDetails,
      scrollToMovies: typeof window.scrollToMovies
    });
    
    // Genre filtering function
    function filterByGenre() {
      const genreSelect = document.getElementById('genreSelect');
      const selectedGenre = genreSelect.value;
      
      console.log('Filtering by genre:', selectedGenre);
      
      // Show loading indicator
      const moviesSection = document.querySelector('.recommended-movies');
      if (moviesSection) {
        const originalContent = moviesSection.innerHTML;
        moviesSection.innerHTML = '<div style="text-align: center; padding: 20px;"><p>Loading movies...</p></div>';
      }
      
      // Get current search query
      const searchInput = document.querySelector('input[name="search"]');
      const searchQuery = searchInput ? searchInput.value : '';
      
      // Build URL with current search and selected genre
      const url = new URL(window.location.href);
      url.searchParams.set('genre', selectedGenre);
      if (searchQuery) {
        url.searchParams.set('search', searchQuery);
      } else {
        url.searchParams.delete('search');
      }
      
      // Redirect to filtered page
      window.location.href = url.toString();
    }
    
    // Make it globally available
    window.filterByGenre = filterByGenre;
  </script>
</body>
</html>