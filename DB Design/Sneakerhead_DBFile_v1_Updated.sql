-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema sneakerhead
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `sneakerhead` ;

-- -----------------------------------------------------
-- Schema sneakerhead
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `sneakerhead` ;
SHOW WARNINGS;
USE `sneakerhead` ;

-- -----------------------------------------------------
-- Table `sneakerhead`.`ref_roles`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sneakerhead`.`ref_roles` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `sneakerhead`.`ref_roles` (
  `role_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`role_id`))
ENGINE = InnoDB;
SHOW WARNINGS;
USE sneakerhead;
INSERT INTO `sneakerhead`.`ref_roles` (`role_id`, `role_name`) VALUES
(1, 'Administrator'),
(2, 'User');
SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `sneakerhead`.`users`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sneakerhead`.`users` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `sneakerhead`.`users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `phone_no` VARCHAR(25) NOT NULL,
  `pw_hash` VARCHAR(255) NOT NULL,
  `role_id` INT UNSIGNED NOT NULL,
  `lname` VARCHAR(100) NOT NULL,
  `fname` VARCHAR(100) NOT NULL,
  `mname` VARCHAR(100) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `address_line` VARCHAR(255) NOT NULL,
  `city_municipality` VARCHAR(45) NOT NULL,
  `province` VARCHAR(45) NOT NULL,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_ref_roles_user`
    FOREIGN KEY (`role_id`)
    REFERENCES `sneakerhead`.`ref_roles` (`role_id`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE UNIQUE INDEX `email_UNIQUE` ON `sneakerhead`.`users` (`email` ASC);

SHOW WARNINGS;
CREATE UNIQUE INDEX `username_UNIQUE` ON `sneakerhead`.`users` (`username` ASC);

SHOW WARNINGS;
CREATE UNIQUE INDEX `pw_hash_UNIQUE` ON `sneakerhead`.`users` (`pw_hash` ASC);

SHOW WARNINGS;
CREATE UNIQUE INDEX `id_UNIQUE` ON `sneakerhead`.`users` (`id` ASC);

SHOW WARNINGS;
CREATE INDEX `fk_ref_roles_user_idx` ON `sneakerhead`.`users` (`role_id` ASC);

SHOW WARNINGS;
INSERT INTO `sneakerhead`.`users` (`username`, `email`, `phone_no`, `pw_hash`, `role_id`, `lname`, `fname`, `mname`,`created_at`, `updated_at`, `address_line`, `city_municipality`, `province`) VALUES
('sneaker_admin1', 'aaron_barcelita@dlsu.edu.ph', '09174567890', '$2y$10$uO0nwaKWRYJ3iwek5/pUY.vRAiK4i0r5GJnHeVHm30CwIPEyFknvK', 1, 'Barcelita', 'Aaron John', 'Chucas', DEFAULT, DEFAULT, 'Brgy. Santiago', 'General Trias', 'Cavite'),
('sneaker_admin2', 'john_mendoza@dlsu.edu.ph', '09982345678', '$2y$10$vXH5hAqX3w05LcT3ayZHweQsBAkU8lIOq0JEf3PAsWBriMBhS6c2y', 1, 'Mendoza', 'John Kirbie', 'Garcia', DEFAULT, DEFAULT, 'Arnaiz Ave.', 'Pasay City', 'Metro Manila'),
('rgcaimoy', 'riane_caimoy@dlsu.edu.ph', '09567891234', '$2y$10$gPhGV1Kq0lKbnmAekofaTu7GGFvz24Se9hrO.65eJS3lcCTkbBJjG', 2, 'Caimoy', 'Riane George', 'Recto', DEFAULT, DEFAULT, 'Rizal St.', 'Calamba', 'Laguna'),
('ramagbitang', 'roel_magbitang@dlsu.edu.ph', '09153456789', '$2y$10$KKYBGESF674Z.AuYEdOCyuDH5eZX/QpG/aabOSjgeTB5PFFt5WF1O', 2, 'Magbitang', 'Roel Andre', 'Santos', DEFAULT, DEFAULT, 'Mabini Ave.', 'Dasmarinas', 'Cavite'),
('jecariaga', 'josh_cariaga@dlsu.edu.ph', '09325678901', '$2y$10$DfeDix3VapncHWuVsF9.2e7WnzsU/seQc5eJ2VzQJJzs5EFtDnMJG', 2, 'Cariaga', 'Josh Enrico', 'Pesigan', DEFAULT, DEFAULT, 'Aguinaldo Hwy.', 'Binan', 'Laguna'),
('ajcbarcelita', 'ajcbarcelita@gmail.com', '09455034508', '$2y$10$9OHw82sztwtX46FaD3N7w.zQjmm2qPqIFFRtUgculySovF11sK2gO', 2, 'Barcelita', 'Aaron John', 'Chucas', DEFAULT, DEFAULT, 'Brgy. Santiago', 'General Trias', 'Cavite'),
('jkmendoza', 'john_kirbie_mendoza@dlsu.edu.ph', '09456466978', '$2y$10$xBXl5awgfUnwtYFOmKu70.KNxsMarS6sg.8CrcbuaZee/881zF4q.', 2, 'Mendoza', 'John Kirbie', 'Garcia', DEFAULT, DEFAULT, 'Arnaiz Ave.', 'Pasay City', 'Metro Manila');

-- -----------------------------------------------------
-- Table `sneakerhead`.`shoes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sneakerhead`.`shoes` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `sneakerhead`.`shoes` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `brand` VARCHAR(45) NOT NULL,
  `price` DECIMAL(10,2) UNSIGNED NOT NULL,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE UNIQUE INDEX `name_UNIQUE` ON `sneakerhead`.`shoes` (`name` ASC);

SHOW WARNINGS;
CREATE UNIQUE INDEX `id_UNIQUE` ON `sneakerhead`.`shoes` (`id` ASC);

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `sneakerhead`.`ref_us_sizes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sneakerhead`.`ref_us_sizes` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `sneakerhead`.`ref_us_sizes` (
  `shoe_size` DECIMAL(3,1) UNSIGNED NOT NULL,
  PRIMARY KEY (`shoe_size`))
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE UNIQUE INDEX `shoe_size_UNIQUE` ON `sneakerhead`.`ref_us_sizes` (`shoe_size` ASC);

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `sneakerhead`.`shoe_size_inventory`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sneakerhead`.`shoe_size_inventory` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `sneakerhead`.`shoe_size_inventory` (
  `shoe_id` INT UNSIGNED NOT NULL,
  `shoe_us_size` DECIMAL(3,1) UNSIGNED NOT NULL,
  `stock` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`shoe_id`, `shoe_us_size`),
  CONSTRAINT `fk_shoe_size_inventory_shoes`
    FOREIGN KEY (`shoe_id`)
    REFERENCES `sneakerhead`.`shoes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_shoe_size_inventory_ref_us_sizes`
    FOREIGN KEY (`shoe_us_size`)
    REFERENCES `sneakerhead`.`ref_us_sizes` (`shoe_size`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_shoe_size_inventory_shoes_idx` ON `sneakerhead`.`shoe_size_inventory` (`shoe_id` ASC);

SHOW WARNINGS;
CREATE INDEX `fk_shoe_size_inventory_ref_us_sizes_idx` ON `sneakerhead`.`shoe_size_inventory` (`shoe_us_size` ASC);

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `sneakerhead`.`promo_codes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sneakerhead`.`promo_codes` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `sneakerhead`.`promo_codes` (
  `promo_code` VARCHAR(20) NOT NULL,
  `discount_type` ENUM('Fixed', 'Percentage') NOT NULL,
  `discount_value` DECIMAL(10,2) UNSIGNED NOT NULL,
  `min_purchase` DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0.00,
  `max_discount` DECIMAL(10,2) UNSIGNED NULL DEFAULT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`promo_code`))
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE UNIQUE INDEX `promo_code_UNIQUE` ON `sneakerhead`.`promo_codes` (`promo_code` ASC);

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `sneakerhead`.`shopping_cart`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sneakerhead`.`shopping_cart` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `sneakerhead`.`shopping_cart` (
  `cart_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`cart_id`),
  CONSTRAINT `fk_shopping_cart_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `sneakerhead`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE UNIQUE INDEX `user_id_UNIQUE` ON `sneakerhead`.`shopping_cart` (`user_id` ASC);

SHOW WARNINGS;
CREATE UNIQUE INDEX `cart_id_UNIQUE` ON `sneakerhead`.`shopping_cart` (`cart_id` ASC);

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `sneakerhead`.`shopping_cart_items`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sneakerhead`.`shopping_cart_items` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `sneakerhead`.`shopping_cart_items` (
  `cart_item_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `cart_id` INT UNSIGNED NOT NULL,
  `shoe_id` INT UNSIGNED NOT NULL,
  `shoe_us_size` DECIMAL(3,1) UNSIGNED NOT NULL,
  `quantity` INT NOT NULL,
  `price_at_addition` DECIMAL(10,2) UNSIGNED NOT NULL,
  `added_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_item_id`),
  CONSTRAINT `fk_shopping_cart_items_shopping_cart1`
    FOREIGN KEY (`cart_id`)
    REFERENCES `sneakerhead`.`shopping_cart` (`cart_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_shopping_cart_items_shoe_size_inventory1`
    FOREIGN KEY (`shoe_id` , `shoe_us_size`)
    REFERENCES `sneakerhead`.`shoe_size_inventory` (`shoe_id` , `shoe_us_size`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_shopping_cart_items_shopping_cart1_idx` ON `sneakerhead`.`shopping_cart_items` (`cart_id` ASC);

SHOW WARNINGS;
CREATE INDEX `fk_shopping_cart_items_shoe_size_inventory1_idx` ON `sneakerhead`.`shopping_cart_items` (`shoe_id` ASC, `shoe_us_size` ASC);

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `sneakerhead`.`orders`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sneakerhead`.`orders` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `sneakerhead`.`orders` (
  `order_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `total_price` DECIMAL(10,2) UNSIGNED NOT NULL,
  `promo_code` VARCHAR(20) NULL DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  CONSTRAINT `fk_orders_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `sneakerhead`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_orders_promo_codes1`
    FOREIGN KEY (`promo_code`)
    REFERENCES `sneakerhead`.`promo_codes` (`promo_code`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_orders_users1_idx` ON `sneakerhead`.`orders` (`user_id` ASC);

SHOW WARNINGS;
CREATE INDEX `fk_orders_promo_codes1_idx` ON `sneakerhead`.`orders` (`promo_code` ASC);

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `sneakerhead`.`order_items`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sneakerhead`.`order_items` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `sneakerhead`.`order_items` (
  `order_item_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` INT UNSIGNED NOT NULL,
  `shoe_id` INT UNSIGNED NOT NULL,
  `shoe_size` DECIMAL(3,1) UNSIGNED NOT NULL,
  `quantity` INT UNSIGNED NOT NULL,
  `price_at_purchase` DECIMAL(10,2) UNSIGNED NOT NULL,
  `subtotal` DECIMAL(10,2) UNSIGNED NOT NULL,
  PRIMARY KEY (`order_item_id`),
  CONSTRAINT `fk_order_items_orders1`
    FOREIGN KEY (`order_id`)
    REFERENCES `sneakerhead`.`orders` (`order_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_items_shoe_size_inventory1`
    FOREIGN KEY (`shoe_id` , `shoe_size`)
    REFERENCES `sneakerhead`.`shoe_size_inventory` (`shoe_id` , `shoe_us_size`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_order_items_orders1_idx` ON `sneakerhead`.`order_items` (`order_id` ASC);

SHOW WARNINGS;
CREATE INDEX `fk_order_items_shoe_size_inventory1_idx` ON `sneakerhead`.`order_items` (`shoe_id` ASC, `shoe_size` ASC);

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `sneakerhead`.`shoe_reviews`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sneakerhead`.`shoe_reviews` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `sneakerhead`.`shoe_reviews` (
  `review_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `shoe_id` INT UNSIGNED NOT NULL,
  `rating` INT UNSIGNED NOT NULL,
  `review_text` VARCHAR(500) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  CONSTRAINT `fk_shoe_reviews_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `sneakerhead`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_shoe_reviews_shoes1`
    FOREIGN KEY (`shoe_id`)
    REFERENCES `sneakerhead`.`shoes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_shoe_reviews_users1_idx` ON `sneakerhead`.`shoe_reviews` (`user_id` ASC);

SHOW WARNINGS;
CREATE INDEX `fk_shoe_reviews_shoes1_idx` ON `sneakerhead`.`shoe_reviews` (`shoe_id` ASC);

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `sneakerhead`.`shoe_images`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sneakerhead`.`shoe_images` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `sneakerhead`.`shoe_images` (
  `image_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `shoe_id` INT UNSIGNED NOT NULL,
  `image_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`image_id`),
  CONSTRAINT `fk_shoe_images_shoes1`
    FOREIGN KEY (`shoe_id`)
    REFERENCES `sneakerhead`.`shoes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE UNIQUE INDEX `image_name_UNIQUE` ON `sneakerhead`.`shoe_images` (`image_name` ASC);

SHOW WARNINGS;
CREATE UNIQUE INDEX `image_id_UNIQUE` ON `sneakerhead`.`shoe_images` (`image_id` ASC);

SHOW WARNINGS;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
<<<<<<< HEAD
USE `sneakerhead`;
=======
>>>>>>> 84b64e75874123b1f822e66ec5b5208b674eae1a
