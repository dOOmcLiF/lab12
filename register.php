<?php
session_start();
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $password]);
        echo "Регистрация успешна!";
    } catch (PDOException $e) {
        echo "Ошибка регистрации.";
    }
}
?>
<form method="post">
    <input type="text" name="username" required placeholder="Имя пользователя"><br>
    <input type="email" name="email" required placeholder="Email"><br>
    <input type="password" name="password" required placeholder="Пароль"><br>
    <button type="submit">Зарегистрироваться</button>
</form>
