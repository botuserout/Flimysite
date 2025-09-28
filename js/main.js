// js/main.js

// Global variables for index.php functionality
window.USER_ID = window.USER_ID || null;

// Global functions that need to be accessible from HTML onclick handlers
window.scrollToMovies = function() {
  const moviesSection = document.querySelector('.recommended-movies');
  if (moviesSection) {
    moviesSection.scrollIntoView({
      behavior: 'smooth'
    });
  }
};

window.viewMovieDetails = function(movieId) {
  window.location.href = `movie.php?id=${movieId}`;
};

// Rating functionality
window.rateMovie = async function(movieId, rating) {
  console.log('=== RATING DEBUG START ===');
  console.log('Rating movie:', movieId, 'with rating:', rating);
  console.log('window.USER_ID:', window.USER_ID);
  console.log('typeof window.USER_ID:', typeof window.USER_ID);
  
  if (!window.USER_ID || window.USER_ID === 'null' || window.USER_ID === null) {
    console.log('No USER_ID found, redirecting to login');
    alert('Please login to rate movies');
    window.location.href = 'login.php';
    return;
  }
  
  try {
    const response = await fetch('api/rate_movie.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        movie_id: movieId,
        rating: rating
      })
    });
    
    const result = await response.json();
    
    if (result.success) {
      // Update the rating display on the page
      updateRatingDisplay(movieId, result.avg_rating, result.rating_count);
      alert('Rating saved! Thank you for your feedback.');
    } else {
      alert(result.message || 'Failed to save rating');
    }
  } catch (error) {
    console.error('Rating error:', error);
    alert('An error occurred while saving your rating');
  }
  console.log('=== RATING DEBUG END ===');
};

// Update rating display on the page
function updateRatingDisplay(movieId, avgRating, ratingCount) {
  // Update rating in movie cards
  const movieCard = document.querySelector(`[data-movie-id="${movieId}"]`);
  if (movieCard) {
    const ratingElement = movieCard.querySelector('.movie-rating .rating-value');
    const countElement = movieCard.querySelector('.movie-rating .rating-count');
    
    if (ratingElement) ratingElement.textContent = avgRating;
    if (countElement) countElement.textContent = `(${ratingCount} ratings)`;
  }
  
  // Update rating in modal if open
  const modal = document.getElementById('modal');
  if (modal && !modal.classList.contains('hidden')) {
    const modalRating = modal.querySelector('.current-rating');
    if (modalRating) {
      modalRating.textContent = `Current Rating: ${avgRating}/5 (${ratingCount} ratings)`;
    }
  }
}


