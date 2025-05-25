<?php
function get_items($sort_field = 'name', $order = 'ASC', $page = 1, $per_page = 5) {
    global $pdo;
    $offset = ($page - 1) * $per_page;

    $stmt = $pdo->prepare("SELECT * FROM items ORDER BY `$sort_field` $order LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
