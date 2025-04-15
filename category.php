<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db.php';  // Подключаем базу данных

// Настройки пагинации
$limit = 5;  // записей на странице
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) {
    $page = 1;
}
$offset = ($page - 1) * $limit;

// Подсчитаем общее количество категорий для вычисления количества страниц
$stmtCount = $pdo->query("SELECT COUNT(*) as total FROM categories");
$total = $stmtCount->fetchColumn();

// Получим категории с учетом лимита и смещения
$stmt = $pdo->prepare("SELECT * FROM categories LIMIT ? OFFSET ?");
$stmt->bindValue(1, $limit, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$categories = $stmt->fetchAll();

// Вычисляем количество страниц
$totalPages = ceil($total / $limit);
?>



<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Список категорий</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .pagination {
            margin-top: 15px;
        }

        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            color: #000;
        }

        .pagination a.current {
            font-weight: bold;
        }

        .top-link {
            margin-bottom: 15px;
            display: inline-block;
        }
    </style>
</head>

<body>
    <h1>Список категорий</h1>
    <a class="top-link" href="category_form.php">Создать новую категорию</a>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Описание</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td><?= htmlspecialchars($cat['id']) ?></td>
                        <td><?= htmlspecialchars($cat['name']) ?></td>
                        <td><?= htmlspecialchars($cat['description']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Категорий не найдено.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Пагинация -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <?php if ($i == $page): ?>
                <a href="?page=<?= $i ?>" class="current"><?= $i ?></a>
            <?php else: ?>
                <a href="?page=<?= $i ?>"><?= $i ?></a>
            <?php endif; ?>
        <?php endfor; ?>
    </div>
</body>

</html>