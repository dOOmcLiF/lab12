<?php
session_start();
require_once 'includes/db.php';

// Проверка прав админа
if (empty($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: index.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$action = $_GET['action'] ?? 'hide'; // hide или show

if ($id <= 0) {
    die("Неверный ID пункта меню.");
}

$visible = $action === 'show' ? 1 : 0;

try {
    $stmt = $pdo->prepare("UPDATE menu SET visible = ? WHERE id = ?");
    $stmt->execute([$visible, $id]);
} catch (PDOException $e) {
    die("Ошибка при обновлении: " . $e->getMessage());
}

header("Location: edit_menu.php");
exit;
?>