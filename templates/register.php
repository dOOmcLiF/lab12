<?php
// register.php — страница регистрации пользователя

require_once 'includes/db.php'; // Подключение к БД

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Валидация данных
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Все поля обязательны для заполнения.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Некорректный адрес электронной почты.";
    } elseif ($password !== $confirm_password) {
        $error = "Пароли не совпадают.";
    } elseif (strlen($password) < 6) {
        $error = "Пароль должен быть не менее 6 символов.";
    } else {
        // Проверяем, существует ли такой пользователь
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $error = "Пользователь с таким логином или email уже существует.";
        } else {
            // Хэшируем пароль
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Сохраняем пользователя в БД
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $password_hash])) {
                $success = "Регистрация успешна! Теперь вы можете войти.";
            } else {
                $error = "Ошибка при регистрации. Попробуйте позже.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="assets/styles.css"> <!-- Подключение стилей -->
</head>
<body>
    <div class="registration-container">
        <h2>Регистрация нового пользователя</h2>

        <?php if ($error): ?>
            <p class="error-message"><?= $error ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="success-message"><?= $success ?></p>
            <p><a href="/?page=login">Войти</a></p>
        <?php endif; ?>

        <form method="post">
            <label>Имя пользователя:<br>
                <input type="text" name="username" required>
            </label>

            <label>Email:<br>
                <input type="email" name="email" required>
            </label>

            <label>Пароль:<br>
                <input type="password" name="password" required>
            </label>

            <label>Подтвердите пароль:<br>
                <input type="password" name="confirm_password" required>
            </label>

            <button type="submit">Зарегистрироваться</button>
        </form>

        <p>Уже есть аккаунт? <a href="/?page=login">Войдите</a></p>
    </div>
</body>
</html>

