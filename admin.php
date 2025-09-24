<!-- admin.php -->
<?php session_start(); 
if(!isset($_SESSION['user_id'])){ header('Location: login.php'); exit; }
?>
<!doctype html>
<html><head><link rel="stylesheet" href="css/style.css"></head><body>
<h2>Admin — Add Movie</h2>
<form id="adminForm">
  <input name="title" placeholder="Title" required>
  <input name="poster_url" placeholder="Poster URL">
  <input name="genre" placeholder="Genre">
  <textarea name="description" placeholder="Description"></textarea>
  <button type="submit">Add Movie</button>
</form>
<script>
document.getElementById('adminForm').onsubmit = async e=>{
  e.preventDefault();
  const f = new FormData(e.target);
  const data = Object.fromEntries(f.entries());
  const res = await fetch('api/admin_add_movie.php', {
    method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data)
  });
  const j = await res.json();
  if (j.success) alert('Added');
  else alert('Error');
};
</script>
</body></html>
