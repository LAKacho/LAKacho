-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Ноя 12 2024 г., 16:34
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `1test_system`
--

-- --------------------------------------------------------

--
-- Структура таблицы `tests`
--

CREATE TABLE `tests` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `questions_json` text NOT NULL,
  `file_path` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `test_results`
--

CREATE TABLE `test_results` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `test_id` varchar(255) CHARACTER SET armscii8 COLLATE armscii8_general_nopad_ci DEFAULT NULL,
  `test_title` varchar(255) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `correct_answers` int(11) DEFAULT 0,
  `total_questions` int(11) DEFAULT 0,
  `score` decimal(5,2) DEFAULT 0.00,
  `passed` tinyint(1) DEFAULT 0,
  `total_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `test_results`
--

INSERT INTO `test_results` (`id`, `user_id`, `test_id`, `test_title`, `start_time`, `end_time`, `correct_answers`, `total_questions`, `score`, `passed`, `total_time`) VALUES
(16, 5, 'test1', '', '2024-11-10 07:44:26', '2024-11-10 07:47:14', 0, 2, 0.00, 0, 168);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'B444', 'B444', 'user'),
(2, 'admin', 'adminpassword', 'admin'),
(5, 'B333', 'B333', 'user'),
(43, 'B345', 'B345', 'user'),
(44, 'B346', 'B346', 'user'),
(45, 'B347', 'B347', 'user'),
(46, 'B348', 'B348', 'user'),
(47, 'B349', 'B349', 'user'),
(48, 'B350', 'B350', 'user'),
(49, 'B351', 'B351', 'user'),
(50, 'B352', 'B352', 'user'),
(51, 'B353', 'B353', 'user'),
(52, 'B354', 'B354', 'user'),
(53, 'B355', 'B355', 'user'),
(54, 'B356', 'B356', 'user'),
(55, 'B357', 'B357', 'user'),
(56, 'B358', 'B358', 'user'),
(57, 'B359', 'B359', 'user'),
(58, 'B360', 'B360', 'user'),
(59, 'B361', 'B361', 'user'),
(60, 'B362', 'B362', 'user'),
(61, 'B363', 'B363', 'user'),
(62, 'B364', 'B364', 'user');

-- --------------------------------------------------------

--
-- Структура таблицы `user_answers`
--

CREATE TABLE `user_answers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `test_id` varchar(255) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL,
  `answer_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `user_answers`
--

INSERT INTO `user_answers` (`id`, `user_id`, `test_id`, `question_id`, `answer`, `is_correct`, `answer_time`) VALUES
(1, 43, 'test1', 2, '[\"1\"]', 0, '2024-11-03 10:50:15'),
(2, 43, 'test1', 1, '[\"2\"]', 0, '2024-11-03 10:50:18'),
(3, 57, 'test1', 2, '[\"1\"]', 0, '2024-11-04 05:47:04'),
(4, 57, 'test1', 1, '[\"2\"]', 0, '2024-11-04 05:47:05'),
(5, 60, 'test1', 1, '[\"0\"]', 1, '2024-11-04 05:49:23'),
(6, 60, 'test1', 2, '[\"0\",\"2\",\"3\"]', 0, '2024-11-04 05:49:28'),
(7, 58, 'test1', 2, '[\"1\"]', 0, '2024-11-04 06:04:42'),
(8, 58, 'test1', 1, '[\"1\"]', 0, '2024-11-04 06:04:44'),
(9, 59, 'test1', 1, '[\"3\"]', 0, '2024-11-04 08:21:43'),
(10, 59, 'test1', 2, '[\"1\"]', 0, '2024-11-04 08:21:44'),
(11, 61, 'test1', 1, '[\"1\"]', 0, '2024-11-04 08:24:32'),
(12, 61, 'test1', 2, '[\"2\"]', 0, '2024-11-04 08:24:34'),
(13, 45, 'test1', 1, '[\"1\"]', 0, '2024-11-04 08:39:24'),
(14, 45, 'test1', 2, '[\"1\"]', 0, '2024-11-04 08:39:27'),
(15, 46, 'test1', 1, '[\"1\"]', 0, '2024-11-04 08:43:39'),
(16, 46, 'test1', 2, '[\"2\"]', 0, '2024-11-04 08:43:42'),
(17, 54, 'test1', 1, '[\"1\"]', 0, '2024-11-04 08:44:33'),
(18, 54, 'test1', 2, '[\"1\",\"2\"]', 0, '2024-11-04 08:44:39'),
(19, 5, 'test1', 2, '[\"2\"]', 0, '2024-11-10 06:47:11'),
(20, 5, 'test1', 1, '[\"1\"]', 0, '2024-11-10 06:47:14');

-- --------------------------------------------------------

--
-- Структура таблицы `user_attempts`
--

CREATE TABLE `user_attempts` (
  `attempt_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `test_id` varchar(255) CHARACTER SET armscii8 COLLATE armscii8_general_nopad_ci NOT NULL,
  `attempt_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `score` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `answer` text DEFAULT NULL,
  `answer_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `user_attempts`
