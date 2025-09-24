<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        echo json_encode(["success" => false, "message" => "All fields are required."]);
        exit();
    }

    $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE username=? OR email=? LIMIT 1");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($id, $uname, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $uname;

            echo json_encode(["success" => true, "message" => "Login successful"]);
        } else {
            echo json_encode(["success" => false, "message" => "Invalid password"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "No account found"]);
    }

    $stmt->close();
    $conn->close();
}
