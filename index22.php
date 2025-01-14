<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Система записи на тестирование</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header>
            <div class="header-content">
                <img src="logo.jpg" alt="Логотип" class="logo">
                <h1 class="title">Система записи на тестирование</h1>
                <form class="refresh-form" action="index.php" method="GET">
                    <button type="submit" class="refresh-button"></button>
                </form>
            </div>
        </header>
        <table class="button-table">
            <tr>
                <td>
                    <form action="process.php" method="POST">
                        <button name="test" value="Старшинство">
                            <img src="images/starshinstvo.png" alt="Старшинство">
                            <span>Старшинство</span>
                        </button>
                    </form>
                </td>
                <td>
                    <form action="process.php" method="POST">
                        <button name="test" value="Наставничество">
                            <img src="images/nastavnichestvo.png" alt="Наставничество">
                            <span>Наставничество</span>
                        </button>
                    </form>
                </td>
                <td>
                    <form action="process.php" method="POST">
                        <button name="test" value="Пересдача АБ">
                            <img src="images/ab_retake.png" alt="Пересдача АБ">
                            <span>Пересдача АБ</span>
                        </button>
                    </form>
                </td>
            </tr>
            <tr>
                <td>
                    <form action="process.php" method="POST">
                        <button name="test" value="Базовые навыки">
                            <img src="images/skills.png" alt="Базовые навыки">
                            <span>Базовые навыки</span>
                        </button>
                    </form>
                </td>
                <td>
                    <form action="process.php" method="POST">
                        <button name="test" value="Пересдача ТБ">
                            <img src="images/tb_retake.png" alt="Пересдача ТБ">
                            <span>Пересдача ТБ</span>
                        </button>
                    </form>
                </td>
                <td>
                    <form action="trening.php" method="POST">
                        <button name="test" value="Тренажеры">
                            <img src="images/training.png" alt="Тренажеры">
                            <span>Тренажеры</span>
                        </button>
                    </form>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
