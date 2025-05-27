<?php
session_start();
require_once 'includes/db.php';

if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $photo_id = $_POST['photo_id'];

    $stmt = $pdo->prepare("SELECT original_path, thumb_path FROM images WHERE id = ? AND user_id = ?");
    $stmt->execute([$photo_id, $_SESSION['user_id']]);
    $photo = $stmt->fetch();

    if ($photo) {
        unlink($photo['original_path']);
        unlink($photo['thumb_path']);

        $stmt = $pdo->prepare("DELETE FROM images WHERE id = ?");
        $stmt->execute([$photo_id]);

        header("Location: /?page=gallery");
        exit;
    } else {
        echo "Ошибка: Фото не найдено.";
    }
}
?>