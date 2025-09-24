<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit();
}

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirm_password = trim($_POST['confirm_password'] ?? '');

if ($username === '' || $email === '' || $password === '' || $confirm_password === '') {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Invalid email address."]);
    exit();
}
if ($password !== $confirm_password) {
    echo json_encode(["success" => false, "message" => "Passwords do not match."]);
    exit();
}

// Check duplicates
$check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1");
$check->execute([$username, $email]);
if ($check->fetch()) {
    echo json_encode(["success" => false, "message" => "Username or Email already exists."]);
    exit();
}

// Insert user
$hashed = password_hash($password, PASSWORD_DEFAULT);
$ins = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
$ins->execute([$username, $email, $hashed]);

echo json_encode(["success" => true, "message" => "Account created successfully."]);
