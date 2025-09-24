<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Unauthorized");
}

$action = $_GET['action'] ?? '';
$id = intval($_GET['id'] ?? 0);

if ($action === 'delete' && $id > 0) {
    $conn->query("DELETE FROM movies WHERE id=$id");
    header("Location: ../admin.php?section=movies");
    exit;
}
