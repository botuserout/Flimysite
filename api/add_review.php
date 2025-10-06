<?php
// api/add_review.php — Add a review for a movie
session_start();
require_once 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error'=>'Login required']); exit;
}
$user_id = (int)$_SESSION['user_id'];
$movie_id = isset($_POST['movie_id']) ? (int)$_POST['movie_id'] : 0;
$review = trim($_POST['review'] ?? '');
if (!$movie_id || !$review) {
    echo json_encode(['error'=>'Missing movie or review']); exit;
}
$stmt = $pdo->prepare('INSERT INTO reviews (user_id, movie_id, review) VALUES (?, ?, ?)');
$stmt->execute([$user_id, $movie_id, $review]);
echo json_encode(['success'=>true]);
