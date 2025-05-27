<?php
require_once 'includes/db.php';

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: text/xml; charset=utf-8');
header('Content-Disposition: attachment; filename="users_export.xml"');

echo "<?xml version='1.0' encoding='UTF-8'?>\n";
echo "<users>\n";

foreach ($users as $user) {
    echo "  <user>\n";
    echo "    <id>" . htmlspecialchars($user['id']) . "</id>\n";
    echo "    <username>" . htmlspecialchars($user['username']) . "</username>\n";
    echo "    <email>" . htmlspecialchars($user['email']) . "</email>\n";
    echo "    <password_hash>" . htmlspecialchars($user['password_hash']) . "</password_hash>\n";
    echo "    <reset_token>" . htmlspecialchars($user['reset_token'] ?? '') . "</reset_token>\n";
    echo "    <token_expires>" . htmlspecialchars($user['token_expires'] ?? '') . "</token_expires>\n";
    echo "  </user>\n";
}

echo "</users>";
exit;
?>