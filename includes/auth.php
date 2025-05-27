<?php
// includes/auth.php

#use PHPMailer\PHPMailer\PHPMailer;
#use PHPMailer\PHPMailer\Exception;

function generate_token() {
    return bin2hex(random_bytes(50));
}

function send_reset_email($email, $token) {
    $reset_link = "/reset_password.php?token=$token";

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);

    try {
        // Настройки сервера
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';      // SMTP-сервер (пример: Gmail)
        $mail->SMTPAuth   = true;
        $mail->Username   = 'erdemstanis@gmail.com'; // ваш email
        $mail->Password   = 'wrhs klls qanf wkwa';          // пароль от почты
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Получатель
        $mail->setFrom('no-reply@example.com', 'Ваш сайт');
        $mail->addAddress('abc.abcd.1910@mail.ru');

        // Содержание письма
        $mail->isHTML(false);
        $mail->Subject = 'Восстановление пароля';
        $mail->Body    = "Для восстановления пароля перейдите по ссылке:\n\n$reset_link";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Ошибка отправки письма: {$mail->ErrorInfo}");
        return false;
    }
}

function get_user_by_token($pdo, $token) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND token_expires > NOW()");
    $stmt->execute([$token]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>