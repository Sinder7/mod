<?php

$dsn = "mysql:host=localhost;dbname=test_db";
$user = "root";
$password = "root";

try {
    $pdo = new PDO($dsn, $user, $password);
} catch (PDOException $e) {
    echo "" . $e->getMessage();
    exit();
}