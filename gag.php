<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fullscreen Mode on Click</title>
</head>
<body>
    <!-- Ваш существующий контент -->

    <script>
        // Функция для активации полноэкранного режима
        function toggleFullscreen() {
            let elem = document.documentElement; // Переход в полноэкранный режим для всего документа
            
            if (!document.fullscreenElement) {
                if (elem.requestFullscreen) {
                    elem.requestFullscreen();
                } else if (elem.mozRequestFullScreen) { // Firefox
                    elem.mozRequestFullScreen();
                } else if (elem.webkitRequestFullscreen) { // Chrome, Safari and Opera
                    elem.webkitRequestFullscreen();
                } else if (elem.msRequestFullscreen) { // IE/Edge
                    elem.msRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        // Привязываем событие нажатия ко всему документу
        document.body.addEventListener("click", toggleFullscreen);
    </script>
</body>
</html>