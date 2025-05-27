<nav class="main-menu">
    <ul>
        <li><a href="?page=home">Главная</a></li>
        <li><a href="?page=about">О сайте</a></li>
        <li><a href="?page=gallery">Галерея</a></li>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <li><a href="upload_image.php">Загрузить фото</a></li>
            <li><a href="?page=gallery">Моя галерея</a></li>
            <li style="color: white; padding: 10px 16px;"> <?= htmlspecialchars($_SESSION['username']) ?></li>
            <li><a href="logout.php">Выход</a></li>
        <?php else: ?>
            <li><a href="/?page=login">Войти</a></li>
            <li><a href="register.php">Зарегистрироваться</a></li>
        <?php endif; ?>
    </ul>
</nav>
