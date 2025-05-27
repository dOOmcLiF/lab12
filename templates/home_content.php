<h2>Последние загруженные фотографии</h2>

<?php if (!empty($photos)): ?>
    <div class="gallery-grid">
        <?php foreach ($photos as $photo): ?>
            <div class="photo-card">
                <a href="#" onclick="openModal('<?= htmlspecialchars($photo['original_path']) ?>', '<?= htmlspecialchars($photo['username']) ?>', '<?= htmlspecialchars($photo['uploaded_at']) ?>')">
                    <img src="<?= htmlspecialchars($photo['thumb_path']) ?>" alt="Миниатюра">
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

<div id="photoModal" class="modal" onclick="closeModal(event)">
    <div class="modal-content" onclick="event.stopPropagation();">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <img id="modal-img" src="" alt="Фотография" style="max-width: 90vw; max-height: 80vh;">
        <div class="modal-details">
            <p><strong>Автор:</strong> <span id="modal-user"></span></p>
            <p><strong>Дата загрузки:</strong> <span id="modal-date"></span></p>
        </div>
    </div>
</div>

<script>
function openModal(imageSrc, username, uploadDate) {
    const modalImg = document.getElementById('modal-img');
    modalImg.src = imageSrc;
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