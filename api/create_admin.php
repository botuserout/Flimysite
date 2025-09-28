<?php
require_once 'db.php';

// Create admin user with proper password hash
$username = 'admin';
$email = 'admin@moviehaven.com';
$password = 'admin123'; // Change this to your desired admin password
$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Check if admin already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    if ($stmt->fetch()) {
        // Update existing admin
        $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, is_admin = 1 WHERE username = ? OR email = ?");
        $stmt->execute([$password_hash, $username, $email]);
        echo "Admin user updated successfully!<br>";
    } else {
        // Create new admin
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, is_admin) VALUES (?, ?, ?, 1)");
        $stmt->execute([$username, $email, $password_hash]);
        echo "Admin user created successfully!<br>";
    }
    
    echo "Admin credentials:<br>";
    echo "Username: " . $username . "<br>";
    echo "Email: " . $email . "<br>";
    echo "Password: " . $password . "<br>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
