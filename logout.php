<?php
session_start();
session_destroy();
setcookie('logged_in', '', time()-3600, '/');
header("Location: index.php");
exit;
?>
