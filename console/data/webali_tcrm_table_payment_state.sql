
--
-- Дамп данных таблицы `payment_state`
--

INSERT INTO `payment_state` (`id`, `title`, `color`, `default_value`, `filter`, `end_state`, `sum_state`) VALUES
(1, 'Не оплачен', 'rgba(217, 24, 24, 0.77)', 1, 1, 0, 0),
(2, 'Частично оплачен', 'rgba(245, 245, 5, 1)', 0, 1, 0, 1),
(4, 'Оплачен', 'rgba(8, 107, 32, 0.73)', 0, 1, 1, 0),
(5, 'Переплата', 'rgba(58, 0, 230, 1)', 0, 1, 0, 1);
