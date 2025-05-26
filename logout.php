<?php
session_start();
session_destroy(); // Полностью уничтожаем сессию
setcookie('PHPSESSID', '', time()-3600, '/'); // Очистка cookie (необязательно)
header("Location: index.php");
exit;
?>