<?php
session_start();
require 'db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { http_response_code(401); echo json_encode(['error'=>'login']); exit; }
$uid = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT is_admin FROM users WHERE id=?');
$stmt->execute([$uid]);
$u = $stmt->fetch();
if (!$u || !$u['is_admin']) { http_response_code(403); echo json_encode(['error'=>'forbidden']); exit; }

$data = json_decode(file_get_contents('php://input'), true);
$title = trim($data['title'] ?? '');
$poster = trim($data['poster_url'] ?? '');
$genre = trim($data['genre'] ?? '');
$description = trim($data['description'] ?? '');

if (!$title) { http_response_code(400); echo json_encode(['error'=>'Title required']); exit; }

$stmt = $pdo->prepare('INSERT INTO movies (title, poster_url, genre, description) VALUES (?, ?, ?, ?)');
$stmt->execute([$title, $poster, $genre, $description]);
echo json_encode(['success'=>true, 'movie_id'=>$pdo->lastInsertId()]);
