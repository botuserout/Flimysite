<?php
session_start();
require 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['error'=>'login']); exit; }
$uid = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$movie_id = intval($data['movie_id'] ?? 0);
if (!$movie_id) { http_response_code(400); echo json_encode(['error'=>'movie required']); exit; }

try {
  $stmt = $pdo->prepare('INSERT IGNORE INTO watchlist (user_id, movie_id) VALUES (?, ?)');
  $stmt->execute([$uid, $movie_id]);
  echo json_encode(['success'=>true]);
} catch (Exception $e) {
  http_response_code(500); echo json_encode(['error'=>'db']);
}
