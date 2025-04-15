<?php
session_start();

// Используем один и тот же ключ, например "errors"
$errors = $_SESSION["errors"] ?? [];
$old = $_SESSION["old"] ?? [];

// Очищаем сохранённые данные
unset($_SESSION["errors"], $_SESSION["old"]);
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>Вход в админ-панель</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2em;
        }

        form {
            width: 300px;
            margin: 0 auto;
        }

        input {
            display: block;
            margin-bottom: 5px;
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        /* Поле с ошибкой выделяется красным */
        .error-input {
            border-color: red;
            background-color: #ffe6e6;
        }

        .error-message {
            color: red;
            font-size: 13px;
            margin-bottom: 10px;
        }

        button {
            padding: 10px 20px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <h2>Вход в админ-панель</h2>
    <form action="login_process.php" method="post">

        <!-- Поле Email -->
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>"
            class="<?= isset($errors['email']) ? 'error-input' : '' ?>">
        <?php if (isset($errors['email'])): ?>
            <div class="error-message"><?= htmlspecialchars($errors['email']) ?></div>
        <?php endif; ?>

        <!-- Поле Пароль -->
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password"
            class="<?= isset($errors['password']) ? 'error-input' : '' ?>">
        <?php if (isset($errors['password'])): ?>
            <div class="error-message"><?= htmlspecialchars($errors['password']) ?></div>
        <?php endif; ?>

        <button type="submit">Войти</button>
    </form>
</body>

</html>