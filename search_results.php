<?php
require_once 'includes/db.php';
$query = "%" . $_GET['query'] . "%";
$stmt = $pdo->prepare("SELECT * FROM items WHERE name LIKE ? OR description LIKE ?");
$stmt->execute([$query, $query]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<h2>Результаты поиска:</h2>
<ul>
<?php foreach ($results as $item): ?>
    <li><?= htmlspecialchars($item['name']) ?></li>
<?php endforeach; ?>
</ul>
