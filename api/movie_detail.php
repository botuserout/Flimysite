<?php
header("Content-Type: application/json");
require_once "db.php";

// Validate movie_id
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo json_encode(["error" => "Invalid movie ID"]);
    exit;
}

$movie_id = intval($_GET['id']);

// Fetch movie details from DB
$stmt = $conn->prepare("SELECT * FROM movies WHERE id = ?");
$stmt->bind_param("i", $movie_id);
$stmt->execute();
$result = $stmt->get_result();
$movie = $result->fetch_assoc();

if ($movie) {
    echo json_encode([
        "id" => $movie["id"],
        "title" => $movie["title"],
        "year" => $movie["year"],
        "genre" => $movie["genre"],
        "description" => $movie["description"],
        "poster_url" => $movie["poster_url"],
        "rating" => $movie["rating"]
    ]);
} else {
    echo json_encode(["error" => "Movie not found"]);
}
?>
