<?php
$stmt = $pdo->query("SELECT * FROM menu WHERE visible = 1 ORDER BY position ASC");
$menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<nav class="main-menu">
    <ul>
        <?php foreach ($menu_items as $item): ?>
            <li><a href="<?= htmlspecialchars($item['url']) ?>"><?= htmlspecialchars($item['title']) ?></a></li>
        <?php endforeach; ?>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <li><a href="?page=upload_image">Загрузить фото</a></li>
            <li><a href="?page=my_gallery">Моя галерея</a></li>
            <li style="color: white; padding: 10px 16px;font-size: 20px; "> <?= htmlspecialchars($_SESSION['username']) ?></li>
            <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == 1): ?>
                <li><a href="edit_menu.php">Админ-панель</a></li>
            <?php endif; ?>
            <li><a href="logout.php">Выход</a></li>
        <?php else: ?>
            <li><a href="/?page=login">Войти</a></li>
            <li><a href="/?page=register">Зарегистрироваться</a></li>
        <?php endif; ?>
    </ul>
</nav>
