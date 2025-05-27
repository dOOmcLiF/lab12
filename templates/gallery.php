<?php
session_start();
require_once 'includes/db.php';

// Получаем все фото с именами пользователей
$stmt = $pdo->query("
    SELECT images.*, users.username 
    FROM images 
    JOIN users ON images.user_id = users.id
    WHERE images.visible = 1
    ORDER BY uploaded_at DESC
");
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Все фотографии</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<h2>Все загруженные фотографии</h2>

<?php if (!empty($photos)): ?>
    <div class="gallery-grid">
        <?php foreach ($photos as $photo): ?>
            <div class="photo-card">
                <a href="#" onclick="openModal('<?= htmlspecialchars($photo['original_path']) ?>', '<?= htmlspecialchars($photo['username']) ?>', '<?= htmlspecialchars($photo['uploaded_at']) ?>', <?= $photo['id'] ?>)">
                    <img src="<?= htmlspecialchars($photo['thumb_path']) ?>" alt="Фото">
                </a>
                <div class="photo-info">
                    <p><strong><?= htmlspecialchars($photo['username']) ?></strong></p>
                    <p><?= date("d.m.Y H:i", strtotime($photo['uploaded_at'])) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>Пока нет загруженных фотографий.</p>
<?php endif; ?>


<!-- Модальное окно -->
<div id="photoModal" class="modal" onclick="closeModal(event)">
    <div class="modal-content" onclick="event.stopPropagation();">
        <span class="close-btn" onclick="closeModal()">×</span>
        <img id="modal-img" src="" alt="Фотография" style="max-width: 90vw; max-height: 80vh;">
        <div class="modal-details">
            <p><strong>Автор:</strong> <span id="modal-user"></span></p>
            <p><strong>Дата загрузки:</strong> <span id="modal-date"></span></p>
        </div>
    </div>
</div>

<script>
function openModal(imageSrc, username, uploadDate, photoId) {
    document.getElementById('modal-img').src = imageSrc;
    document.getElementById('modal-user').innerText = username;
    document.getElementById('modal-date').innerText = uploadDate;

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

</body>
</html>