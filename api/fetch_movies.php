<?php
header('Content-Type: application/json');
require 'db.php';

$q = $_GET['q'] ?? '';
$genre = $_GET['genre'] ?? '';
$limit = (int)($_GET['limit'] ?? 50);  // Cast to integer

$sql = "SELECT m.*, 
 (SELECT IFNULL(ROUND(AVG(rating),1),0) FROM ratings WHERE movie_id = m.id) AS avg_rating,
 (SELECT COUNT(*) FROM ratings WHERE movie_id = m.id) AS rating_count
 FROM movies m WHERE 1";

$params = [];
if ($q !== '') {
    $sql .= " AND m.title LIKE ?";
    $params[] = "%$q%";
}
if ($genre !== '' && $genre !== 'All') {
    $sql .= " AND m.genre = ?";
    $params[] = $genre;
}
$sql .= " ORDER BY m.title LIMIT " . $limit; // Changed to directly append the integer limit

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$movies = $stmt->fetchAll();
echo json_encode($movies);
