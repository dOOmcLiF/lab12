<?php
session_start();
if (empty($_SESSION['user_id'])) {
    die("Требуется войти.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tmp_name = $_FILES['image']['tmp_name'];
    $name = basename($_FILES['image']['name']);
    $target = "uploads/" . uniqid() . "_" . $name;

    move_uploaded_file($tmp_name, $target);

    // Создание миниатюры
    create_thumbnail($target, "thumbs/" . $name);

    // Водяной знак
    add_watermark($target);
}
?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="image" required>
    <button type="submit">Загрузить</button>
</form>

<?php function create_thumbnail($source, $dest, $max_size = 200) { ... } ?>
<?php function add_watermark($source) { ... } ?>
