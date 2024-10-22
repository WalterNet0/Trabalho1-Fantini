-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema a2023952500@teiacoltec.org
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema a2023952500@teiacoltec.org
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `a2023952500@teiacoltec.org` DEFAULT CHARACTER SET utf8 ;
USE `a2023952500@teiacoltec.org` ;

-- -----------------------------------------------------
-- Table `a2023952500@teiacoltec.org`.`Usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `a2023952500@teiacoltec.org`.`Usuarios` (
  `idUsuario` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(45) NULL,
  `email` VARCHAR(45) NULL,
  `tipo` INT NULL,
  `ativo` TINYINT NULL,
  PRIMARY KEY (`idUsuario`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `a2023952500@teiacoltec.org`.`Senhas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `a2023952500@teiacoltec.org`.`Senhas` (
  `idUsuario` INT NOT NULL,
  `senha` VARCHAR(255) NULL,
  PRIMARY KEY (`idUsuario`),
  CONSTRAINT `idUsuario`
    FOREIGN KEY (`idUsuario`)
    REFERENCES `a2023952500@teiacoltec.org`.`Usuarios` (`idUsuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
