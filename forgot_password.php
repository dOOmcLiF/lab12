<?php
#session_start();
date_default_timezone_set('Asia/Krasnoyarsk');

require_once __DIR__ . '/lib/PHPMailer/src/Exception.php';
require_once __DIR__ . '/lib/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/lib/PHPMailer/src/SMTP.php';

require_once 'includes/db.php';
require_once 'includes/auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    if (!$email) {
        $error = "Введите корректный email.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $token = generate_token();
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, token_expires = ? WHERE id = ?");
            $stmt->execute([$token, $expires, $user['id']]);

            if (send_reset_email($email, $token)) {
                $success = "На ваш email отправлена ссылка для восстановления пароля.";
            } else {
                $error = "Не удалось отправить письмо. Попробуйте позже.";
            }
        } else {
            $error = "Пользователь с таким email не найден.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Восстановить пароль</title>
</head>
<body>

<h2>Восстановление пароля</h2>

<?php if ($error): ?>
    <p style="color: red;"><?= $error ?></p>
<?php endif; ?>

<?php if ($success): ?>
    <p style="color: green;"><?= $success ?></p>
    <p><a href="login.php">← Войти</a></p>
<?php endif; ?>

<form method="post">
    <label>Email:<br>
        <input type="email" name="email" required><br><br>
    </label>
    <button type="submit">Отправить ссылку</button>
</form>

<p><a href="login.php">← Назад</a></p>

</body>
</html>