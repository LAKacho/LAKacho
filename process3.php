<?php
include 'db.php';
include 'utils.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


$link=mysqli_connect("localhost", "u159215", "ZsX0seor!", "b159215_intern");
// Путь к файлу Excel
$filePath = 'registrations.xlsx';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tab_number']) && !isset($_POST['test'])) {
        $tab_number = $_POST['tab_number'];

        // Проверка табельного номера
        $stmt = $pdo2->prepare("SELECT * FROM names_sd WHERE login = ?");
        $stmt->execute([$tab_number]);
        $user = $stmt->fetch();

        if (!$user) {
            echo "<script>
                    alert('Ваш табельный номер отсутствует в системе. Обратитесь к администратору.');
                    window.location.href = 'index.php';
                  </script>";
            exit;
        }

        // Вывод меню тестов
        echo "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Выбор теста</title>
                <link rel='stylesheet' href='styles.css'>
            </head>
            <body>
                <div class='container'>
                    <h1>Выберите тест</h1>
                    <form action='process.php' method='POST'>
                        <input type='hidden' name='tab_number' value='$tab_number'>
                        <button name='test' value='Старшинство'>Старшинство</button>
                        <button name='test' value='Наставничество'>Наставничество</button>
                        <button name='test' value='Пересдача АБ'>Пересдача АБ</button>
                        <button name='test' value='Базовые управленческие навыки'>Базовые управленческие навыки</button>
                        <button name='test' value='Пересдача ТБ'>Пересдача ТБ</button>
                        <button name='test' value='Тренажеры'>Тренажеры</button>
                    </form>
                </div>
            </body>
            </html>
        ";
        exit;
    }

    if (isset($_POST['test'])) {
        $tab_number = $_POST['tab_number'];
        $test_name = $_POST['test'];
        $current_date = date('Y-m-d H:i:s');

        // Проверка ограничений
        if (!check_restrictions($pdo, $tab_number, $test_name)) {
            echo "<script>
                    alert('Вы не можете записаться на этот тест сейчас.');
                    window.location.href = 'index.php';
                  </script>";
            exit;
        }

        // Добавление записи в базу данных
        $stmt = $pdo->prepare("INSERT INTO test_registrations (tab_number, test_name, date_time) VALUES (?, ?, ?)");
        $stmt->execute([$tab_number, $test_name, $current_date]);

        // Запись в файл Excel
        if (file_exists($filePath)) {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
        } else {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Установка заголовков
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'Табельный номер');
            $sheet->setCellValue('C1', 'Название теста');
            $sheet->setCellValue('D1', 'Дата и время');
        }

        // Номер следующей строки
        $row = $sheet->getHighestRow() + 1;

        // Запись данных
        $sheet->setCellValue("A$row", $row - 1); // ID
        $sheet->setCellValue("B$row", $tab_number);
        $sheet->setCellValue("C$row", $test_name);
        $sheet->setCellValue("D$row", $current_date);

        // Сохранение Excel-файла
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        // Отправка уведомления администраторам
        include 'email_notify.php';
        sendNotification($tab_number, $test_name, $current_date);

        // Вывод сообщения об успешной записи с кнопкой на главное меню
        echo "
            <!DOCTYPE html>
            <html lang='en'>
            <head>
                <meta charset='UTF-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                <title>Запись успешна</title>
                <link rel='stylesheet' href='styles.css'>
            </head>
            <body>
                <div class='container'>
                    <h1>Запись успешна!</h1>
                    <p>Вы успешно записались на тест <b>$test_name</b>.<br>Занимайте свободный компьютер.</p>
                    <form action='index.php' method='GET'>
                        <button type='submit'>Вернуться на главное меню</button>
                    </form>
                </div>
            </body>
            </html>
        ";
    }
}
?>
