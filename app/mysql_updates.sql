#----------------------- 12.10.2013 ----------------------------------
CREATE TABLE `project` (
  `id`          INT(11)      NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(255) NULL DEFAULT NULL,
  `description` VARCHAR(255) NULL DEFAULT NULL,
  `user_id`     INT(11)      NOT NULL,
  `active`      TINYINT(4)   NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  INDEX `FK_project_user` (`user_id`),
  CONSTRAINT `FK_project_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE =InnoDB;

ALTER TABLE `project`
ADD COLUMN `created` DATETIME NULL DEFAULT NULL AFTER `user_id`;

CREATE TABLE `task` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `project_id` INT(11) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  `status` ENUM('waiting','in progress','complete','in review','suspended') NULL DEFAULT 'waiting',
  `created` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE=InnoDB;

ALTER TABLE `task`
ADD CONSTRAINT `FK_task_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
ADD CONSTRAINT `FK_task_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;

CREATE TABLE `task_status` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `active` TINYINT(4) NULL DEFAULT '1',
  PRIMARY KEY (`id`)
)
  ENGINE=InnoDB;

ALTER TABLE `task`
CHANGE COLUMN `status` `status_id` INT(11) NOT NULL AFTER `description`,
ADD CONSTRAINT `FK_task_task_status` FOREIGN KEY (`status_id`) REFERENCES `task_status` (`id`) ON UPDATE CASCADE ON DELETE CASCADE;

INSERT INTO `faktury`.`task_status` (`name`) VALUES ('waiting');
INSERT INTO `faktury`.`task_status` (`name`) VALUES ('in progress');
INSERT INTO `faktury`.`task_status` (`name`) VALUES ('complete');
INSERT INTO `faktury`.`task_status` (`name`) VALUES ('in review');
INSERT INTO `faktury`.`task_status` (`name`) VALUES ('suspended');


#----------------------- 16.10.2013 ----------------------------------
CREATE TABLE `task_share` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `edit` TINYINT(4) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `FK_task_share_user` (`user_id`),
  CONSTRAINT `FK_task_share_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
)
  ENGINE=InnoDB;

CREATE TABLE `task_share_project` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `task_share_id` INT(11) NOT NULL,
  `project_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `FK__task_share` (`task_share_id`),
  INDEX `FK__project` (`project_id`),
  CONSTRAINT `FK__task_share` FOREIGN KEY (`task_share_id`) REFERENCES `task_share` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT `FK__project` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
)
  ENGINE=InnoDB;
