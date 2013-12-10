-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.27 - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL Version:             8.1.0.4545
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table faktury.admin_module
DROP TABLE IF EXISTS `admin_module`;
CREATE TABLE IF NOT EXISTS `admin_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Číslo modulu',
  `name` varchar(255) NOT NULL COMMENT 'Meno modulu',
  `table` varchar(255) NOT NULL COMMENT 'Tabuľka',
  `allow_export` enum('1','0') DEFAULT '1' COMMENT 'Povoliť export',
  `active` enum('1','0') DEFAULT '1' COMMENT 'Aktívny',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- Dumping data for table faktury.admin_module: ~3 rows (approximately)
DELETE FROM `admin_module`;
/*!40000 ALTER TABLE `admin_module` DISABLE KEYS */;
INSERT INTO `admin_module` (`id`, `name`, `table`, `allow_export`, `active`) VALUES
  (1, 'moduleList', 'admin_module', '1', '1'),
  (8, 'theme', 'theme', '1', '1'),
  (9, 'user', 'user', '1', '1');
/*!40000 ALTER TABLE `admin_module` ENABLE KEYS */;


-- Dumping structure for table faktury.admin_module_column
DROP TABLE IF EXISTS `admin_module_column`;
CREATE TABLE IF NOT EXISTS `admin_module_column` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_module_id` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `replacement_table` varchar(255) DEFAULT NULL,
  `replacement_id_column` varchar(255) DEFAULT NULL,
  `replacement_name_column` varchar(255) DEFAULT NULL,
  `editable` enum('1','0') DEFAULT '0',
  `viewable` enum('1','0') DEFAULT '0',
  `active` enum('1','0') DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 3` (`admin_module_id`,`name`),
  KEY `FK_admin_module_column_admin_module` (`admin_module_id`),
  CONSTRAINT `FK_admin_module_column_admin_module` FOREIGN KEY (`admin_module_id`) REFERENCES `admin_module` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=259 DEFAULT CHARSET=utf8;

-- Dumping data for table faktury.admin_module_column: ~19 rows (approximately)
DELETE FROM `admin_module_column`;
/*!40000 ALTER TABLE `admin_module_column` DISABLE KEYS */;
INSERT INTO `admin_module_column` (`id`, `admin_module_id`, `name`, `replacement_table`, `replacement_id_column`, `replacement_name_column`, `editable`, `viewable`, `active`) VALUES
  (4, 1, 'name', NULL, NULL, NULL, '0', '1', '1'),
  (5, 1, 'table', NULL, NULL, NULL, '0', '1', '1'),
  (6, 1, 'allow_export', NULL, NULL, NULL, '0', '1', '1'),
  (7, 1, 'active', NULL, NULL, NULL, '0', '1', '1'),
  (10, 1, 'id', NULL, NULL, NULL, '0', '1', '1'),
  (245, 8, 'id', NULL, NULL, NULL, '0', '1', '1'),
  (246, 8, 'name', NULL, NULL, NULL, '1', '1', '1'),
  (247, 8, 'theme_folder', NULL, NULL, NULL, '1', '1', '1'),
  (248, 8, 'host', NULL, NULL, NULL, '1', '1', '1'),
  (249, 8, 'active', NULL, NULL, NULL, '1', '1', '1'),
  (250, 9, 'id', NULL, NULL, NULL, '0', '1', '1'),
  (251, 9, 'nickname', NULL, NULL, NULL, '0', '1', '1'),
  (252, 9, 'nickname_webalized', NULL, NULL, NULL, '0', '0', '1'),
  (253, 9, 'email', NULL, NULL, NULL, '0', '1', '1'),
  (254, 9, 'password', NULL, NULL, NULL, '0', '0', '1'),
  (255, 9, 'create_date', NULL, NULL, NULL, '0', '1', '1'),
  (256, 9, 'hash', NULL, NULL, NULL, '0', '0', '1'),
  (257, 9, 'role', NULL, NULL, NULL, '0', '1', '1'),
  (258, 9, 'active', NULL, NULL, NULL, '0', '1', '1');
/*!40000 ALTER TABLE `admin_module_column` ENABLE KEYS */;


-- Dumping structure for table faktury.client
DROP TABLE IF EXISTS `client`;
CREATE TABLE IF NOT EXISTS `client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `tel` varchar(255) DEFAULT NULL,
  `dic` varchar(255) DEFAULT NULL,
  `ic_dph` varchar(255) DEFAULT NULL,
  `ico` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_client_user` (`user_id`),
  CONSTRAINT `FK_client_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- Dumping data for table faktury.client: ~3 rows (approximately)
DELETE FROM `client`;
/*!40000 ALTER TABLE `client` DISABLE KEYS */;
INSERT INTO `client` (`id`, `user_id`, `name`, `street`, `zip`, `city`, `tel`, `dic`, `ic_dph`, `ico`, `created`) VALUES
  (4, 23, 'TravelData, s. r. o', 'Agatova 5', '91101', 'Bratislava', '123456', '852468', 'SK852468', '123456', '2013-08-06 14:43:28'),
  (5, 23, 'Altamira', 'Jaskovy rad 187', '83101', 'Bratislava', '123456', '45132', 'fAS12345', '123123', '2013-08-19 14:18:51'),
  (6, 1, 'Altamira Softworks, s.r.o.', 'Jaskový rad 187', '831 01', 'Bratislava', '+421 903 773 023', '2022861368', 'SK2022861368', '44880774', '2013-10-08 09:28:19');
/*!40000 ALTER TABLE `client` ENABLE KEYS */;


