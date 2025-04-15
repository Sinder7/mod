<?php
session_start();
require_once 'db.php';

if (!isset($_GET['id'])) {
    echo "Не указан идентификатор категории.";
    exit;
}

$id = intval($_GET['id']);

// Здесь можно добавить проверку, есть ли товары в категории, и запретить удаление, если они есть
$stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
$stmtCheck->execute([$id]);
$count = $stmtCheck->fetchColumn();

if ($count > 0) {
    echo "Невозможно удалить категорию, так как в ней присутствуют товары.";
    exit;
}

// Удаляем категорию
$stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
if ($stmt->execute([$id])) {
    header("Location: category.php");
    exit;
} else {
    echo "Ошибка при удалении категории.";
}
?>
