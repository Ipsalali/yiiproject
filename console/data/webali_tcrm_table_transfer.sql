
--
-- Дамп данных таблицы `transfer`
--

INSERT INTO `transfer` (`id`, `package_id`, `client_id`, `name`, `sum`, `sum_ru`, `comment`, `version_id`, `isDeleted`, `course`, `currency`) VALUES
(1, 1, 101, 'Китай', 50000, 500000, '', 3, 0, 10, 1),
(2, 1, 101, 'Свифт', 100, 1200, '', 4, 0, 12, 2),
(3, 2, 75, 'Lincoln Trading', 3228.63, 236981.44, '', 1, 0, NULL, NULL),
(4, 2, 75, 'Свифт', 150, 11010, '', 2, 0, NULL, NULL);
