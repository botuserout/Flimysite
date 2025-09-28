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
  <title>Sign Up - MovieHaven</title>
  <link rel="stylesheet" href="css/sstyle.css">
</head>
<body class="signup-body">

  <div class="signup-container">
    <div class="signup-box">
      <!-- Left section (Form) -->
      <div class="signup-left">
        <h1>Create Your Account</h1>

        <form id="signupForm">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Enter your username" required>

          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="user@filmheavens.com" required>

          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="******" required>

          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" placeholder="******" required>

          <button type="submit" class="btn-signup">Sign Up</button>
        </form>

        <p class="login-text">
          Already have an account? <a href="login.php">Login here.</a>
        </p>
      </div>

      <!-- Right section (Image) -->
      <div class="signup-right">
        <img src="https://upload.wikimedia.org/wikipedia/en/6/6d/The_Terminator.png" alt="Signup Banner" class="signup-image">
      </div>
    </div>
  </div>

  <script src="js/main.js"></script>
  <script>
    // AJAX Signup
    document.getElementById('signupForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const formData = new FormData(e.target);

      const response = await fetch('api/signup_api.php', {
        method: 'POST',
        body: formData
      });
      const result = await response.json();

      if (result.success) {
        alert("Signup successful! Redirecting to login...");
        window.location.href = 'login.php';
      } else {
        alert(result.message);
      }
    });
  </script>
</body>
</html>
