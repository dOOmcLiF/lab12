<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'includes/db.php';
require_once 'includes/gallery_functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $allowed_types = ['image/jpeg', 'image/png'];
    $file = $_FILES['photo'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = "Ошибка при загрузке файла.";
    } elseif (!in_array($file['type'], $allowed_types)) {
        $error = "Разрешены только JPG и PNG.";
    } else {
        $name = uniqid('img_') . '_' . basename($file['name']);
        $original_path = "uploads/" . $name;
        $thumb_path = "thumbs/" . $name;

        if (move_uploaded_file($file['tmp_name'], $original_path)) {
            // Создаем миниатюру
            create_thumbnail($original_path, $thumb_path);

            // Добавляем водяной знак
            add_watermark($original_path);

            // Сохраняем в БД
            $stmt = $pdo->prepare("INSERT INTO images (user_id, original_path, thumb_path) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $original_path, $thumb_path]);

            $success = "Фото успешно загружено!";
        } else {
            $error = "Не удалось сохранить файл.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Загрузить фото</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<div class = "upload-container">
    <h2>Загрузить фото</h2>

    <?php if ($error): ?>
        <p style="error"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="success"><?= $success ?></p>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <input type="file" name="photo" accept="image/*" required><br><br>
        <button class="button1" type="submit">Загрузить</button>
    </form>

</div>
</body>
</html>