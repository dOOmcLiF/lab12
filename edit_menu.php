<?php
session_start();
require_once 'includes/db.php';

// Только админ может редактировать
if (empty($_SESSION['user_id']) || $_SESSION['user_id'] != 1) {
    header("Location: index.php");
    exit;
}

// === Обработка формы ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";
    // exit;

    // === 1. Удаление пункта ===
    if (!empty($_POST['delete_id'])) {
        $delete_id = (int)$_POST['delete_id'];

        try {
            $stmt = $pdo->prepare("DELETE FROM menu WHERE id = ?");
            $stmt->execute([$delete_id]);
            header("Location: edit_menu.php");
            exit;
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Ошибка при удалении: " . $e->getMessage() . "</p>";
        }
    }

    // === 2. Добавление нового пункта ===
    if (!empty($_POST['new_title']) && !empty($_POST['new_url'])) {
        $new_title = trim($_POST['new_title']);
        $new_url = trim($_POST['new_url']);
        $new_position = (int)$_POST['new_position'];
        $new_visible = isset($_POST['new_visible']) ? 1 : 0;

        try {
            $stmt = $pdo->prepare("INSERT INTO menu (title, url, position, visible) VALUES (?, ?, ?, ?)");
            $stmt->execute([$new_title, $new_url, $new_position, $new_visible]);
            header("Location: edit_menu.php");
            exit;
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Ошибка при добавлении: " . $e->getMessage() . "</p>";
        }
    }

    if (isset($_POST['title'])) {
        try {
            foreach ($_POST['title'] as $id => $title) {
                $url = $_POST['url'][$id];
                $position = (int)$_POST['position'][$id];
                $visible = (int)$_POST['visible'][$id];

                $stmt = $pdo->prepare("UPDATE menu SET title = ?, url = ?, position = ?, visible = ? WHERE id = ?");
                $stmt->execute([$title, $url, $position, $visible, $id]);
            }
            echo "<p style='color:green;'>Меню успешно обновлено!</p>";
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Ошибка при обновлении: " . $e->getMessage() . "</p>";
        }

        header("Location: edit_menu.php");
        exit;
    }
}

// Получаем текущие пункты меню
$stmt = $pdo->query("SELECT * FROM menu ORDER BY position ASC");
$menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать меню</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        input[type="text"], input[type="number"] { width: 100%; padding: 5px; }
        button { padding: 10px 15px; background-color: #2c3e50; color: white; border: none; cursor: pointer; margin-top: 10px; }
        button:hover { background-color: #1a252f; }
        form { margin-bottom: 20px; }
    </style>
</head>
<body>

<h2>Редактировать меню</h2>

<form method="post">
    <input type="text" name="new_title" placeholder="Заголовок" required><br><br>
    <input type="text" name="new_url" placeholder="URL" required><br><br>
    <input type="number" name="new_position" placeholder="Порядок" min="0" required><br><br>
    <label><input type="checkbox" name="new_visible" value="1" checked> Видимый</label><br><br>
    <button type="submit">Добавить пункт</button>
</form>

<form method="post">
    <table>
        <thead>
            <tr>
                <th>Заголовок</th>
                <th>URL</th>
                <th>Порядок</th>
                <th>Видимость</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($menu_items as $item): ?>
                <tr>
                    <td><input type="text" name="title[<?= $item['id'] ?>" value="<?= htmlspecialchars($item['title']) ?>"></td>
                    <td><input type="text" name="url[<?= $item['id'] ?>" value="<?= htmlspecialchars($item['url']) ?>"></td>
                    <td><input type="number" name="position[<?= $item['id'] ?>" value="<?= $item['position'] ?>" min="0"></td>
                    <td>
                        <?php if ($item['visible']): ?>
                            <a href="toggle_menu_visible.php?id=<?= $item['id'] ?>&action=hide">Скрыть</a>
                        <?php else: ?>
                            <a href="toggle_menu_visible.php?id=<?= $item['id'] ?>&action=show">Показать</a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="delete_menu_item.php?id=<?= $item['id'] ?>" onclick="return confirm('Удалить этот пункт?')">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</form>

<p><a href="index.php">← Назад</a></p>

</body>
</html>