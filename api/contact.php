<?php
require 'db.php';
header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);
$name = trim($data['name'] ?? '');
$email = trim($data['email'] ?? '');
$msg = trim($data['message'] ?? '');
if (!$email || !$msg) { http_response_code(400); echo json_encode(['error'=>'missing']); exit; }
$stmt = $pdo->prepare('INSERT INTO feedback (name,email,message) VALUES (?, ?, ?)');
$stmt->execute([$name,$email,$msg]);
// Optionally: mail('you@domain.com', "New feedback", $msg, "From: $email");
echo json_encode(['success'=>true]);
