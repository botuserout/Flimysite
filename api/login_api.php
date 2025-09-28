<?php
session_start();
require 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit();
}

$identifier = trim($_POST['username'] ?? $_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($identifier === '' || $password === '') {
    echo json_encode(["success" => false, "message" => "Email/username and password are required"]);
    exit();
}

$stmt = $pdo->prepare("SELECT id, username, email, password_hash, is_admin FROM users WHERE email = ? OR username = ? LIMIT 1");
$stmt->execute([$identifier, $identifier]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['is_admin'] = $user['is_admin']; // Add admin status to session
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Invalid credentials"]);
}
