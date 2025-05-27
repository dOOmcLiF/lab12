<!-- templates/home_content.php -->
<h2>Последние загруженные фотографии</h2>

<?php if (!empty($photos)): ?>
    <div class="gallery-grid">
        <?php foreach ($photos as $photo): ?>
            <div class="photo-card">
                <a href="<?= htmlspecialchars($photo['original_path']) ?>" target="_blank">
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