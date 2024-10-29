// timer.js

let timeLeft = parseInt(document.getElementById('timer').getAttribute('data-time'), 10);
const timerDisplay = document.getElementById('timer');

const countdown = setInterval(() => {
    if (timeLeft <= 0) {
        clearInterval(countdown);
        alert("Время теста истекло!");
        document.getElementById("test-form").submit(); // Отправляем форму, завершив тест
    } else {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerDisplay.innerText = `Оставшееся время: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        timeLeft--;
    }
}, 1000);
