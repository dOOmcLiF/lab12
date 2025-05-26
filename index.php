<?php
$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'about': $content = 'about_content.php'; break;
    case 'gallery': $content = 'gallery.php'; break;
    default: $content = 'home_content.php';
}

include 'templates/header.php';
include "templates/menu.php";
include "templates/$content";
include 'templates/footer.php';
include 'register.php';
?>
