<?php
session_start();
require_once 'db.php';  // Подключение к базе данных

$errors = [];  // Массив для ошибок
$old = [];     // Массив для сохранения старых значений формы

// Получаем данные из формы (предполагается, что форма отправлена методом POST)
$email = trim($_POST["email"] ?? '');
$password = trim($_POST["password"] ?? '');

// Сохраняем старые значения (например, email)
$old["email"] = $email;

// Валидация
if (empty($email)) {
    $errors['email'] = 'Поле Email обязательно для заполнения.';
}
if (empty($password)) {
    $errors['password'] = 'Поле Пароль обязательно для заполнения.';
}

// Если есть ошибки валидации, сохраняем их в сессии и возвращаем пользователя на страницу авторизации
if (!empty($errors)) {
    $_SESSION["errors"] = $errors;
    $_SESSION["old"] = $old;
    header("Location: login.php");
    exit;
}

// Подготовка и выполнение запроса (передаем параметры в виде массива)
$stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ?");
$stmt->execute([$email]);
$admin = $stmt->fetch();

// Проверяем, найден ли администратор и корректен ли пароль
if (!$admin || $admin["password"] != $password) {
    $errors["email"] = "Неверный email или пароль.";  // Добавляем ошибку в массив
    $_SESSION["errors"] = $errors;
    $_SESSION["old"] = $old;
    header("Location: login.php");
    exit;
}

// Если данные верные — создаем сессию администратора
$_SESSION["admin"] = [
    'id' => $admin['id'],
    'email' => $admin['email'],
];

// Перенаправляем на главную админской панели
header("Location: index.php");
exit;
?>