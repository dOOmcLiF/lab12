<?php
session_start();
require_once 'includes/db.php';
$page = $_GET['page'] ?? 'home';

$stmt = $pdo->query("
    SELECT images.*, users.username 
    FROM images 
    JOIN users ON images.user_id = users.id 
    WHERE images.visible = 1 
    ORDER BY images.uploaded_at DESC 
    LIMIT 6
");
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

switch ($page) {
    case 'about':
        $content = 'about_content.php';
        break;
    case 'my_gallery':
        $content = 'my_gallery.php';
        break;
    case 'gallery':
        $content = 'gallery.php';
        break;
    case 'login':
        $content = 'login.php';
        break;
    case 'register':
        $content = 'register.php';
        break;
    case 'upload_image':
        $content = 'upload_image.php';
        break;
    default:
        $content = 'home_content.php';
}

include 'templates/header.php';
include 'templates/menu.php';
include "templates/$content";
include 'templates/footer.php';
?>