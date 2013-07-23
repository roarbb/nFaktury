-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               5.5.27 - MySQL Community Server (GPL)
-- Server OS:                    Win32
-- HeidiSQL Version:             8.0.0.4396
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table faktury.client: ~1 rows (approximately)
DELETE FROM `client`;
/*!40000 ALTER TABLE `client` DISABLE KEYS */;
INSERT INTO `client` (`id`, `user_id`, `name`, `street`, `zip`, `city`, `tel`, `dic`, `ic_dph`, `ico`, `created`) VALUES
(3, 1, 'Novy klient', 'Ulicová 2', '97411', 'Banská Bystrica', '+421 907 885 111', '12', 'sk12', '12', '2013-07-23 23:31:03');
/*!40000 ALTER TABLE `client` ENABLE KEYS */;


-- Dumping structure for table faktury.theme
DROP TABLE IF EXISTS `theme`;
CREATE TABLE IF NOT EXISTS `theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `theme_folder` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `active` tinyint(1) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Dumping data for table faktury.theme: ~1 rows (approximately)
DELETE FROM `theme`;
/*!40000 ALTER TABLE `theme` DISABLE KEYS */;
INSERT INTO `theme` (`id`, `name`, `theme_folder`, `host`, `active`) VALUES
(1, 'LocalHost', 'original', 'localhost', 1);
/*!40000 ALTER TABLE `theme` ENABLE KEYS */;


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
  `fa_dic` varchar(255) DEFAULT NULL,
  `fa_ic_dph` varchar(255) DEFAULT NULL,
  `active` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniquemail` (`email`),
  UNIQUE KEY `uniquelogin` (`nickname`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Dumping data for table faktury.user: ~2 rows (approximately)
DELETE FROM `user`;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` (`id`, `nickname`, `nickname_webalized`, `email`, `password`, `create_date`, `hash`, `role`, `fa_supplier_name`, `fa_supplier_address`, `fa_supplier_zip`, `fa_supplier_city`, `fa_supplier_tel`, `fa_bank_account_no`, `fa_dic`, `fa_ic_dph`, `active`) VALUES
(1, 'roarbb', 'roarbb', 'roarbb@gmail.com', '$2a$07$$$$$$$$$$$$$$$$$$$$$$.mnEkB8o4j5Gx.R1.SZ8.4TXhYdmA7uK', '2013-07-05 06:55:01', '672z7ohwv7usddgbe6d4', 'admin', 'Jonh Doe', 'Downing Street 42', '85974', 'City', '00421922258313', '520700-xxxxxxxxxx/8360', '1088744629', NULL, 1),
(3, 'roarbb2', 'roarbb', 'roarbb2@gmail.com', '$2a$07$$$$$$$$$$$$$$$$$$$$$$.mnEkB8o4j5Gx.R1.SZ8.4TXhYdmA7uK', '2013-07-05 06:55:01', '672z7ohwv7usddgbe6d4', 'admin', 'Jonh Doe', 'Downing Street 42', '85974', 'City', '00421922258313', '520700-xxxxxxxxxx/8360', '1088744629', NULL, 1);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
