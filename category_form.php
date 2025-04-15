<?php
session_start();
require_once 'db.php';

// Инициализация переменных и массивов ошибок
$errors = [];
$old = [];
$isEdit = false;  // Режим добавления по умолчанию

// Если передан параметр для редактирования, загружаем данные категории
if (isset($_GET['id'])) {
    $isEdit = true;
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $category = $stmt->fetch();
    if (!$category) {
        // Если категории с таким id нет, можно перенаправить или показать ошибку
        echo "Категория не найдена.";
        exit;
    }
    // Заполняем старые значения из БД
    $old['name'] = $category['name'];
    $old['description'] = $category['description'];
}

// Если форма отправлена методом POST, обрабатываем данные
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // В режиме редактирования берем id из скрытого поля
    if ($isEdit || isset($_POST['id'])) {
        $isEdit = true;
        $id = intval($_POST['id']);
    }
    
    // Получаем данные и очищаем лишние пробелы
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $old['name'] = $name;
    $old['description'] = $description;

    // Валидация названия категории: обязательно, максимум 15 символов
    if (empty($name)) {
        $errors['name'] = "Название категории обязательно.";
    } elseif (mb_strlen($name) > 15) {
        $errors['name'] = "Название категории не должно превышать 15 символов.";
    }

    // Валидация описания: не обязательно, максимум 50 символов
    if (!empty($description) && mb_strlen($description) > 50) {
        $errors['description'] = "Описание не должно превышать 50 символов.";
    }

    // Если ошибок нет – сохраняем данные в базе
    if (empty($errors)) {
        if ($isEdit) {
            // Режим редактирования – UPDATE
            $stmt = $pdo->prepare("UPDATE categories SET name = ?, description = ? WHERE id = ?");
            $result = $stmt->execute([$name, $description, $id]);
        } else {
            // Режим добавления – INSERT
            $stmt = $pdo->prepare("INSERT INTO categories (name, description) VALUES (?, ?)");
            $result = $stmt->execute([$name, $description]);
        }

        if ($result) {
            // Перенаправляем на страницу списка категорий
            header("Location: category.php");
            exit;
        } else {
            $errors['form'] = "Ошибка при сохранении данных в базе.";
        }
    }
    
    // Если есть ошибки, их можно сохранить в сессии для вывода или сразу использовать локально
    $_SESSION['errors'] = $errors;
    $_SESSION['old'] = $old;
    // Если редактирование, можно оставить на той же странице, иначе редирект на форму добавления
    header("Location: " . ($_SERVER['REQUEST_URI']));
    exit;
}

// Если данные ошибок сохранены в сессии, извлекаем их и очищаем (для первого отображения формы)
if (isset($_SESSION['errors'])) {
    $errors = $_SESSION['errors'];
    $old = $_SESSION['old'];
    unset($_SESSION['errors'], $_SESSION['old']);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $isEdit ? "Редактирование" : "Создание" ?> категории</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 400px; }
        label { display: block; margin-bottom: 5px; }
        input[type="text"], textarea {
            width: 100%; padding: 8px; margin-bottom: 5px;
            border: 1px solid #ccc; border-radius: 4px;
        }
        .error-input {
            border-color: red; background-color: #ffe6e6;
        }
        .error-message {
            color: red; font-size: 13px; margin-bottom: 10px;
        }
        button { padding: 10px 20px; cursor: pointer; }
    </style>
</head>
<body>
    <h2><?= $isEdit ? "Редактирование категории" : "Создание новой категории" ?></h2>

    <!-- Если есть ошибка общей формы -->
    <?php if (isset($errors['form'])): ?>
        <div class="error-message"><?= htmlspecialchars($errors['form']) ?></div>
    <?php endif; ?>

    <form action="category_form.php<?= $isEdit ? '?id=' . $id : '' ?>" method="post">
        <?php if ($isEdit): ?>
            <!-- Скрытое поле для id в режиме редактирования -->
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
        <?php endif; ?>

        <label for="name">Название категории (обязательно, максимум 15 символов):</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($old['name'] ?? '') ?>"
               class="<?= isset($errors['name']) ? 'error-input' : '' ?>">
        <?php if (isset($errors['name'])): ?>
            <div class="error-message"><?= htmlspecialchars($errors['name']) ?></div>
        <?php endif; ?>

        <label for="description">Описание категории (не обязательно, максимум 50 символов):</label>
        <textarea id="description" name="description"
                  class="<?= isset($errors['description']) ? 'error-input' : '' ?>"><?= htmlspecialchars($old['description'] ?? '') ?></textarea>
        <?php if (isset($errors['description'])): ?>
            <div class="error-message"><?= htmlspecialchars($errors['description']) ?></div>
        <?php endif; ?>

        <button type="submit"><?= $isEdit ? "Сохранить изменения" : "Создать категорию" ?></button>
    </form>
    <?php if ($isEdit): ?>
        <p><a href="categories.php">Вернуться к списку категорий</a></p>
    <?php endif; ?>
</body>
</html>
