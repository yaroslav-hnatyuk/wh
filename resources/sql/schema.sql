-- MySQL Script generated by MySQL Workbench
-- Sat May 26 17:53:49 2018
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema wh
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `wh` ;

-- -----------------------------------------------------
-- Schema wh
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `wh` DEFAULT CHARACTER SET utf8 ;
USE `wh` ;

-- -----------------------------------------------------
-- Table `wh`.`table1`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wh`.`table1` ;

CREATE TABLE IF NOT EXISTS `wh`.`table1` (
)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `wh`.`company`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wh`.`company` ;

CREATE TABLE IF NOT EXISTS `wh`.`company` (
  `id` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `wh`.`office`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wh`.`office` ;

CREATE TABLE IF NOT EXISTS `wh`.`office` (
  `id` INT NOT NULL,
  `address` TEXT(1000) NULL,
  `company_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_office_company1_idx` (`company_id` ASC),
  CONSTRAINT `fk_office_company1`
    FOREIGN KEY (`company_id`)
    REFERENCES `wh`.`company` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `wh`.`user`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wh`.`user` ;

CREATE TABLE IF NOT EXISTS `wh`.`user` (
  `id` INT NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `role` VARCHAR(45) NOT NULL,
  `first_name` VARCHAR(45) NULL,
  `last_name` VARCHAR(45) NULL,
  `phone` VARCHAR(45) NULL,
  `office_id` INT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_user_office1_idx` (`office_id` ASC),
  CONSTRAINT `fk_user_office1`
    FOREIGN KEY (`office_id`)
    REFERENCES `wh`.`office` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `wh`.`dish_group`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wh`.`dish_group` ;

CREATE TABLE IF NOT EXISTS `wh`.`dish_group` (
  `id` INT NOT NULL,
  `name` VARCHAR(255) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `wh`.`dish`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wh`.`dish` ;

CREATE TABLE IF NOT EXISTS `wh`.`dish` (
  `id` INT NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `price` FLOAT NULL,
  `description` TEXT(2000) NULL,
  `ingredients` TEXT(2000) NULL,
  `weight` FLOAT NULL,
  `dish_group_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_dish_dish_group1_idx` (`dish_group_id` ASC),
  CONSTRAINT `fk_dish_dish_group1`
    FOREIGN KEY (`dish_group_id`)
    REFERENCES `wh`.`dish_group` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `wh`.`menu_dish`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wh`.`menu_dish` ;

CREATE TABLE IF NOT EXISTS `wh`.`menu_dish` (
  `id` INT NOT NULL,
  `day` DATE NOT NULL,
  `dish_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_menu_dish1_idx` (`dish_id` ASC),
  CONSTRAINT `fk_menu_dish1`
    FOREIGN KEY (`dish_id`)
    REFERENCES `wh`.`dish` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `wh`.`order`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wh`.`order` ;

CREATE TABLE IF NOT EXISTS `wh`.`order` (
  `id` INT NOT NULL,
  `day` DATE NOT NULL,
  `count` INT NULL,
  `user_id` INT NOT NULL,
  `menu_dish_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_order_user1_idx` (`user_id` ASC),
  INDEX `fk_order_menu_dish1_idx` (`menu_dish_id` ASC),
  CONSTRAINT `fk_order_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `wh`.`user` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_menu_dish1`
    FOREIGN KEY (`menu_dish_id`)
    REFERENCES `wh`.`menu_dish` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `wh`.`table2`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `wh`.`table2` ;

CREATE TABLE IF NOT EXISTS `wh`.`table2` (
)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
