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
