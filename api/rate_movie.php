<?php
session_start();
require_once 'db.php';
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please login to rate movies']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get data from request
$data = json_decode(file_get_contents('php://input'), true);
$movie_id = intval($data['movie_id'] ?? 0);
$rating = intval($data['rating'] ?? 0);

// Validate input
if (!$movie_id || $rating < 1 || $rating > 5) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid movie ID or rating. Rating must be between 1 and 5.']);
    exit;
}

try {
    // Check if movie exists
    $check_movie = $pdo->prepare('SELECT id FROM movies WHERE id = ?');
    $check_movie->execute([$movie_id]);
    if (!$check_movie->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Movie not found']);
        exit;
    }
    
    // Insert or update rating (using ON DUPLICATE KEY UPDATE)
    $stmt = $pdo->prepare('
        INSERT INTO ratings (user_id, movie_id, rating) 
        VALUES (?, ?, ?) 
        ON DUPLICATE KEY UPDATE rating = VALUES(rating)
    ');
    $stmt->execute([$user_id, $movie_id, $rating]);
    
    // Get updated average rating and count
    $avg_stmt = $pdo->prepare('
        SELECT 
            IFNULL(ROUND(AVG(rating), 1), 0) as avg_rating,
            COUNT(*) as rating_count
        FROM ratings 
        WHERE movie_id = ?
    ');
    $avg_stmt->execute([$movie_id]);
    $rating_data = $avg_stmt->fetch();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Rating saved successfully!',
        'avg_rating' => floatval($rating_data['avg_rating']),
        'rating_count' => intval($rating_data['rating_count'])
    ]);
    
} catch (Exception $e) {
    error_log("Rating error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>