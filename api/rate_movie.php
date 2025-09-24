<?php
session_start();
require 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['error'=>'login']); exit; }
$uid = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$movie_id = intval($data['movie_id'] ?? 0);
$rating = intval($data['rating'] ?? 0);
if ($movie_id<=0 || $rating <1 || $rating>5) { http_response_code(400); echo json_encode(['error'=>'invalid']); exit; }

$stmt = $pdo->prepare('INSERT INTO ratings (user_id, movie_id, rating) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE rating = VALUES(rating), created_at = CURRENT_TIMESTAMP');
$stmt->execute([$uid,$movie_id,$rating]);
echo json_encode(['success'=>true]);
