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
