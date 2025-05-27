<?php
// delete_menu_item.php

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Проверка прав
if (empty($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: index.php");
    exit;
}

// Подключение к БД
require_once 'includes/db.php';

// Получаем ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    die("Неверный ID пункта меню.");
}

// Удаляем из БД
try {
    $stmt = $pdo->prepare("DELETE FROM menu WHERE id = ?");
    $stmt->execute([$id]);
} catch (PDOException $e) {
    die("Ошибка при удалении: " . $e->getMessage());
}

header("Location: edit_menu.php");
exit;
?>