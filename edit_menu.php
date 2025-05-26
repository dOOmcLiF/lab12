<?php
$stmt = $pdo->query("SELECT * FROM menu ORDER BY position");
$menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<form method="post">
<?php foreach ($menu_items as $item): ?>
    <div>
        <input type="text" name="title[<?= $item['id'] ?>]" value="<?= $item['title'] ?>">
        <input type="text" name="url[<?= $item['id'] ?>]" value="<?= $item['url'] ?>">
        <input type="number" name="position[<?= $item['id'] ?>]" value="<?= $item['position'] ?>">
        <label><input type="checkbox" name="visible[<?= $item['id'] ?>]" <?= $item['visible'] ? 'checked' : '' ?>> Отображать</label>
    </div>
<?php endforeach; ?>
<button type="submit">Сохранить</button>
</form>
