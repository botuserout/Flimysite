<?php
header("Content-Type: application/json");
require_once "db.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(["error" => "Invalid movie ID"]);
    exit;
}

$movie_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT id, title, genre, description, poster_url, release_year FROM movies WHERE id = ?");
$stmt->execute([$movie_id]);
$movie = $stmt->fetch();

if (!$movie) {
    echo json_encode(["error" => "Movie not found"]);
    exit;
}

$r = $pdo->prepare("SELECT IFNULL(ROUND(AVG(rating),1),0) AS avg_rating, COUNT(*) AS rating_count FROM ratings WHERE movie_id = ?");
$r->execute([$movie_id]);
$rating = $r->fetch() ?: ["avg_rating"=>0, "rating_count"=>0];

echo json_encode(array_merge($movie, $rating));
