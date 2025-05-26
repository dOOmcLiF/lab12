<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? null;

if (!$token) {
    $error = "Неверный токен.";
} else {
    $user = get_user_by_token($pdo, $token);
    if (!$user) {
        $error = "Неверный или истёкший токен.";
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            $error = "Пароли не совпадают.";
        } elseif (strlen($password) < 6) {
            $error = "Пароль должен быть не менее 6 символов.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ?, reset_token = NULL, token_expires = NULL WHERE id = ?");
            $stmt->execute([$password_hash, $user['id']]);
            $success = "Пароль успешно изменён!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Сброс пароля</title>
</head>
<body>

<h2>Сброс пароля</h2>

<?php if ($error): ?>
    <p style="color: red;"><?= $error ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color: green;"><?= $success ?></p>
    <p><a href="login.php">Войти</a></p>
<?php endif; ?>

<?php if (!$success && $user): ?>
<form method="post">
    <label>Новый пароль:<br>
        <input type="password" name="password" required><br><br>
    </label>
    <label>Подтвердите пароль:<br>
        <input type="password" name="confirm_password" required><br><br>
    </label>
    <button type="submit">Сохранить новый пароль</button>
</form>
<?php endif; ?>

</body>
</html>