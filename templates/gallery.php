<?php
session_start();
require_once 'includes/db.php';

// Получаем параметры фильтрации из GET
$author = $_GET['author'] ?? '';
$date_input = $_GET['date'] ?? '';
$date_for_sql = '';

// Преобразование DD/MM/YYYY → YYYY-MM-DD
if (!empty($date_input)) {
    $date_obj = DateTime::createFromFormat('d/m/Y', $date_input);
    if ($date_obj) {
        $date_for_sql = $date_obj->format('Y-m-d');
    }
}

// Подготавливаем SQL запрос с возможностью фильтрации
$sql = "
    SELECT images.*, users.username 
    FROM images 
    JOIN users ON images.user_id = users.id
    WHERE images.visible = 1
";

$params = [];

if (!empty($author)) {
    $sql .= " AND users.username LIKE :author";
    $params[':author'] = "%$author%";
}

if (!empty($date_for_sql)) {
    $sql .= " AND DATE(images.uploaded_at) = :date";
    $params[':date'] = $date_for_sql;
}

$sql .= " ORDER BY images.uploaded_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Галерея фотографий</title>
    <link rel="stylesheet" href="assets/style.css">
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css ">

    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr "></script>
</head>
<body>

<h2>Фильтр фотографий</h2>

<!-- Форма фильтрации -->
<form method="get" action="/?page=gallery">
    <input type="hidden" name="page" value="<?= htmlspecialchars($_GET['page'] ?? '') ?>">
    <label for="author">Имя автора:</label>
    <input type="text" id="author" name="author" value="<?= htmlspecialchars($author) ?>">

    <label for="date">Дата загрузки (ДД/ММ/ГГГГ):</label>
    <input type="text" id="date" name="date" placeholder="дд/мм/гггг" value="<?= htmlspecialchars($date_input) ?>" readonly>

    <button type="submit">Применить фильтр</button>
</form>

<hr>

<h2>Результаты</h2>

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
    <p>Нет фотографий, удовлетворяющих условиям фильтра.</p>
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
<script>
    flatpickr("#date", {
        dateFormat: "d/m/Y",     // Формат отображения
        altFormat: "d/m/Y",      // Альтернативный формат
        allowInput: false,       // Запрет ручного ввода
        locale: {
            firstDayOfWeek: 1    // Начать неделю с понедельника
        }
    });
</script>
</body>
</html>