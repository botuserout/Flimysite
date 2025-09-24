<?php
require 'db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
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

    $check = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
    $check->bind_param("ss", $username, $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Username or Email already exists."]);
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Account created successfully."]);
    } else {
        echo json_encode(["success" => false, "message" => "Error creating account."]);
    }

    $stmt->close();
    $conn->close();
}
