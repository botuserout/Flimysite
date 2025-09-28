<?php
require_once 'db.php';
header('Content-Type: application/json');

$movie_id = intval($_GET['id'] ?? 0);

if (!$movie_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Movie ID required']);
    exit;
}

try {
    // Get average rating and count for the movie
    $stmt = $pdo->prepare('
        SELECT 
            IFNULL(ROUND(AVG(rating), 1), 0) as avg_rating,
            COUNT(*) as rating_count
        FROM ratings 
        WHERE movie_id = ?
    ');
    $stmt->execute([$movie_id]);
    $result = $stmt->fetch();
    
    echo json_encode([
        'avg_rating' => floatval($result['avg_rating']),
        'rating_count' => intval($result['rating_count'])
    ]);
    
} catch (Exception $e) {
    error_log("Get rating error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
}
?>
