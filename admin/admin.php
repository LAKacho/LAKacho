<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit;
}

// Логика для выхода из аккаунта
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: ../index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-container">
        <h2>Добро пожаловать в Админ-панель</h2>
        <nav>
            <ul>
                <li><a href="manage_users.php">Управление пользователями</a></li>
                <li><a href="manage_tests.php">Управление тестами</a></li>
                <li><a href="manage_access.php">Управление доступом к тестам</a></li>
                <li><a href="view_results.php">Просмотр результатов</a></li>
                <li><a href="?logout=true">Выйти</a></li>
            </ul>
        </nav>
    </div>
</body>
</html>
