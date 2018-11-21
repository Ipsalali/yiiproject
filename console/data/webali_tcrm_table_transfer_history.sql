
--
-- Дамп данных таблицы `transfer_history`
--

INSERT INTO `transfer_history` (`id`, `entity_id`, `package_id`, `client_id`, `name`, `sum`, `sum_ru`, `comment`, `created_at`, `type_action`, `version`, `creator_id`, `isDeleted`, `course`, `currency`) VALUES
(1, 3, 2, 75, 'Lincoln Trading', 3228.63, 236981.44, '', '2018-07-31 15:18:28', 2, 1, 4, 0, NULL, NULL),
(2, 4, 2, 75, 'Свифт', 150, 11010, '', '2018-07-31 15:34:58', 2, 1, 4, 0, NULL, NULL),
(3, 1, 1, 101, 'Китай', 50000, 500000, '', '2018-08-04 04:39:21', 2, 2, 2, 0, 10, 1),
(4, 2, 1, 101, 'Свифт', 100, 1200, '', '2018-08-04 04:39:21', 2, 2, 2, 0, 12, 2);
