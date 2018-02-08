-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Table `search_group`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `search_group` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(65) NOT NULL,
  `coordinator` VARCHAR(65) NOT NULL,
  `year` INT NOT NULL,
  `numGrad` INT NOT NULL,
  `numPosGrad` INT NOT NULL,
  `goals` MEDIUMTEXT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `professor
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `professor` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `search_group_id` INT NOT NULL,
  `name` VARCHAR(45) NULL,
  `ccsa` TINYINT(1) NOT NULL,
  `master_phd` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id`, `search_group_id`),
  INDEX `fk_professor_search_group_idx` (`search_group_id` ASC),
  CONSTRAINT `fk_professor_search_group`
    FOREIGN KEY (`search_group_id`)
    REFERENCES `search_group` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `insertion`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `insertion` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `criterion` INT NULL,
  `type` VARCHAR(45) NULL,
  `content` MEDIUMTEXT NULL,
  `professor_id` INT NOT NULL,
  `professor_search_group_id` INT NOT NULL,
  PRIMARY KEY (`id`, `professor_id`, `professor_search_group_id`),
  INDEX `fk_insertion_professor1_idx` (`professor_id` ASC, `professor_search_group_id` ASC),
  CONSTRAINT `fk_insertion_professor1`
    FOREIGN KEY (`professor_id` , `professor_search_group_id`)
    REFERENCES `professor` (`id` , `search_group_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
