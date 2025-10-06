<?php
// api/user_watchlist.php
// Fetches the current user's watchlist (movie info) as JSON. POST can update watchlist status.
session_start();
require_once 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error'=>'login']);
    exit;
}
$uid = (int)$_SESSION['user_id'];
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->prepare("SELECT m.* FROM user_movies um JOIN movies m ON um.movie_id = m.id WHERE um.user_id = ? AND um.in_watchlist = 1");
    $stmt->execute([$uid]);
    $movies = $stmt->fetchAll();
    echo json_encode(['watchlist'=>$movies]);
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $movie_id = intval($data['movie_id'] ?? 0);
    $add = !empty($data['add']);
    if (!$movie_id) {
        http_response_code(400);
        echo json_encode(['error'=>'movie required']);
        exit;
    }
    if ($add) {
        $stmt = $pdo->prepare("INSERT INTO user_movies (user_id, movie_id, in_watchlist) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE in_watchlist=1");
        $stmt->execute([$uid, $movie_id]);
    } else {
        $stmt = $pdo->prepare("UPDATE user_movies SET in_watchlist=0 WHERE user_id=? AND movie_id=?");
        $stmt->execute([$uid, $movie_id]);
    }
    echo json_encode(['success'=>true]);
    exit;
}
echo json_encode(['error'=>'invalid request']);