--

INSERT INTO `user_attempts` (`attempt_id`, `user_id`, `test_id`, `attempt_date`, `score`, `question_id`, `answer`, `answer_time`) VALUES
(3, 5, '0', '2024-10-30 19:03:47', 0, NULL, NULL, '2024-11-03 09:39:17'),
(4, 47, '0', '2024-11-03 10:41:16', 0, NULL, NULL, '2024-11-03 10:41:16'),
(5, 43, '0', '2024-11-03 10:50:18', 0, NULL, NULL, '2024-11-03 10:50:18'),
(6, 57, '0', '2024-11-04 05:47:05', 0, NULL, NULL, '2024-11-04 05:47:05'),
(7, 60, '0', '2024-11-04 05:49:28', 50, NULL, NULL, '2024-11-04 05:49:28'),
(8, 58, '0', '2024-11-04 06:04:44', 0, NULL, NULL, '2024-11-04 06:04:44'),
(9, 59, '0', '2024-11-04 08:21:44', 0, NULL, NULL, '2024-11-04 08:21:44'),
(10, 61, 'test1', '2024-11-04 08:24:34', 0, NULL, NULL, '2024-11-04 08:24:34'),
(11, 61, 'test1', '2024-11-04 08:26:14', 0, NULL, NULL, '2024-11-04 08:26:14'),
(12, 45, 'test1', '2024-11-04 08:39:27', 0, NULL, NULL, '2024-11-04 08:39:27'),
(13, 46, 'test1', '2024-11-04 08:43:42', 0, NULL, NULL, '2024-11-04 08:43:42'),
(14, 54, 'test1', '2024-11-04 08:44:39', 0, NULL, NULL, '2024-11-04 08:44:39'),
(15, 5, 'test1', '2024-11-10 06:47:14', 0, NULL, NULL, '2024-11-10 06:47:14');

-- --------------------------------------------------------

--
-- Структура таблицы `user_test_access`
--

CREATE TABLE `user_test_access` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `test_id` varchar(255) DEFAULT NULL,
  `access_level` tinyint(1) DEFAULT 1,
  `completed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `user_test_access`
--

INSERT INTO `user_test_access` (`id`, `user_id`, `test_id`, `access_level`, `completed`) VALUES
(24, 43, 'test1', 1, 1),
(25, 44, 'test1', 1, 0),
(26, 45, 'test1', 1, 1),
(27, 46, 'test1', 1, 1),
(28, 47, 'test1', 1, 1),
(29, 48, 'test1', 1, 0),
(31, 50, 'test1', 1, 0),
(32, 51, 'test1', 1, 0),
(33, 52, 'test1', 1, 0),
(34, 53, 'test1', 1, 0),
(35, 54, 'test1', 1, 1),
(36, 55, 'test1', 1, 0),
(37, 56, 'test1', 1, 0),
(38, 57, 'test1', 1, 1),
(39, 58, 'test1', 1, 1),
(40, 59, 'test1', 1, 1),
(41, 60, 'test1', 1, 1),
(42, 61, 'test1', 1, 1),
(43, 62, 'test1', 1, 0),
(44, 49, 'test2', 1, 0),
(45, 5, '%D0%B0%D0%B8%D0%B0%D0%B8%D0%B0%D0%B8', 1, 0),
(46, 5, '31', 1, 0),
(47, 5, 'asdf', 1, 0),
(48, 5, 'caf', 1, 0),
(49, 5, 'test1', 1, 1),
(50, 5, 'test2', 1, 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `test_results`
--
ALTER TABLE `test_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Индексы таблицы `user_answers`
--
ALTER TABLE `user_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `user_attempts`
--
ALTER TABLE `user_attempts`
  ADD PRIMARY KEY (`attempt_id`);

--
-- Индексы таблицы `user_test_access`
--
ALTER TABLE `user_test_access`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `test_id` (`test_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `test_results`
--
ALTER TABLE `test_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT для таблицы `user_answers`
--
ALTER TABLE `user_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `user_attempts`
--
ALTER TABLE `user_attempts`
  MODIFY `attempt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `user_test_access`
--
ALTER TABLE `user_test_access`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `test_results`
--
ALTER TABLE `test_results`
  ADD CONSTRAINT `test_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `user_answers`
--
ALTER TABLE `user_answers`
  ADD CONSTRAINT `user_answers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `user_test_access`
--
ALTER TABLE `user_test_access`
  ADD CONSTRAINT `user_test_access_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
