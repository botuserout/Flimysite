// js/main.js
document.addEventListener('DOMContentLoaded', () => {
  const grid = document.getElementById('movieGrid');
  const search = document.getElementById('searchInput');
  const genre = document.getElementById('genreSelect');
  const modal = document.getElementById('modal');
  const modalBody = document.getElementById('modalBody');
  const closeModal = document.getElementById('closeModal');

  async function loadMovies() {
    const q = encodeURIComponent(search.value.trim());
    const g = encodeURIComponent(genre.value);
    const res = await fetch(`api/fetch_movies.php?q=${q}&genre=${g}&limit=100`);
    const movies = await res.json();
    renderMovies(movies);
  }

  function renderMovies(movies){
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
    document.querySelectorAll('.watch-btn').forEach(b => b.onclick = addWatchlist);
  }

  async function showDetails(e) {
    const id = e.target.dataset.id;
    const res = await fetch(`api/movie_detail.php?id=${id}`);
    const m = await res.json();
    modalBody.innerHTML = `
      <h2>${m.title}</h2>
      <img src="${m.poster_url || 'assets/placeholder.jpg'}" style="max-width:200px;">
      <p>${m.description}</p>
      <div>
        <label>Rate this movie:
          <select id="rateSelect">
            <option>5</option><option>4</option><option>3</option><option>2</option><option>1</option>
          </select>
        </label>
        <button id="rateBtn" data-id="${m.id}">Submit</button>
      </div>
    `;
    document.getElementById('rateBtn').onclick = async (ev) => {
      if (!USER_ID) { alert('Login to rate'); return; }
      const rating = parseInt(document.getElementById('rateSelect').value);
      await fetch('api/rate_movie.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({movie_id:m.id, rating})
      });
      alert('Thanks for rating!');
      loadMovies();
    };
    modal.classList.remove('hidden');
  }

  async function addWatchlist(e) {
    const id = e.target.dataset.id;
    if (!USER_ID) { alert('Login to add to watchlist'); return; }
    await fetch('api/add_watchlist.php', {
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body: JSON.stringify({movie_id: id})
    });
    alert('Added to watchlist');
  }

  closeModal.onclick = () => modal.classList.add('hidden');
  search.oninput = debounce(loadMovies, 300);
  genre.onchange = loadMovies;

  loadMovies();
});

// simple debounce
function debounce(fn, ms){ let t; return (...a)=>{ clearTimeout(t); t = setTimeout(()=>fn(...a), ms); }; }
