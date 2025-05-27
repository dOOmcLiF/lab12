<?php
session_start();
require_once 'includes/db.php';

// Параметры пагинации
$photos_per_page = 6;
$page = $_GET['p'] ?? 1;
$page = is_numeric($page) ? (int)$page : 1;

// Получаем параметры фильтрации
$author = $_GET['author'] ?? '';
$date_input = $_GET['date'] ?? '';
$date_for_sql = '';

// Преобразование даты DD/MM/YYYY → YYYY-MM-DD
if (!empty($date_input)) {
    $date_obj = DateTime::createFromFormat('d/m/Y', $date_input);
    if ($date_obj) {
        $date_for_sql = $date_obj->format('Y-m-d');
    }
}

// Подсчёт общего количества фото с учетом фильтров
$count_sql = "
    SELECT COUNT(*) 
    FROM images 
    JOIN users ON images.user_id = users.id
    WHERE images.visible = 1
";
$params = [];

if (!empty($author)) {
    $count_sql .= " AND users.username LIKE :author";
    $params[':author'] = "%$author%";
}
if (!empty($date_for_sql)) {
    $count_sql .= " AND DATE(images.uploaded_at) = :date";
    $params[':date'] = $date_for_sql;
}

$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_photos = $stmt->fetchColumn();
$total_pages = ceil($total_photos / $photos_per_page);

// Ограничиваем номер страницы диапазоном
if ($page < 1) $page = 1;
if ($page > $total_pages) $page = $total_pages;

// Основной запрос с фильтрами и пагинацией
$sql = "
    SELECT images.*, users.username 
    FROM images 
    JOIN users ON images.user_id = users.id
    WHERE images.visible = 1
";

if (!empty($author)) {
    $sql .= " AND users.username LIKE :author";
}
if (!empty($date_for_sql)) {
    $sql .= " AND DATE(images.uploaded_at) = :date";
}

$sql .= " ORDER BY images.uploaded_at DESC ";
$sql .= " LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);
$params[':limit'] = $photos_per_page;
$params[':offset'] = ($page - 1) * $photos_per_page;

$stmt->execute($params);
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Галерея фотографий</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css ">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr "></script>
</head>
<body>

<h2>Галерея</h2>

<!-- Форма фильтрации -->
<form method="get" action="index.php" class="filter-form">
    <input type="hidden" name="page" value="<?= htmlspecialchars($_GET['page'] ?? 'gallery') ?>">

    <h3>Фильтр фотографий</h3>

    <label for="author">Имя автора:</label>
    <input type="text" id="author" name="author" value="<?= htmlspecialchars($author) ?>" placeholder="Введите имя автора">

    <label for="date">Дата загрузки (ДД/ММ/ГГГГ):</label>
    <input type="text" id="date" name="date" placeholder="дд/мм/гггг" value="<?= htmlspecialchars($date_input) ?>" readonly>

    <button type="submit">Применить фильтр</button>
</form>

<hr>

<h2>Фотографии</h2>

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

<!-- Пагинация -->
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?page=gallery&p=<?= $page - 1 ?>&author=<?= urlencode($author) ?>&date=<?= urlencode($date_input) ?>">&laquo; Назад</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <?php if ($i == $page): ?>
            <strong><?= $i ?></strong>
        <?php else: ?>
            <a href="?page=gallery&p=<?= $i ?>&author=<?= urlencode($author) ?>&date=<?= urlencode($date_input) ?>"><?= $i ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($page < $total_pages): ?>
        <a href="?page=gallery&p=<?= $page + 1 ?>&author=<?= urlencode($author) ?>&date=<?= urlencode($date_input) ?>">Вперёд &raquo;</a>
    <?php endif; ?>
</div>

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
        dateFormat: "d/m/Y",
        altFormat: "d/m/Y",
        allowInput: false,
        locale: {
            firstDayOfWeek: 1
        }
    });
</script>

</body>
</html>