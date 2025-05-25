<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        setcookie('logged_in', 'true', time()+3600, '/');
        header("Location: index.php");
        exit;
    } else {
        echo "Неверные учетные данные.";
    }
}
?>
<form method="post">
    <input type="text" name="username" required placeholder="Логин"><br>
    <input type="password" name="password" required placeholder="Пароль"><br>
    <button type="submit">Войти</button>
</form>
