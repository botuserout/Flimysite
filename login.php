<?php
session_start();

// If already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - MovieHaven</title>
  <link rel="stylesheet" href="css/lstyle.css">
</head>
<body class="login-body">

  <div class="login-container">
    <div class="login-box">
      <div class="login-left">
        <h1>Welcome Back to <span class="brand">MovieHaven</span></h1>
        <p class="subtitle">Discover your next favorite film.</p>

        <form id="loginForm">
          <label for="username">Email or Username</label>
          <input type="text" id="username" name="username" placeholder="Enter your email or username" required>

          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="Enter your password" required>

          <a href="forgot.php" class="forgot-link">Forgot Password?</a>

          <button type="submit" class="btn-login">Login</button>
        </form>

        <p class="signup-text">
          Don’t have an account? <a href="signup.php">Sign Up</a>
        </p>
      </div>

      <div class="login-right">
        <img src="assests/placeholder.jpg" alt="Movie Haven" class="login-image">
      </div>
    </div>
  </div>

  <script src="js/main.js"></script>
  <script>
    // AJAX Login
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);

      const response = await fetch('api/login_api.php', {
        method: 'POST',
        body: formData
      });
      const result = await response.json();

      if (result.success) {
        window.location.href = 'index.php';
      } else {
        alert(result.message);
      }
    });
  </script>
</body>
</html>
