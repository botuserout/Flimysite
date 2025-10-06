<?php
// api/get_reviews.php — Fetch reviews for a movie
require_once 'db.php';
header('Content-Type: application/json');
$movie_id = isset($_GET['movie_id']) ? (int)$_GET['movie_id'] : 0;
if (!$movie_id) { echo json_encode([]); exit; }
$stmt = $pdo->prepare('SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.movie_id = ? ORDER BY r.created_at DESC');
$stmt->execute([$movie_id]);
echo json_encode($stmt->fetchAll());
