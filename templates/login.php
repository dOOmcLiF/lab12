<?php
session_start();
require_once 'includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        header("Location: index.php");
        exit;
    } else {
        $error = "Неверный логин или пароль.";
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="login-container">
    <h2>Вход</h2>

    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="username" required placeholder="Логин">
        <input type="password" name="password" required placeholder="Пароль">
        <button type="submit">Войти</button>
    </form>

    <div class="link">
        Нет аккаунта? <a href="register.php">Зарегистрироваться</a><br>
        <a href="forgot_password.php">Забыли пароль?</a>
    </div>
</div>

</body>
</html>