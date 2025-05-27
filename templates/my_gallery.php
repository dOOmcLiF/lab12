<?php
session_start();
require_once 'includes/db.php';

if (empty($_SESSION['user_id'])) {
    header("Location: /?page=login");
    exit;
}

// Получаем все фото пользователя
$stmt = $pdo->prepare("SELECT images.*, users.username FROM images JOIN users ON images.user_id = users.id WHERE images.user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Моя галерея</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<h2>Моя галерея</h2>

<?php if (!empty($photos)): ?>
    <div class="gallery-grid">
        <?php foreach ($photos as $photo): ?>
            <div class="photo-card">
                <a href="#" onclick="openModal('<?= htmlspecialchars($photo['original_path']) ?>', '<?= htmlspecialchars($photo['username']) ?>', '<?= htmlspecialchars($photo['uploaded_at']) ?>', <?= $photo['id'] ?>)">
                    <img src="<?= htmlspecialchars($photo['thumb_path']) ?>" alt="Фото">
                </a>
                <p><?= date("d.m.Y H:i", strtotime($photo['uploaded_at'])) ?></p>

                <form action="delete_photo.php" method="post" style="display:inline;">
                    <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                    <button type="submit" onclick="return confirm('Вы уверены?')">Удалить</button>
                </form>

                <form action="toggle_visibility.php" method="post" style="display:inline;">
                    <input type="hidden" name="photo_id" value="<?= $photo['id'] ?>">
                    <button type="submit">
                        <?= $photo['visible'] ? 'Скрыть' : 'Показать' ?>
                    </button>
                </form>

                <p>
                    <?php if ($photo['visible']): ?>
                        <span style="color: green;">✅ Видимое</span>
                    <?php else: ?>
                        <span style="color: red;">❌ Скрыто</span>
                    <?php endif; ?>
                </p>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>У вас пока нет загруженных фотографий.</p>
<?php endif; ?>

<p><a href="/?page=upload_image">← Загрузить новое фото</a></p>

<div id="photoModal" class="modal" onclick="closeModal(event)">
    <div class="modal-content" onclick="event.stopPropagation();">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <img id="modal-img" src="" alt="Фотография" style="max-width: 90vw; max-height: 80vh;">
        <div class="modal-details">
            <p><strong>Автор:</strong> <span id="modal-user"></span></p>
            <p><strong>Дата загрузки:</strong> <span id="modal-date"></span></p>
            <form id="modal-delete-form" action="delete_photo.php" method="post">
                <input type="hidden" id="modal-photo-id" name="photo_id" value="">
                <button type="submit" onclick="return confirm('Вы уверены?')">Удалить это фото</button>
            </form>
        </div>
    </div>
</div>

<script>
function openModal(imageSrc, username, uploadDate, photoId) {
    document.getElementById('modal-img').src = imageSrc;
    document.getElementById('modal-user').innerText = username;
    document.getElementById('modal-date').innerText = uploadDate;
    document.getElementById('modal-photo-id').value = photoId;
    document.getElementById('photoModal').style.display = "block";
}

function closeModal() {
    document.getElementById('photoModal').style.display = "none";
}

// Закрытие по клику вне окна
window.onclick = function(event) {
    const modal = document.getElementById('photoModal');
    if (event.target === modal) {
        closeModal();
    }
}
</script>