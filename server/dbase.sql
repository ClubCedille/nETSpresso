DROP USER IF EXISTS `cake`;
CREATE USER 'cake'@'localhost' IDENTIFIED BY 'cake';
GRANT ALL PRIVILEGES ON api_atiende_me.* TO 'cake'@'%';
FLUSH PRIVILEGES;

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DROP SCHEMA IF EXISTS `netspresso` ;
CREATE SCHEMA IF NOT EXISTS `netspresso` DEFAULT CHARACTER SET latin1 ;
USE `netspresso` ;

-- -----------------------------------------------------
-- Table `netspresso`.`boxes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `netspresso`.`boxes` ;

CREATE TABLE IF NOT EXISTS `netspresso`.`boxes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `created` DATETIME NULL DEFAULT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  `name` VARCHAR(45) NULL DEFAULT NULL,
  `mac` VARCHAR(40) NOT NULL DEFAULT '00:00:00:00:00:00',
  `ev_time` DATETIME NULL DEFAULT NULL,
  `ev_stdby` DATETIME NULL DEFAULT NULL,
  `ev_state` VARCHAR(20) NULL DEFAULT 'Stand-By',
  `hb_time` DATETIME NULL DEFAULT NULL,
  `hb_state` VARCHAR(20) NULL DEFAULT 'Stand-By',
  `hb_temp` VARCHAR(20) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id` (`id` ASC),
  UNIQUE INDEX `mac_UNIQUE` (`mac` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `netspresso`.`events`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `netspresso`.`events` ;

CREATE TABLE IF NOT EXISTS `netspresso`.`events` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `created` DATETIME NULL DEFAULT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  `uuid` VARCHAR(200) NOT NULL DEFAULT '',
  `event_id` INT(11) NOT NULL DEFAULT '0',
  `resource_event_id` INT(11) NOT NULL DEFAULT '0',
  `calendar_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL DEFAULT '0',
  `username` VARCHAR(100) NOT NULL DEFAULT 'Unknown',
  `start_time` DATETIME NOT NULL,
  `end_time` DATETIME NOT NULL,
  `subjet` VARCHAR(150) NOT NULL,
  `status` VARCHAR(20) NOT NULL DEFAULT 'NEEDS-ACTION',
  `ready_time` DATETIME NOT NULL,
  `stdby_time` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `uuid_UNIQUE` (`uuid` ASC),
  INDEX `uuid` (`uuid` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 21
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `netspresso`.`metrics`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `netspresso`.`metrics` ;

CREATE TABLE IF NOT EXISTS `netspresso`.`metrics` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `created` DATETIME NULL DEFAULT NULL,
  `modified` DATETIME NULL DEFAULT NULL,
  `sensor` VARCHAR(120) NULL DEFAULT NULL,
  `value` VARCHAR(45) NULL DEFAULT NULL,
  `units` VARCHAR(45) NULL DEFAULT NULL,
  `acquired` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `id` (`id` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 15277
DEFAULT CHARACTER SET = latin1;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
