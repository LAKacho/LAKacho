<?php
session_start();
require '../db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: ../index.php');
    exit;
}

// Получение списка пользователей для фильтрации
$userQuery = "SELECT id, username FROM users";
$userStmt = $pdo->query($userQuery);
$users = $userStmt->fetchAll(PDO::FETCH_ASSOC);

// Проверка фильтра и формирование запроса
$filters = [];
$params = [];

// Применение фильтра по пользователю
if (!empty($_GET['user_id'])) {
    $filters[] = 'u.id = :user_id';
    $params['user_id'] = $_GET['user_id'];
}

// Применение фильтра по названию теста
if (!empty($_GET['test_title'])) {
    $filters[] = 't.title LIKE :test_title';
    $params['test_title'] = '%' . $_GET['test_title'] . '%';
}

// Формирование основного запроса с присоединением таблицы tests для получения названия теста
$query = "SELECT r.id, u.username AS username, t.title AS test_title, r.start_time, r.end_time, 
                 r.correct_answers, r.total_questions, r.score, r.passed 
          FROM test_results r
          JOIN users u ON r.user_id = u.id
          JOIN tests t ON r.test_id = t.id";

// Добавляем условия фильтрации, если они заданы
if ($filters) {
    $query .= ' WHERE ' . implode(' AND ', $filters);
}

$query .= " ORDER BY r.start_time DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Просмотр результатов тестирования</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="view-results-container">
        <h2>Результаты тестирования</h2>

        <!-- Форма фильтрации по пользователю и тесту -->
        <form action="view_results.php" method="get">
            <label>Фильтровать по пользователю:</label>
            <select name="user_id">
                <option value="">Все пользователи</option>
                <?php foreach ($users as $user): ?>
                    <option value="<?= $user['id'] ?>" <?= isset($_GET['user_id']) && $_GET['user_id'] == $user['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($user['username']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Название теста:</label>
            <input type="text" name="test_title" placeholder="Введите название теста" value="<?= htmlspecialchars($_GET['test_title'] ?? '') ?>">
            <button type="submit">Применить фильтр</button>
        </form>

        <!-- Таблица результатов -->
        <table>
            <thead>
                <tr>
                    <th>Пользователь</th>
                    <th>Название теста</th>
                    <th>Время начала</th>
                    <th>Время окончания</th>
                    <th>Правильные ответы</th>
                    <th>Всего вопросов</th>
                    <th>Результат (%)</th>
                    <th>Оценка</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $result): ?>
                    <tr>
                        <td><?= htmlspecialchars($result['username']) ?></td>
                        <td><?= htmlspecialchars($result['test_title']) ?></td>
                        <td><?= htmlspecialchars($result['start_time']) ?></td>
                        <td><?= htmlspecialchars($result['end_time']) ?></td>
                        <td><?= $result['correct_answers'] ?></td>
                        <td><?= $result['total_questions'] ?></td>
                        <td><?= round($result['score'], 2) ?>%</td>
                        <td><?= $result['passed'] ? 'Пройден' : 'Не пройден' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
