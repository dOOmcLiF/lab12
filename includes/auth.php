<?php
function generate_token() {
    return bin2hex(random_bytes(50));
}

function send_reset_email($email, $token) {
    $reset_link = "reset_password.php?token=$token";
    $subject = "Восстановление пароля";
    $message = "Для восстановления пароля перейдите по ссылке:\n\n$reset_link";
    $headers = "From: no-reply@example.com";

    // Для тестирования можно использовать mail()
    return mail($email, $subject, $message, $headers);
}

function get_user_by_token($pdo, $token) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND token_expires > NOW()");
    $stmt->execute([$token]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>