document.addEventListener('DOMContentLoaded', () => {
  // Get elements that might exist on different pages
  const grid = document.getElementById('movieGrid');
  const search = document.getElementById('searchInput');
  const genre = document.getElementById('genreSelect');
  const modal = document.getElementById('modal');
  const modalBody = document.getElementById('modalBody');
  const closeModal = document.getElementById('closeModal');
  
  // Debug: Log which elements are found
  console.log('Elements found:', {
    grid: !!grid,
    search: !!search,
    genre: !!genre,
    modal: !!modal,
    modalBody: !!modalBody,
    closeModal: !!closeModal
  });

  async function loadMovies() {
    if (!search || !genre) return; // Exit if elements don't exist
    const q = encodeURIComponent(search.value.trim());
    const g = encodeURIComponent(genre.value);
    const res = await fetch(`api/fetch_movies.php?q=${q}&genre=${g}&limit=100`);
    const movies = await res.json();
    renderMovies(movies);
  }

  function renderMovies(movies){
    if (!grid) return; // Exit if grid doesn't exist (not on index page)
    
    grid.innerHTML = '';
    movies.forEach(m => {
      const el = document.createElement('div');
      el.className = 'card';
      el.innerHTML = `
        <img src="${m.poster_url || 'assets/placeholder.jpg'}" alt="">
        <div class="card-body">
          <h3>${m.title}</h3>
          <p class="meta">${m.genre} • ${m.release_year || ''}</p>
          <p class="desc">${m.description ? m.description.slice(0,120) + '...' : ''}</p>
          <div class="card-actions">
            <button data-id="${m.id}" class="details-btn">Details</button>
            <button data-id="${m.id}" class="watch-btn">+ Watchlist</button>
            <div class="rating">Avg: ${m.avg_rating || 0} (${m.rating_count || 0})</div>
          </div>
        </div>
      `;
      grid.appendChild(el);
    });

           // attach handlers
           document.querySelectorAll('.details-btn').forEach(b => b.onclick = showDetails);
  }

  async function showDetails(e) {
    const id = e.target.dataset.id;
    const res = await fetch(`api/movie_detail.php?id=${id}`);
    const m = await res.json();
    
    // Get current rating for this movie
    const ratingRes = await fetch(`api/get_movie_rating.php?id=${id}`);
    const ratingData = await ratingRes.json();
    
    modalBody.innerHTML = `
      <div class="movie-detail-modal">
        <div class="movie-poster-section">
          <img src="${m.poster_url || 'assets/placeholder.jpg'}" alt="${m.title}" class="modal-poster">
        </div>
        <div class="movie-info-section">
          <h2>${m.title}</h2>
          <p class="movie-year">${m.release_year || 'N/A'}</p>
          <p class="movie-genre">${m.genre || 'N/A'}</p>
          <div class="current-rating">Current Rating: ${ratingData.avg_rating || 0}/5 (${ratingData.rating_count || 0} ratings)</div>
          <p class="movie-description">${m.description || 'No description available.'}</p>
          
          ${window.USER_ID ? `
            <div class="rating-section">
              <h3>Rate this movie:</h3>
              <div class="star-rating">
                <button class="star-btn" data-rating="1" onclick="rateMovie(${m.id}, 1)">⭐</button>
                <button class="star-btn" data-rating="2" onclick="rateMovie(${m.id}, 2)">⭐</button>
                <button class="star-btn" data-rating="3" onclick="rateMovie(${m.id}, 3)">⭐</button>
                <button class="star-btn" data-rating="4" onclick="rateMovie(${m.id}, 4)">⭐</button>
                <button class="star-btn" data-rating="5" onclick="rateMovie(${m.id}, 5)">⭐</button>
              </div>
              <p class="rating-help">Click a star to rate (1-5 stars)</p>
            </div>
          ` : `
            <div class="rating-section">
              <p><a href="login.php">Login to rate this movie</a></p>
            </div>
          `}
        </div>
      </div>
    `;
    modal.classList.remove('hidden');
  }



  // Modal functionality
  if (modal && closeModal) {
    closeModal.addEventListener('click', function() {
      modal.classList.add('hidden');
    });
    
    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        modal.classList.add('hidden');
      }
    });
  }

  // Event listeners for search and genre (only if elements exist and are valid)
  if (search && search !== null && typeof search.addEventListener === 'function') {
    try {
      search.addEventListener('input', debounce(loadMovies, 300));
      console.log('Search input listener attached');
    } catch (error) {
      console.error('Error attaching search listener:', error);
    }
  } else {
    console.log('Search input not found or not attachable');
  }
  
  if (genre && genre !== null && typeof genre.addEventListener === 'function') {
    try {
      genre.addEventListener('change', loadMovies);
      console.log('Genre select listener attached');
    } catch (error) {
      console.error('Error attaching genre listener:', error);
    }
  } else {
    console.log('Genre select not found or not attachable');
  }

  // Load movies only if grid exists
  if (grid) {
    loadMovies();
  }
});

// simple debounce
function debounce(fn, ms){ let t; return (...a)=>{ clearTimeout(t); t = setTimeout(()=>fn(...a), ms); }; }