-- Dumping structure for table faktury.invoice
DROP TABLE IF EXISTS `invoice`;
CREATE TABLE IF NOT EXISTS `invoice` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `invoice_number` varchar(255) NOT NULL,
  `variable_sign` varchar(255) NOT NULL,
  `maturity_date` date NOT NULL COMMENT 'Dátum splatnosti',
  `tax_duty_date` date NOT NULL COMMENT 'Daňová povinnosť',
  `delivery_date` date NOT NULL COMMENT 'Dátum dodania',
  `date_of_issue` date NOT NULL COMMENT 'Dátum vyhotovenia',
  PRIMARY KEY (`id`),
  KEY `FK_invoice_user` (`user_id`),
  KEY `FK__client` (`client_id`),
  CONSTRAINT `FK_invoice_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__client` FOREIGN KEY (`client_id`) REFERENCES `client` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- Dumping data for table faktury.invoice: ~3 rows (approximately)
DELETE FROM `invoice`;
/*!40000 ALTER TABLE `invoice` DISABLE KEYS */;
INSERT INTO `invoice` (`id`, `client_id`, `user_id`, `invoice_number`, `variable_sign`, `maturity_date`, `tax_duty_date`, `delivery_date`, `date_of_issue`) VALUES
  (10, 4, 23, '001/2013', '31021000806', '2013-08-13', '2013-08-06', '2013-08-06', '2013-08-06'),
  (11, 6, 1, '009/2013', '31021001008', '2013-10-15', '2013-10-08', '2013-10-08', '2013-10-08'),
  (12, 6, 1, '010/2013', '0102013', '2013-11-15', '2013-11-07', '2013-11-07', '2013-11-07');
/*!40000 ALTER TABLE `invoice` ENABLE KEYS */;


