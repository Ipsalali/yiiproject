
CREATE TABLE IF NOT EXISTS `transfers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `sum` double NOT NULL,
  `sum_ru` double NOT NULL,
  `comment` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `transfers_package`
--

CREATE TABLE IF NOT EXISTS `transfers_package` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `comment` text NOT NULL,
  `status_date` timestamp NULL DEFAULT NULL,
  `status` int(3) NOT NULL,
  `files` text  NULL DEFAULT NULL,
  `currency` int(3) NOT NULL,
  `course` double NOT NULL,
  `version_id` int(11) DEFAULT NULL,
  `isDeleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `transfers_package_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date` timestamp NULL DEFAULT NULL,
  `comment` text NOT NULL,
  `status_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` int(3) NOT NULL,
  `files` text NULL DEFAULT NULL,
  `currency` int(3) NOT NULL,
  `course` double NOT NULL,
  `version` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type_action` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `isDeleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;