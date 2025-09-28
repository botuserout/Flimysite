<?php 
session_start(); 
require_once "api/db.php";

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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MovieHeaven - Discover Your Next Favorite Movie</title>
  <link rel="stylesheet" href="css/main.css">
</head>
<body>
  <header class="topbar">
    <h1 class="brand">🎬 MovieHeaven</h1>
    <nav class="nav-links">
      <a href="index.php" class="nav-link">Home</a>
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
        <a href="login.php" class="btn">Login</a>
        <a href="signup.php" class="btn">Sign up</a>
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
                <h3 class="movie-title"><?= htmlspecialchars($movie['title']) ?></h3>
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
  </script>
  <script src="js/main.js"></script>
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