<?php
// api/chatbot.php — AI-powered chatbot endpoint for movie recommendations
session_start();
require_once 'db.php';
header('Content-Type: application/json');

// Simulated NLP intent and entity extraction (for demo)
function parse_query($query) {
    $query = strtolower(trim($query));
    $intent = 'recommend';
    $genre = null; $decade = null; $similar = null;
    if (preg_match('/action|comedy|drama|thriller|romance|horror|sci-fi|animation/', $query, $gmatch)) {
        $genre = $gmatch[0];
    }
    if (preg_match('/(\d{4})s/', $query, $dmatch)) {
        $decade = $dmatch[1];
    }
    if (preg_match('/similar to ([\w\s]+)/', $query, $smatch)) {
        $similar = trim($smatch[1]);
    }
    return compact('intent','genre','decade','similar');
}

function recommend_movies($pdo, $user_id, $genre, $decade, $similar) {
    // Personalized: prefer movies not watched/rated by user
    $params = [];
    $sql = "SELECT m.*, IFNULL(AVG(r.rating),0) as avg_rating
            FROM movies m LEFT JOIN ratings r ON m.id = r.movie_id
            WHERE 1";
    if ($genre) {
        $sql .= " AND LOWER(m.genre) LIKE ?";
        $params[] = "%$genre%";
    }
    if ($decade) {
        $sql .= " AND m.release_year BETWEEN ? AND ?";
        $params[] = $decade;
        $params[] = $decade+9;
    }
    if ($user_id) {
        $sql .= " AND m.id NOT IN (SELECT movie_id FROM watched_movies WHERE user_id = ? AND watched_status = 1)";
        $params[] = $user_id;
    }
    $sql .= " GROUP BY m.id ORDER BY avg_rating DESC LIMIT 5";
    // Similar movie logic
    if ($similar) {
        $sim = $pdo->prepare("SELECT * FROM movies WHERE title LIKE ? LIMIT 1");
        $sim->execute(["%$similar%"]);
        $base = $sim->fetch();
        if ($base) {
            $sql = "SELECT m.*, IFNULL(AVG(r.rating),0) as avg_rating
                    FROM movies m LEFT JOIN ratings r ON m.id = r.movie_id
                    WHERE m.id != ? AND m.genre = ? GROUP BY m.id ORDER BY avg_rating DESC LIMIT 5";
            $params = [$base['id'], $base['genre']];
        }
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Input validation
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['message'])) {
    echo json_encode(['error'=>'Missing message']); exit;
}
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
$query = filter_var($input['message'], FILTER_SANITIZE_STRING);
$parsed = parse_query($query);
$movies = recommend_movies($pdo, $user_id, $parsed['genre'], $parsed['decade'], $parsed['similar']);

if (!$movies) {
    echo json_encode(['reply'=>'Sorry, no movies found for your request.']); exit;
}
$reply = "Recommended movies:";
foreach ($movies as $m) {
    $reply .= "\n- {$m['title']} (".($m['release_year']?:'').") ⭐".number_format($m['avg_rating'],1);
}
echo json_encode(['reply'=>$reply, 'movies'=>$movies]);
