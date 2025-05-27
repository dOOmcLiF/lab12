<?php
session_start();
require_once 'includes/db.php';

if (empty($_SESSION['user_id'])) {
    header("Location: /?page=login");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $photo_id = $_POST['photo_id'];

    $stmt = $pdo->prepare("SELECT id FROM images WHERE id = ? AND user_id = ?");
    $stmt->execute([$photo_id, $_SESSION['user_id']]);
    if (!$stmt->fetch()) {
        die("Ошибка доступа.");
    }

    $stmt = $pdo->prepare("SELECT visible FROM images WHERE id = ?");
    $stmt->execute([$photo_id]);
    $row = $stmt->fetch();
    $new_state = $row['visible'] ? 0 : 1;

    $stmt = $pdo->prepare("UPDATE images SET visible = ? WHERE id = ?");
    $stmt->execute([$new_state, $photo_id]);

    header("Location: /?page=gallery");
    exit;
}
?>