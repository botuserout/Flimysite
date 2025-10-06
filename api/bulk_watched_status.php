<?php
// api/bulk_watched_status.php — Get watched status for multiple movie IDs for a user
session_start();
require_once "db.php";
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$ids = isset($_POST['movie_ids']) ? $_POST['movie_ids'] : '';
if (!$ids) {
    echo json_encode(['success' => false, 'error' => 'No movie_ids']);
    exit;
}
$movie_ids = explode(',', $ids);
$in = str_repeat('?,', count($movie_ids) - 1) . '?';
$sql = "SELECT movie_id, watched_status FROM watched_movies WHERE user_id = ? AND movie_id IN ($in)";
$params = array_merge([$user_id], $movie_ids);
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
$status_map = [];
foreach ($rows as $row) {
    $status_map[$row['movie_id']] = (int)$row['watched_status'];
}
echo json_encode(['success' => true, 'status_map' => $status_map]);
