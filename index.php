<?php
session_start();
require_once 'includes/db.php';
$page = $_GET['page'] ?? 'home';

$photos = [];
$stmt = $pdo->query("SELECT * FROM images ORDER BY uploaded_at DESC LIMIT 6");
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

switch ($page) {
    case 'about':
        $content = 'about_content.php';
        break;
    case 'gallery':
        $content = 'gallery_content.php';
        break;
    case 'login':
        $content = 'login.php';
        break;
    default:
        $content = 'home_content.php';
}

include 'templates/header.php';
include 'templates/menu.php';
include "templates/$content";
include 'templates/footer.php';
?>