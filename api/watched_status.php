<?php
// api/watched_status.php — Handle AJAX watched status updates and fetch
session_start();
require_once "db.php";
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$movie_id = isset($_POST['movie_id']) ? (int)$_POST['movie_id'] : 0;
$watched_status = isset($_POST['watched_status']) ? (int)$_POST['watched_status'] : null;

if (!$movie_id) {
    echo json_encode(['success' => false, 'error' => 'Missing movie_id']);
    exit;
}

if ($watched_status !== null) {
    // Update watched status
    $stmt = $pdo->prepare("INSERT INTO watched_movies (user_id, movie_id, watched_status) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE watched_status = VALUES(watched_status)");
    $stmt->execute([$user_id, $movie_id, $watched_status]);
    echo json_encode(['success' => true, 'watched_status' => $watched_status]);
    exit;
} else {
    // Fetch watched status
    $stmt = $pdo->prepare("SELECT watched_status FROM watched_movies WHERE user_id = ? AND movie_id = ?");
    $stmt->execute([$user_id, $movie_id]);
    $row = $stmt->fetch();
    $watched = $row ? (int)$row['watched_status'] : 0;
    echo json_encode(['success' => true, 'watched_status' => $watched]);
    exit;
}
