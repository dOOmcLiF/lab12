<?php
$stmt = $pdo->query("SELECT * FROM menu WHERE visible = 1 ORDER BY position ASC");
$menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<nav class="main-menu">
    <ul>
        <?php foreach ($menu_items as $item): ?>
            <li><a href="<?= htmlspecialchars($item['url']) ?>"><?= htmlspecialchars($item['title']) ?></a></li>
        <?php endforeach; ?>
        <li><a href="?page=home">Главная</a></li>
        <li><a href="?page=about">О сайте</a></li>
        <li><a href="?page=gallery">Галерея</a></li>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <li><a href="upload_image.php">Загрузить фото</a></li>
            <li><a href="?page=gallery">Моя галерея</a></li>
            <li style="color: white; padding: 10px 16px;"> <?= htmlspecialchars($_SESSION['username']) ?></li>
            <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] == 1): ?>
                <li><a href="edit_menu.php">Редактировать меню</a></li>
            <?php endif; ?>
            <li><a href="logout.php">Выход</a></li>
        <?php else: ?>
            <li><a href="/?page=login">Войти</a></li>
            <li><a href="/?page=register">Зарегистрироваться</a></li>
        <?php endif; ?>
    </ul>
</nav>
