<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

$error = '';

// Добавление пользователя
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = $_POST['login'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    // Проверка уникальности имени пользователя
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $exists = $stmt->fetchColumn();

    if ($exists) {
        $error = "Пользователь с таким логином уже существует!";
    } else {
        // Добавляем пользователя, если логин уникален
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role) VALUES (:username, :password, :role)");
        $stmt->execute(['username' => $username, 'password' => $password, 'role' => $role]);
    }
}

// Получение списка пользователей
$stmt = $pdo->query("SELECT id, username, role FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Получение списка тестов
$testStmt = $pdo->query("SELECT id, title FROM tests"); // Таблица tests с полями id и title
$tests = $testStmt->fetchAll(PDO::FETCH_ASSOC);

// Обновление доступа к тестам для пользователя
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_access'])) {
    $userId = $_POST['user_id'];
    $selectedTests = $_POST['test_access'] ?? [];

    // Очистка текущего доступа
    $pdo->prepare("DELETE FROM user_test_access WHERE user_id = :user_id")->execute(['user_id' => $userId]);

    // Добавление выбранных тестов
    $stmt = $pdo->prepare("INSERT INTO user_test_access (user_id, test_id) VALUES (:user_id, :test_id)");
    foreach ($selectedTests as $testId) {
        $stmt->execute(['user_id' => $userId, 'test_id' => $testId]);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление пользователями</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="manage-users-container">
        <h2>Управление пользователями</h2>
        
        <!-- Отображение сообщения об ошибке -->
        <?php if ($error) { echo "<p style='color: red;'>$error</p>"; } ?>
        
        <!-- Форма добавления нового пользователя -->
        <form method="post" action="manage_users.php">
            <h3>Добавить нового пользователя</h3>
            <label>Логин:</label>
            <input type="text" name="login" required>
            
            <label>Пароль:</label>
            <input type="password" name="password" required>
            
            <label>Роль:</label>
            <select name="role">
                <option value="user">Пользователь</option>
                <option value="admin">Администратор</option>
            </select>
            
            <button type="submit" name="add_user">Добавить</button>
        </form>

        <!-- Таблица пользователей -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Логин</th>
                    <th>Роль</th>
                    <th>Доступ к тестам</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= $user['role'] ?></td>
                        <td>
                            <!-- Форма управления доступом к тестам -->
                            <form action="manage_users.php" method="post">
                                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                
                                <?php foreach ($tests as $test): ?>
                                    <?php
                                    // Проверка, есть ли доступ к тесту
                                    $accessCheckStmt = $pdo->prepare("SELECT COUNT(*) FROM user_test_access WHERE user_id = :user_id AND test_id = :test_id");
                                    $accessCheckStmt->execute(['user_id' => $user['id'], 'test_id' => $test['id']]);
                                    $hasAccess = $accessCheckStmt->fetchColumn() > 0;
                                    ?>
                                    <label>
                                        <input type="checkbox" name="test_access[]" value="<?= $test['id'] ?>" <?= $hasAccess ? 'checked' : '' ?>>
                                        <?= htmlspecialchars($test['title']) ?>
                                    </label><br>
                                <?php endforeach; ?>

                                <button type="submit" name="update_access">Обновить доступ</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
