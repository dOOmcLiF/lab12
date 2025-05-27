<?php
session_start();
require_once 'includes/db.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: index.php");
    exit;
}

// Сохранение изменений
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['title'] as $id => $title) {
        $url = $_POST['url'][$id];
        $position = (int)$_POST['position'][$id];
        $visible = isset($_POST['visible'][$id]) ? 1 : 0;

        $stmt = $pdo->prepare("
            UPDATE menu 
            SET title = ?, url = ?, position = ?, visible = ? 
            WHERE id = ?
        ");
        $stmt->execute([$title, $url, $position, $visible, $id]);
    }

    echo "<p style='color:green;'>Меню обновлено!</p>";
}

$stmt = $pdo->query("SELECT * FROM menu ORDER BY position ASC");
$menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать меню</title>
    <style>
        
    </style>
</head>
<body>

<h2>Редактировать меню</h2>

<form method="post">
    <table>
        <thead>
            <tr>
                <th>Заголовок</th>
                <th>URL</th>
                <th>Порядок</th>
                <th>Видимость</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($menu_items as $item): ?>
                <tr>
                    <td>
                        <input type="text" name="title[<?= $item['id'] ?>" value="<?= htmlspecialchars($item['title']) ?>">
                    </td>
                    <td>
                        <input type="text" name="url[<?= $item['id'] ?>" value="<?= htmlspecialchars($item['url']) ?>">
                    </td>
                    <td>
                        <input type="number" name="position[<?= $item['id'] ?>" value="<?= $item['position'] ?>" min="0">
                    </td>
                    <td>
                        <label>
                            <input type="checkbox" name="visible[<?= $item['id'] ?>" <?= $item['visible'] ? 'checked' : '' ?>>
                            Видимый
                        </label>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button type="submit">Сохранить изменения</button>
</form>

<p><a href="index.php">← Назад</a></p>

</body>
</html>