-- Dumping structure for table faktury.invoice_items
DROP TABLE IF EXISTS `invoice_items`;
CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `unit` enum('hour','piece') NOT NULL,
  `unit_count` float(10,3) NOT NULL,
  `unit_price` float(10,3) NOT NULL,
  `vat` float(10,2) DEFAULT NULL,
  `discount_percentage` float(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK__invoice` (`invoice_id`),
  CONSTRAINT `FK__invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoice` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Dumping data for table faktury.invoice_items: ~3 rows (approximately)
DELETE FROM `invoice_items`;
/*!40000 ALTER TABLE `invoice_items` DISABLE KEYS */;
INSERT INTO `invoice_items` (`id`, `invoice_id`, `text`, `unit`, `unit_count`, `unit_price`, `vat`, `discount_percentage`) VALUES
  (2, 10, 'vyrobu webstranky TMR', 'piece', 1.000, 1500.000, 20.00, 0.00),
  (3, 11, 'Služby poskytnuté v mesiaci September 2013', 'hour', 175.550, 10.000, 0.00, 0.00),
  (4, 12, 'Služby poskytnuté v mesiaci Október 2013', 'hour', 166.580, 10.000, 0.00, 0.00);
/*!40000 ALTER TABLE `invoice_items` ENABLE KEYS */;


-- Dumping structure for table faktury.project
DROP TABLE IF EXISTS `project`;
CREATE TABLE IF NOT EXISTS `project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `FK_project_user` (`user_id`),
  CONSTRAINT `FK_project_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- Dumping data for table faktury.project: ~19 rows (approximately)
DELETE FROM `project`;
/*!40000 ALTER TABLE `project` DISABLE KEYS */;
INSERT INTO `project` (`id`, `name`, `description`, `user_id`, `created`, `active`) VALUES
  (1, 'TMR', '', 1, '2013-10-31 11:33:49', 1),
  (2, 'ETI', '', 24, '2013-11-04 11:14:03', 1),
  (3, 'TUI', '', 24, '2013-11-04 11:14:12', 1),
  (4, 'gopass', '', 25, '2013-11-05 14:55:02', 1),
  (5, 'cook', '', 25, '2013-11-05 17:56:49', 1),
  (6, 'TMR', 'TMR', 26, '2013-11-06 09:51:14', 1),
  (7, 'TUI', '', 25, '2013-11-11 23:53:33', 1),
  (8, 'tui', '', 27, '2013-11-12 15:00:24', 1),
  (9, 'Traveldata', '', 25, '2013-11-12 23:38:14', 1),
  (10, 'ETI.at', '', 1, '2013-11-13 15:54:08', 1),
  (11, 'ETI', '', 25, '2013-11-18 14:30:19', 1),
  (12, 'TUI', '', 1, '2013-11-21 14:54:11', 1),
  (13, 'TMR', '', 24, '2013-11-25 16:10:52', 1),
  (14, 'TMR', '', 25, '2013-11-26 19:07:37', 1),
  (15, 'TUI.hu', '', 1, '2013-12-03 18:47:09', 1),
  (16, 'CK-Ecomm', '', 1, '2013-12-04 15:51:49', 1),
  (17, 'Kaskady', '', 1, '2013-12-05 17:03:48', 1),
  (18, 'ETI.sk', '', 1, '2013-12-05 17:05:09', 1),
  (19, 'php storm', '', 24, '2013-12-10 09:42:11', 1);
/*!40000 ALTER TABLE `project` ENABLE KEYS */;


-- Dumping structure for table faktury.task
DROP TABLE IF EXISTS `task`;
CREATE TABLE IF NOT EXISTS `task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `status_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_task_user` (`user_id`),
  KEY `FK_task_project` (`project_id`),
  KEY `FK_task_task_status` (`status_id`),
  CONSTRAINT `FK_task_task_status` FOREIGN KEY (`status_id`) REFERENCES `task_status` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_task_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_task_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Dumping data for table faktury.task: ~4 rows (approximately)
DELETE FROM `task`;
/*!40000 ALTER TABLE `task` DISABLE KEYS */;
INSERT INTO `task` (`id`, `user_id`, `project_id`, `name`, `description`, `status_id`, `created`) VALUES
  (2, 1, 1, 'Gopass', '<b>Gopass<br><br></b><u>-&nbsp;pri rezervacii ineho hotela ako GH Jasna mi to na bookingu presmeruje na url </u><a target="_blank" rel="nofollow" href="http://booking.gopass.sk/chyba/"><u>http://booking.gopass.sk/chyba/</u></a><br><b></b><u>-&nbsp;rovnako aj metoda booking vracia stale result 0‏</u><br>', 3, '2013-11-13 15:43:51'),
  (3, 1, 10, 'ETI SK spustenie', '- Upravit footer, dat prec HanseMerkur<br>- poistenie pri rezervacii treba vypnut<br>- treba vypnut tiez platbu kartou pri rezervacii', 3, '2013-11-13 15:55:29'),
  (4, 1, 1, 'Magnus platby', 'Mato, prosim Ta, spoj sa dnes s p. Bizikom, to je clovek, co ma na \nstarosti Magnus platby.\n<br>Skus sa ho popytat, ci si to otestovali a ci sa to moze spustit - ak \nbudu mat nejake pripomienky, tak to prosim zapracuj (je to velmi \njednoduche, je tam jedna trieda magnusPayments a to je cele).\n<br>\n<br>Jeho mail: <a target="_blank" rel="nofollow">matej.bizik@axasoft.eu</a>', 3, '2013-11-14 12:51:02'),
  (5, 1, 12, 'Requesty z tui a tui.hu', 'webservis:&nbsp;hotelinfo<br>cudne:<br>id touroperatora<br>giata id', 1, '2013-11-21 14:54:02');
/*!40000 ALTER TABLE `task` ENABLE KEYS */;


-- Dumping structure for table faktury.task_share
DROP TABLE IF EXISTS `task_share`;
CREATE TABLE IF NOT EXISTS `task_share` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `can_edit` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_task_share_user` (`user_id`),
  CONSTRAINT `FK_task_share_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table faktury.task_share: ~0 rows (approximately)
DELETE FROM `task_share`;
/*!40000 ALTER TABLE `task_share` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_share` ENABLE KEYS */;


-- Dumping structure for table faktury.task_share_project
DROP TABLE IF EXISTS `task_share_project`;
CREATE TABLE IF NOT EXISTS `task_share_project` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `task_share_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK__task_share` (`task_share_id`),
  KEY `FK__project` (`project_id`),
  CONSTRAINT `FK__task_share` FOREIGN KEY (`task_share_id`) REFERENCES `task_share` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK__project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table faktury.task_share_project: ~0 rows (approximately)
DELETE FROM `task_share_project`;
/*!40000 ALTER TABLE `task_share_project` DISABLE KEYS */;
/*!40000 ALTER TABLE `task_share_project` ENABLE KEYS */;


-- Dumping structure for table faktury.task_status
DROP TABLE IF EXISTS `task_status`;
CREATE TABLE IF NOT EXISTS `task_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Dumping data for table faktury.task_status: ~5 rows (approximately)
DELETE FROM `task_status`;
/*!40000 ALTER TABLE `task_status` DISABLE KEYS */;
INSERT INTO `task_status` (`id`, `name`, `active`) VALUES
  (1, 'waiting', 1),
  (2, 'in progress', 1),
  (3, 'complete', 1),
  (4, 'in review', 1),
  (5, 'suspended', 1);
/*!40000 ALTER TABLE `task_status` ENABLE KEYS */;


-- Dumping structure for table faktury.theme
DROP TABLE IF EXISTS `theme`;
CREATE TABLE IF NOT EXISTS `theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `theme_folder` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `active` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- Dumping data for table faktury.theme: ~2 rows (approximately)
DELETE FROM `theme`;
/*!40000 ALTER TABLE `theme` DISABLE KEYS */;
INSERT INTO `theme` (`id`, `name`, `theme_folder`, `host`, `active`) VALUES
  (1, 'LocalHost', 'original', 'localhost', 1),
  (2, 'faktury.sajgal.com', 'original', 'faktury.sajgal.com', 1);
/*!40000 ALTER TABLE `theme` ENABLE KEYS */;


-- Dumping structure for table faktury.timesheet
DROP TABLE IF EXISTS `timesheet`;
CREATE TABLE IF NOT EXISTS `timesheet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `description` text,
  `from` datetime DEFAULT NULL,
  `to` datetime DEFAULT NULL,
  `created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `last_update` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_timesheet_user` (`user_id`),
  KEY `FK_timesheet_project` (`project_id`),
  CONSTRAINT `FK_timesheet_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_timesheet_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8;

-- Dumping data for table faktury.timesheet: ~110 rows (approximately)
DELETE FROM `timesheet`;
/*!40000 ALTER TABLE `timesheet` DISABLE KEYS */;
INSERT INTO `timesheet` (`id`, `user_id`, `project_id`, `description`, `from`, `to`, `created`, `last_update`) VALUES
  (2, 24, 3, 'newsletter', '2013-11-04 09:30:00', '2013-11-04 10:30:00', '2013-11-04 11:15:24', '2013-11-04 17:31:09'),
  (3, 24, 2, 'preklady', '2013-11-04 10:30:00', '2013-11-04 17:30:00', '2013-11-04 16:19:26', '2013-11-04 17:30:58'),
  (4, 24, 3, 'newsletter', '2013-11-05 10:00:00', '2013-11-05 12:00:00', '2013-11-05 11:51:50', '2013-11-05 11:51:50'),
  (5, 24, 2, 'preklady', '2013-11-05 12:30:00', '2013-11-05 14:30:00', '2013-11-05 14:40:36', '2013-11-05 14:40:36'),
  (7, 25, 4, 'Programovanie dizajnu', '2013-11-05 14:55:00', '2013-11-05 17:58:00', '2013-11-05 17:56:08', '2013-11-05 17:56:08'),
  (8, 25, 5, 'tvorba dizajnu pre thomas cook', '2013-11-05 10:30:00', '2013-11-05 14:55:00', '2013-11-05 17:57:52', '2013-11-05 17:57:52'),
  (9, 26, 6, 'zmena buttonik', '2013-11-06 09:51:00', '2013-11-06 10:51:00', '2013-11-06 09:52:04', '2013-11-06 09:52:04'),
  (10, 24, 2, 'preklady', '2013-11-06 09:30:00', '2013-11-06 12:30:00', '2013-11-06 13:07:35', '2013-11-06 13:07:35'),
  (11, 24, 3, 'katalogy', '2013-11-06 13:00:00', '2013-11-06 17:00:00', '2013-11-06 18:54:59', '2013-11-06 18:54:59'),
  (12, 25, 4, '', '2013-11-06 12:45:00', '2013-11-06 07:15:00', '2013-11-06 19:17:06', '2013-11-06 19:17:06'),
  (14, 25, 5, '', '2013-11-06 09:30:00', '2013-11-06 10:15:00', '2013-11-06 19:19:53', '2013-11-06 19:19:53'),
  (15, 1, 1, 'tmr multiple rooms', '2013-11-06 19:15:00', '2013-11-06 20:23:00', '2013-11-06 19:32:20', '2013-11-06 20:22:05'),
  (16, 24, 3, 'katalogy', '2013-11-07 12:30:00', '2013-11-07 13:30:00', '2013-11-07 13:01:27', '2013-11-07 13:27:38'),
  (17, 1, 1, 'gopass', '2013-11-07 17:15:00', '2013-11-07 19:21:00', '2013-11-07 17:14:49', '2013-11-07 19:12:11'),
  (18, 24, 3, 'katalogy', '2013-11-07 17:00:00', '2013-11-07 17:30:00', '2013-11-07 18:21:35', '2013-11-07 18:21:35'),
  (19, 25, 5, 'grafika thomas cook', '2013-11-08 09:50:00', '2013-11-08 12:10:00', '2013-11-08 22:29:08', '2013-11-08 22:29:08'),
  (20, 25, 5, 'Male drobnosti pre jara na tui a citanie mailov (vvecer so mto robil ale nechce sa mi to delit na 10 casti)', '2013-11-08 12:10:00', '2013-11-08 13:50:00', '2013-11-08 22:30:05', '2013-11-08 22:30:05'),
  (21, 25, 5, 'Znova cook a troska uz aj vernostny program pre tui :) ', '2013-11-08 13:50:00', '2013-11-08 21:30:00', '2013-11-08 22:30:44', '2013-11-08 22:30:44'),
  (22, 25, 5, '', '2013-11-11 13:52:00', '2013-11-11 14:45:00', '2013-11-11 23:53:15', '2013-11-11 23:53:15'),
  (23, 25, 7, 'tvorba navrhu pre vernostny program alternativy ... ', '2013-11-11 14:45:00', '2013-11-11 17:56:00', '2013-11-11 23:54:13', '2013-11-11 23:54:13'),
  (24, 24, 3, 'newsletter', '2013-11-12 09:30:00', '2013-11-12 12:30:00', '2013-11-12 14:26:50', '2013-11-12 14:26:50'),
  (25, 24, 2, 'preklady', '2013-11-12 13:00:00', '2013-11-12 17:30:00', '2013-11-12 14:27:15', '2013-11-12 17:26:54'),
  (26, 27, 8, 'tui vernostny program...', '2013-11-12 11:30:00', '2013-11-12 15:00:00', '2013-11-12 15:01:27', '2013-11-12 15:01:47'),
  (27, 25, 7, '', '2013-11-12 10:10:00', '2013-11-12 11:59:00', '2013-11-12 23:37:20', '2013-11-12 23:37:20'),
  (28, 25, 9, '', '2013-11-12 13:37:00', '2013-11-12 20:37:00', '2013-11-12 23:38:37', '2013-11-12 23:38:37'),
  (29, 24, 3, 'uprava newslettra', '2013-11-13 09:30:00', '2013-11-13 10:30:00', '2013-11-13 10:35:40', '2013-11-13 10:35:40'),
  (30, 24, 2, 'stranka', '2013-11-13 12:30:00', '2013-11-13 16:30:00', '2013-11-13 16:12:34', '2013-11-13 16:12:34'),
  (31, 24, 3, 'kataligy', '2013-11-14 17:00:00', '2013-11-14 18:00:00', '2013-11-14 18:05:15', '2013-11-14 18:05:15'),
  (32, 27, 8, 'tui-zakaznicky program', '2013-11-14 15:30:00', '2013-11-14 20:45:00', '2013-11-14 18:16:22', '2013-11-14 20:41:38'),
  (33, 25, 9, '', '2013-11-14 15:33:00', '2013-11-14 20:52:00', '2013-11-14 19:58:03', '2013-11-14 19:58:03'),
  (34, 25, 9, '', '2013-11-14 10:58:00', '2013-11-14 13:20:00', '2013-11-14 19:58:51', '2013-11-14 19:58:51'),
  (35, 27, 8, 'zakaznicky sytem', '2013-11-15 09:00:00', '2013-11-15 14:00:00', '2013-11-15 09:16:35', '2013-11-15 14:05:50'),
  (36, 24, 3, 'katalogy', '2013-11-15 13:00:00', '2013-11-15 17:30:00', '2013-11-15 13:10:24', '2013-11-15 17:30:46'),
  (39, 24, 3, 'newsletter', '2013-11-18 09:30:00', '2013-11-18 10:30:00', '2013-11-18 14:29:38', '2013-11-18 14:29:38'),
  (40, 24, 2, 'web, preklady', '2013-11-18 10:30:00', '2013-11-18 14:30:00', '2013-11-18 14:30:00', '2013-11-18 14:30:00'),
  (41, 25, 11, 'Obrazky, kontrola prekladov, ...', '2013-11-18 11:30:00', '2013-11-18 17:30:00', '2013-11-18 14:31:42', '2013-11-18 17:35:52'),
  (42, 27, 8, 'Dorabanie Zakaznickeho prostredia', '2013-11-18 14:20:00', '2013-11-18 19:45:00', '2013-11-18 19:48:14', '2013-11-18 19:48:14'),
  (44, 25, 9, 'newsletter\n', '2013-11-18 17:30:00', '2013-11-18 22:23:00', '2013-11-18 22:24:36', '2013-11-18 22:24:36'),
  (45, 25, 9, 'Newsletter, ucenie sa nette :) ', '2013-11-19 10:50:00', '2013-11-19 19:24:00', '2013-11-19 10:57:07', '2013-11-19 19:24:59'),
  (46, 27, 8, 'Dorobene cele prihlasovanie uz len user prostredie vyrobit...', '2013-11-19 12:00:00', '2013-11-19 16:00:00', '2013-11-19 16:06:57', '2013-11-19 16:06:56'),
  (47, 24, 3, 'newsletter', '2013-11-19 16:30:00', '2013-11-19 17:30:00', '2013-11-19 17:38:27', '2013-11-19 17:38:27'),
  (48, 24, 3, 'uprava newslettra', '2013-11-20 10:30:00', '2013-11-20 12:00:00', '2013-11-20 11:59:12', '2013-11-20 11:59:12'),
  (49, 24, 2, 'novy ETI newsletter', '2013-11-20 12:30:00', '2013-11-20 18:00:00', '2013-11-20 16:12:13', '2013-11-20 17:47:59'),
  (50, 27, 8, 'Vernostny program - uz zaciatok uzivatelskeho menu.', '2013-11-20 13:40:00', '2013-11-20 19:00:00', '2013-11-20 21:41:34', '2013-11-20 21:41:34'),
  (51, 25, 9, 'Serverove veci, som riesil toho chalana; ', '2013-11-20 09:54:00', '2013-11-20 10:45:00', '2013-11-20 22:08:51', '2013-11-20 22:08:51'),
  (52, 25, 9, 'Hral som sa s tym newslettrom ', '2013-11-20 10:45:00', '2013-11-20 13:08:00', '2013-11-20 22:10:26', '2013-11-20 22:10:26'),
  (53, 25, 9, 'Rozoberali sme ten server a planovali nakup :)', '2013-11-20 13:08:00', '2013-11-20 14:00:00', '2013-11-20 22:11:13', '2013-11-20 22:11:13'),
  (54, 25, 9, 'Potreboval som si rozbehat travelweby lebo som to nemal na notebooku, doinstalovat rozne blbosti a popri som nieco pomohol tomasovi :)', '2013-11-20 14:00:00', '2013-11-20 15:11:00', '2013-11-20 22:12:01', '2013-11-20 22:12:01'),
  (55, 25, 7, 'Riesenie kategorii na TUI', '2013-11-20 15:11:00', '2013-11-20 17:32:00', '2013-11-20 22:13:30', '2013-11-20 22:20:06'),
  (56, 25, 11, 'Pomohol som nike s par veciami :) spravil som  ten banner do newslettra a nakodil som footer :) ', '2013-11-20 17:32:00', '2013-11-20 19:13:00', '2013-11-20 22:14:43', '2013-11-20 22:19:51'),
  (57, 25, 11, 'Spravil som to otvorenie do popupou v slidery :) ', '2013-11-20 19:13:00', '2013-11-20 20:13:00', '2013-11-20 22:15:49', '2013-11-20 22:19:28'),
  (58, 25, 7, 'Vernostny program, navrhy, ... :)', '2013-11-20 20:13:00', '2013-11-20 22:15:00', '2013-11-20 22:18:07', '2013-11-20 22:18:56'),
  (60, 24, 3, 'Newsletter', '2013-11-22 10:00:00', '2013-11-22 17:16:00', '2013-11-22 17:15:46', '2013-11-22 17:15:46'),
  (61, 27, 8, 'Dojebane phpstorm-fixed\nTui-Vernostny program dokoncovanie fixovane template + errory AND jemny testing,overovanie passwordov,etc...\n', '2013-11-25 10:30:00', '2013-11-25 19:00:00', '2013-11-25 11:31:02', '2013-11-25 19:02:39'),
  (62, 24, 2, 'newsletter', '2013-11-25 10:00:00', '2013-11-25 12:00:00', '2013-11-25 16:11:14', '2013-11-25 16:11:14'),
  (63, 24, 13, 'testovanie', '2013-11-25 12:30:00', '2013-11-25 16:00:00', '2013-11-25 16:12:01', '2013-11-25 16:12:01'),
  (64, 25, 4, 'Riesenie bugov s projektom *pokazene svn :) ', '2013-11-25 09:51:00', '2013-11-25 10:30:00', '2013-11-25 20:24:51', '2013-11-25 20:24:51'),
  (65, 25, 11, 'Newsletter, grafika, upravy :)', '2013-11-25 10:30:00', '2013-11-25 12:06:00', '2013-11-25 20:25:37', '2013-11-25 20:25:37'),
  (66, 25, 4, ':) templatovanie, ... ', '2013-11-25 12:06:00', '2013-11-25 16:00:00', '2013-11-25 20:26:33', '2013-11-25 20:26:33'),
  (67, 25, 7, 'Tomasovi som rezal img v ps a pomahal mu ', '2013-11-25 16:00:00', '2013-11-25 17:00:00', '2013-11-25 20:27:12', '2013-11-25 20:27:12'),
  (68, 25, 4, 'Doladovacky na gopass :)', '2013-11-25 17:00:00', '2013-11-25 18:00:00', '2013-11-25 20:27:52', '2013-11-25 20:27:52'),
  (69, 25, 11, 'Ta flashova 360stupnova parada medzi obrazkami, slider :)', '2013-11-25 18:00:00', '2013-11-25 20:30:00', '2013-11-25 20:28:57', '2013-11-25 20:28:57'),
  (70, 25, 4, 'RIesnie problemov s instalaciou s gopassom ( riesene cez vikend !!!) ', '2013-11-25 08:29:00', '2013-11-25 09:29:00', '2013-11-25 20:30:31', '2013-11-25 20:30:31'),
  (71, 27, 8, 'Tui dorabanie detailov...', '2013-11-26 13:00:00', '2013-11-26 15:00:00', '2013-11-26 14:56:37', '2013-11-26 14:56:37'),
  (72, 24, 13, 'testovanie', '2013-11-26 13:30:00', '2013-11-26 17:00:00', '2013-11-26 16:01:47', '2013-11-26 17:32:17'),
  (73, 25, 4, 'Upravy na gopass', '2013-11-26 10:00:00', '2013-11-26 12:05:00', '2013-11-26 19:05:50', '2013-11-26 19:05:50'),
  (74, 25, 11, 'Advent. kalendar', '2013-11-26 12:05:00', '2013-11-26 16:00:00', '2013-11-26 19:06:45', '2013-11-26 19:06:51'),
  (75, 25, 14, 'Trustpay, grafika quickbookera', '2013-11-26 16:00:00', '2013-11-26 19:08:00', '2013-11-26 19:08:22', '2013-11-26 19:08:22'),
  (76, 25, 4, 'fix bugs, ', '2013-11-26 21:00:00', '2013-11-26 21:12:00', '2013-11-26 21:55:42', '2013-11-26 21:55:42'),
  (77, 25, 14, 'Riesenie chyby s tetou a nejake veci pre jara :)', '2013-11-26 21:12:00', '2013-11-26 21:30:00', '2013-11-26 21:56:14', '2013-11-26 21:56:14'),
  (79, 24, 13, 'testovanie', '2013-11-27 10:00:00', '2013-11-27 15:00:00', '2013-11-27 10:32:35', '2013-11-27 10:32:35'),
  (80, 27, 8, 'Vernostny program', '2013-11-27 12:00:00', '2013-11-27 17:00:00', '2013-11-27 17:36:31', '2013-11-27 17:36:31'),
  (81, 25, 9, 'Servery :) posielanie info, diskusia ako to ma fungovat, ... ', '2013-11-27 09:09:00', '2013-11-27 10:03:00', '2013-11-27 18:20:32', '2013-11-27 18:20:32'),
  (82, 25, 14, 'Trustpay', '2013-11-27 10:03:00', '2013-11-27 11:20:00', '2013-11-27 18:21:22', '2013-11-27 18:21:22'),
  (83, 25, 11, 'Advent kal. ', '2013-11-27 11:20:00', '2013-11-27 15:30:00', '2013-11-27 18:22:03', '2013-11-27 18:22:03'),
  (84, 25, 4, 'Oprava chyb :)', '2013-11-27 15:30:00', '2013-11-27 17:21:00', '2013-11-27 18:23:00', '2013-11-27 18:23:00'),
  (85, 25, 14, 'datapicker a ine detaily', '2013-11-27 17:21:00', '2013-11-27 18:22:00', '2013-11-27 18:23:50', '2013-11-27 18:23:50'),
  (86, 25, 11, 'Dorabacky na advente', '2013-11-29 17:24:00', '2013-11-29 18:54:00', '2013-11-29 23:16:41', '2013-11-29 23:16:41'),
  (87, 25, 14, 'Grafika na TMR :)', '2013-11-29 18:54:00', '2013-11-29 23:57:00', '2013-11-29 23:17:13', '2013-11-29 23:17:13'),
  (88, 25, 14, 'Nova grafika a programovanie cross sell hotel screenu', '2013-11-30 08:17:00', '2013-11-30 21:00:00', '2013-11-30 21:00:44', '2013-11-30 21:00:44'),
  (89, 1, 1, 'Gopass parametre', '2013-12-02 10:24:00', '2013-12-02 14:00:00', '2013-12-02 10:23:57', '2013-12-02 15:01:45'),
  (90, 24, 3, 'newsletter', '2013-12-02 10:30:00', '2013-12-02 15:00:00', '2013-12-02 10:36:22', '2013-12-02 15:01:12'),
  (91, 1, 10, 'Eti advent + pripomienky od Nikol', '2013-12-02 14:00:00', '2013-12-02 15:03:00', '2013-12-02 15:02:20', '2013-12-02 15:02:20'),
  (92, 25, 4, 'Dnesne chytanie bugov pre gopass :) ', '2013-12-02 08:30:00', '2013-12-02 11:30:00', '2013-12-02 19:35:30', '2013-12-02 19:35:30'),
  (93, 25, 14, 'Programovanie Crossell, skusanie novych feature a tvorba grafiky :) ', '2013-12-02 11:30:00', '2013-12-02 19:36:00', '2013-12-02 19:36:21', '2013-12-02 19:36:21'),
  (94, 25, 11, 'FIX na newsletter, prehodenie na sk verziu, graficke prvky dalsie :) ( AJ Z NEDELE) ', '2013-12-02 19:36:00', '2013-12-02 21:40:00', '2013-12-02 19:37:22', '2013-12-02 19:37:22'),
  (96, 1, 1, 'gopass, cross-sell', '2013-12-03 09:43:00', '2013-12-03 14:47:00', '2013-12-03 09:42:40', '2013-12-03 18:46:31'),
  (97, 24, 2, 'adventny kalendar', '2013-12-03 14:00:00', '2013-12-03 16:00:00', '2013-12-03 15:03:52', '2013-12-03 18:14:09'),
  (98, 24, 13, 'testovanie', '2013-12-03 16:00:00', '2013-12-03 19:00:00', '2013-12-03 18:14:27', '2013-12-03 18:59:09'),
  (99, 1, 15, 'Pripomienky k linkovym-letom, hotel-info (od joza)', '2013-12-03 14:47:00', '2013-12-03 15:30:00', '2013-12-03 18:47:58', '2013-12-03 18:47:58'),
  (100, 1, 1, 'pripomienky ku gopass, spustenie', '2013-12-03 15:30:00', '2013-12-03 18:49:00', '2013-12-03 18:49:14', '2013-12-03 18:49:14'),
  (101, 25, 4, 'Oprava prekladov gopass', '2013-12-03 10:00:00', '2013-12-03 10:30:00', '2013-12-03 20:42:06', '2013-12-03 20:42:06'),
  (102, 25, 5, 'Stretnutie so zakaznikom', '2013-12-03 10:30:00', '2013-12-03 13:30:00', '2013-12-03 20:42:35', '2013-12-03 20:42:50'),
  (103, 25, 14, 'Riesenie bugov v tmr, nova grafika a ine kraviny ', '2013-12-03 13:30:00', '2013-12-03 18:00:00', '2013-12-03 20:43:56', '2013-12-03 20:43:56'),
  (104, 25, 7, '360view ', '2013-12-03 18:00:00', '2013-12-03 18:30:00', '2013-12-03 20:44:50', '2013-12-03 20:44:50'),
  (105, 25, 9, 'Rozne doladovacky, orpava malych chyb, riesenie kaskad, ... ', '2013-12-03 18:44:00', '2013-12-03 21:00:00', '2013-12-03 20:45:38', '2013-12-03 20:45:38'),
  (106, 1, 1, 'Pripomienky od Papsona - zase 2 pripomienky ktore som len vysvetlil, nase chyby ziadne\nGoPASS cyklenie\nGoPass rest, pripomienky', '2013-12-04 07:54:00', '2013-12-04 12:52:00', '2013-12-04 07:53:01', '2013-12-04 15:51:18'),
  (107, 24, 13, 'testovanie, nahadzovanie, upravy', '2013-12-04 13:00:00', '2013-12-04 16:00:00', '2013-12-04 15:17:20', '2013-12-04 15:53:30'),
  (108, 1, 16, 'iframe katalogy, zmena otvaracich hodin', '2013-12-04 12:52:00', '2013-12-04 13:15:00', '2013-12-04 15:52:26', '2013-12-04 15:52:26'),
  (109, 1, 10, 'Zmena otvaracich hodin, parkplatz 10 dni pred odletom, obrazok maroko,', '2013-12-04 13:15:00', '2013-12-04 14:15:00', '2013-12-04 15:54:04', '2013-12-04 15:54:04'),
  (110, 1, 1, 'gopass marketingove kategorie, nasadzovanie', '2013-12-04 14:15:00', '2013-12-04 16:00:00', '2013-12-04 15:54:36', '2013-12-04 15:54:57'),
  (111, 1, 1, '- marketingove kategorie a kategorie hosta\n- oprava nazvov izieb\n- pripomienky do Blaha (konecne)', '2013-12-05 08:01:00', '2013-12-05 14:05:00', '2013-12-05 08:00:52', '2013-12-05 17:08:55'),
  (112, 1, 10, 'Gutscheiny uprava sablony + nahladu css\nPoistenie Turecko', '2013-12-05 14:05:00', '2013-12-05 15:06:00', '2013-12-05 17:06:07', '2013-12-05 17:09:05'),
  (113, 1, 17, 'Nove CMS, wireframy, ostatne drobnosti', '2013-12-05 15:06:00', '2013-12-05 17:07:00', '2013-12-05 17:08:25', '2013-12-05 17:09:11'),
  (114, 1, 1, 'trustpay, papson vysvetlovanie, gopass encoding, chybajuce informacie', '2013-12-06 08:57:00', '2013-12-06 17:11:00', '2013-12-06 09:00:08', '2013-12-06 17:10:17'),
  (115, 24, 3, 'newsletter+prednaska', '2013-12-06 13:00:00', '2013-12-06 16:00:00', '2013-12-06 16:12:32', '2013-12-06 16:12:32'),
  (117, 1, 1, 'Papsonovi nejde GHJ a GHP', '2013-12-10 08:15:00', '2013-12-10 08:48:00', '2013-12-10 08:23:02', '2013-12-10 08:48:49'),
  (118, 1, 1, 'Gopass pripomienky od Blaha, xml export, trustpay', '2013-12-10 09:42:00', '2013-12-10 17:21:00', '2013-12-10 09:42:08', '2013-12-10 21:05:59'),
  (119, 24, 19, 'phpstorm', '2013-12-10 09:00:00', '2013-12-10 12:00:00', '2013-12-10 11:55:20', '2013-12-10 11:55:20'),
  (120, 1, 18, 'import agentur', '2013-12-10 17:21:00', '2013-12-10 17:48:00', '2013-12-10 21:04:48', '2013-12-10 21:05:26');
/*!40000 ALTER TABLE `timesheet` ENABLE KEYS */;


-- Dumping structure for table faktury.timesheet_data
DROP TABLE IF EXISTS `timesheet_data`;
CREATE TABLE IF NOT EXISTS `timesheet_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `day` date NOT NULL,
  `lunch_in_minutes` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 3` (`user_id`,`day`),
  CONSTRAINT `FK_timesheet_data_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- Dumping data for table faktury.timesheet_data: ~23 rows (approximately)
DELETE FROM `timesheet_data`;
/*!40000 ALTER TABLE `timesheet_data` DISABLE KEYS */;
INSERT INTO `timesheet_data` (`id`, `user_id`, `day`, `lunch_in_minutes`) VALUES
  (1, 24, '2013-11-18', 30),
  (2, 25, '2013-11-18', 30),
  (3, 27, '2013-11-18', 30),
  (4, 27, '2013-11-19', 30),
  (5, 25, '2013-11-19', 30),
  (6, 27, '2013-11-20', 40),
  (7, 25, '2013-11-20', 30),
  (8, 24, '2013-11-22', 60),
  (9, 27, '2013-11-25', 0),
  (10, 25, '2013-11-25', 40),
  (11, 25, '2013-11-26', 30),
  (12, 24, '2013-11-27', 30),
  (13, 25, '2013-11-27', 30),
  (14, 25, '2013-11-30', 40),
  (15, 24, '2013-12-02', 30),
  (16, 1, '2013-12-02', 40),
  (17, 25, '2013-12-02', 30),
  (18, 1, '2013-12-03', 30),
  (19, 25, '2013-12-03', 50),
  (20, 1, '2013-12-04', 30),
  (21, 1, '2013-12-05', 20),
  (22, 1, '2013-12-06', 30),
  (23, 1, '2013-12-10', 30);
/*!40000 ALTER TABLE `timesheet_data` ENABLE KEYS */;


-- Dumping structure for table faktury.timesheet_share
DROP TABLE IF EXISTS `timesheet_share`;
CREATE TABLE IF NOT EXISTS `timesheet_share` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `timesheet_owner_id` int(11) NOT NULL,
  `active` bigint(20) DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Index 2` (`user_id`,`timesheet_owner_id`),
  CONSTRAINT `FK_timesheet_share_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Dumping data for table faktury.timesheet_share: ~0 rows (approximately)
DELETE FROM `timesheet_share`;
/*!40000 ALTER TABLE `timesheet_share` DISABLE KEYS */;
INSERT INTO `timesheet_share` (`id`, `user_id`, `timesheet_owner_id`, `active`) VALUES
  (3, 1, 26, 1);
/*!40000 ALTER TABLE `timesheet_share` ENABLE KEYS */;


-- Dumping structure for table faktury.user
DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nickname` varchar(255) NOT NULL,
  `nickname_webalized` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` char(60) NOT NULL,
  `create_date` datetime NOT NULL,
  `hash` varchar(20) NOT NULL,
  `role` varchar(255) NOT NULL,
  `fa_supplier_name` varchar(255) DEFAULT NULL,
  `fa_supplier_address` varchar(255) DEFAULT NULL,
  `fa_supplier_zip` varchar(255) DEFAULT NULL,
  `fa_supplier_city` varchar(255) DEFAULT NULL,
  `fa_supplier_tel` varchar(255) DEFAULT NULL,
  `fa_bank_account_no` varchar(255) DEFAULT NULL,
  `fa_suplier_ico` varchar(255) DEFAULT NULL,
  `fa_dic` varchar(255) DEFAULT NULL,
  `fa_ic_dph` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniquemail` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;

-- Dumping data for table faktury.user: ~6 rows (approximately)
DELETE FROM `user`;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `nickname`, `nickname_webalized`, `email`, `password`, `create_date`, `hash`, `role`, `fa_supplier_name`, `fa_supplier_address`, `fa_supplier_zip`, `fa_supplier_city`, `fa_supplier_tel`, `fa_bank_account_no`, `fa_suplier_ico`, `fa_dic`, `fa_ic_dph`, `active`) VALUES
  (1, 'roarbb', 'roarbb', 'roarbb@gmail.com', '$2a$07$$$$$$$$$$$$$$$$$$$$$$.mnEkB8o4j5Gx.R1.SZ8.4TXhYdmA7uK', '2013-07-05 06:55:01', '672z7ohwv7usddgbe6d4', 'admin', 'Matej Šajgal', 'Sásovská cesta 42', '97411', 'Banská Bystrica', '+421 902 139 313', '520700-4202613970/8360', '45709858', '1083117629', NULL, 1),
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
