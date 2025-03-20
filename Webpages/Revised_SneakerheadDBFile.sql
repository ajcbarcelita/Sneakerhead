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
  `role_id` INT UNSIGNED NOT NULL,
  `role_name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`role_id`))
ENGINE = InnoDB;

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
  `phone_no` INT NOT NULL,
  `pw_hash` VARCHAR(255) NOT NULL,
  `role_id` INT UNSIGNED NOT NULL,
  `lname` VARCHAR(100) NOT NULL,
  `fname` VARCHAR(100) NOT NULL,
  `mname` VARCHAR(100) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `address_line` VARCHAR(255) NOT NULL,
  `province` VARCHAR(45) NOT NULL,
  `city_municipality` VARCHAR(45) NOT NULL,
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
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE UNIQUE INDEX `name_UNIQUE` ON `sneakerhead`.`shoes` (`name` ASC) ;

SHOW WARNINGS;

INSERT INTO `sneakerhead`.`shoes` (`name`, `brand`, `price`) VALUES
('Nike Air Force 1', 'Nike', 90.00),
('Nike Dunk Low', 'Nike', 100.00),
('Air Jordan 1 Low', 'Jordan', 110.00),
('Adidas Ultraboost Light', 'Adidas', 180.00),
('Kobe 6 Proto \'Grinch\'', 'Nike', 220.00),
('Adidas OG Samba', 'Adidas', 80.00);


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
CREATE UNIQUE INDEX `shoe_size_UNIQUE` ON `sneakerhead`.`ref_us_sizes` (`shoe_size` ASC) ;

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
  CONSTRAINT `fk_shoes`
    FOREIGN KEY (`shoe_id`)
    REFERENCES `sneakerhead`.`shoes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ref_us_sizes`
    FOREIGN KEY (`shoe_us_size`)
    REFERENCES `sneakerhead`.`ref_us_sizes` (`shoe_size`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_ref_us_sizes_idx` ON `sneakerhead`.`shoe_size_inventory` (`shoe_us_size` ASC) ;

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
  CONSTRAINT `fk_users`
    FOREIGN KEY (`user_id`)
    REFERENCES `sneakerhead`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE UNIQUE INDEX `user_id_UNIQUE` ON `sneakerhead`.`shopping_cart` (`user_id` ASC) ;

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
  `price_at_addition` DECIMAL(15,2) UNSIGNED NOT NULL,
  `added_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_item_id`),
  CONSTRAINT `fk_shopping_cart_items_1`
    FOREIGN KEY (`shoe_id` , `shoe_us_size`)
    REFERENCES `sneakerhead`.`shoe_size_inventory` (`shoe_id` , `shoe_us_size`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_shopping_cart_items_2`
    FOREIGN KEY (`cart_id`)
    REFERENCES `sneakerhead`.`shopping_cart` (`cart_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_shopping_cart_items_1_idx` ON `sneakerhead`.`shopping_cart_items` (`shoe_id` ASC, `shoe_us_size` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_shopping_cart_items_2_idx` ON `sneakerhead`.`shopping_cart_items` (`cart_id` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `sneakerhead`.`promo_codes`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sneakerhead`.`promo_codes` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `sneakerhead`.`promo_codes` (
  `promo_code` VARCHAR(20) NOT NULL,
  `discount_type` ENUM("Fixed", "Percentage") NOT NULL,
  `discount_value` DECIMAL(10,2) UNSIGNED NOT NULL,
  `is_active` ENUM("Y", "N") NOT NULL DEFAULT 'Y',
  `is_deleted` ENUM("Y", "N") NOT NULL DEFAULT 'N',
  PRIMARY KEY (`promo_code`))
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE UNIQUE INDEX `promo_code_UNIQUE` ON `sneakerhead`.`promo_codes` (`promo_code` ASC) ;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `sneakerhead`.`orders`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sneakerhead`.`orders` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `sneakerhead`.`orders` (
  `order_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `total_price` DECIMAL(10,2) UNSIGNED NOT NULL,
  `promo_code` VARCHAR(20) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`),
  CONSTRAINT `fk_order_1`
    FOREIGN KEY (`promo_code`)
    REFERENCES `sneakerhead`.`promo_codes` (`promo_code`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_2`
    FOREIGN KEY (`user_id`)
    REFERENCES `sneakerhead`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_order_1_idx` ON `sneakerhead`.`orders` (`promo_code` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_order_2_idx` ON `sneakerhead`.`orders` (`user_id` ASC) ;

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
  CONSTRAINT `fk_order_items_1`
    FOREIGN KEY (`shoe_id` , `shoe_size`)
    REFERENCES `sneakerhead`.`shoe_size_inventory` (`shoe_id` , `shoe_us_size`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_order_items_2`
    FOREIGN KEY (`order_id`)
    REFERENCES `sneakerhead`.`orders` (`order_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_order_items_1_idx` ON `sneakerhead`.`order_items` (`shoe_id` ASC, `shoe_size` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_order_items_2_idx` ON `sneakerhead`.`order_items` (`order_id` ASC) ;

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
  `rating` INT UNSIGNED NOT NULL COMMENT 'Rating should only be from 1 - 5',
  `review_text` VARCHAR(500) NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`review_id`),
  CONSTRAINT `fk_shoe_reviews_1`
    FOREIGN KEY (`shoe_id`)
    REFERENCES `sneakerhead`.`shoes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_shoe_reviews_2`
    FOREIGN KEY (`user_id`)
    REFERENCES `sneakerhead`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `fk_shoe_reviews_1_idx` ON `sneakerhead`.`shoe_reviews` (`shoe_id` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_shoe_reviews_2_idx` ON `sneakerhead`.`shoe_reviews` (`user_id` ASC) ;

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
  CONSTRAINT `fk_shoe_images_1`
    FOREIGN KEY (`shoe_id`)
    REFERENCES `sneakerhead`.`shoes` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE UNIQUE INDEX `image_name_UNIQUE` ON `sneakerhead`.`shoe_images` (`image_name` ASC) ;

SHOW WARNINGS;
CREATE INDEX `fk_shoe_images_1_idx` ON `sneakerhead`.`shoe_images` (`shoe_id` ASC) ;
INSERT INTO `sneakerhead`.`shoe_images` (`shoe_id`, `image_name`, `file_path`) VALUES
(1, 'airforce1.jpg', 'images/airforce1.jpg'),
(2, 'dunklow.jpg', 'images/dunklow.jpg'),
(3, 'jordan1low.jpg', 'images/jordan1low.jpg'),
(4, 'ultraboost.jpg', 'images/ultraboost.jpg'),
(5, 'kobe6.jpg', 'images/kobe6.jpg'),
(6, 'samba.jpg', 'images/samba.jpg');
SHOW WARNINGS;
USE `sneakerhead` ;

-- -----------------------------------------------------
-- Placeholder table for view `sneakerhead`.`current_shoe_inventory`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sneakerhead`.`current_shoe_inventory` (`shoe_id` INT, `brand` INT, `shoe_name` INT, `price` INT, `shoe_us_size` INT, `stock` INT);
SHOW WARNINGS;

-- -----------------------------------------------------
-- Placeholder table for view `sneakerhead`.`users_order_history`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sneakerhead`.`users_order_history` (`user_id` INT, `order_id` INT, `username` INT, `order_datetime` INT, `shoe_id` INT, `shoe_name` INT, `size` INT, `quantity` INT, `unit_price` INT, `subtotal` INT, `promo_code` INT, `discount_value` INT);
SHOW WARNINGS;

-- -----------------------------------------------------
-- Placeholder table for view `sneakerhead`.`users_shopping_cart`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `sneakerhead`.`users_shopping_cart` (`user_id` INT, `username` INT, `cart_id` INT, `cart_item_id` INT, `shoe_id` INT, `shoe_name` INT, `size` INT, `quantity` INT, `unit_price` INT, `total_price` INT, `added_datetime` INT);
SHOW WARNINGS;

-- -----------------------------------------------------
-- View `sneakerhead`.`current_shoe_inventory`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sneakerhead`.`current_shoe_inventory`;
SHOW WARNINGS;
DROP VIEW IF EXISTS `sneakerhead`.`current_shoe_inventory` ;
SHOW WARNINGS;
USE `sneakerhead`;
CREATE  OR REPLACE VIEW `current_shoe_inventory` AS
SELECT
	s.id AS shoe_id,
    s.brand,
    s.name AS shoe_name,
    s.price,
    i.shoe_us_size,
    i.stock
FROM shoe_size_inventory i
JOIN shoes s ON i.shoe_id = s.id
JOIN ref_us_sizes us ON i.shoe_us_size = us.shoe_size
WHERE i.stock > 0
ORDER BY s.brand, s.name ASC;
SHOW WARNINGS;

-- -----------------------------------------------------
-- View `sneakerhead`.`users_order_history`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sneakerhead`.`users_order_history`;
SHOW WARNINGS;
DROP VIEW IF EXISTS `sneakerhead`.`users_order_history` ;
SHOW WARNINGS;
USE `sneakerhead`;
CREATE  OR REPLACE VIEW `users_order_history` AS
SELECT
    u.id AS user_id,
    o.order_id,
    u.username,
    o.created_at AS order_datetime,
    oi.shoe_id,
    s.name AS shoe_name,
    oi.shoe_size AS size,
    oi.quantity,
    oi.price_at_purchase AS unit_price,
    oi.subtotal, 
    o.promo_code,
    p.discount_value
FROM orders o
JOIN users u on o.user_id = u.id
JOIN order_items oi ON o.order_id = oi.order_id
JOIN shoes s ON oi.shoe_id = s.id
LEFT JOIN promo_codes p ON o.promo_code = p.promo_code
ORDER BY u.id, o.order_id;
SHOW WARNINGS;

-- -----------------------------------------------------
-- View `sneakerhead`.`users_shopping_cart`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `sneakerhead`.`users_shopping_cart`;
SHOW WARNINGS;
DROP VIEW IF EXISTS `sneakerhead`.`users_shopping_cart` ;
SHOW WARNINGS;
USE `sneakerhead`;
CREATE  OR REPLACE VIEW `users_shopping_cart` AS
SELECT
    u.id AS user_id,
    u.username,
    sc.cart_id,
    sci.cart_item_id,
    sci.shoe_id,
    s.name AS shoe_name,
    sci.shoe_us_size AS size,
    sci.quantity,
    sci.price_at_addition AS unit_price,
    (sci.quantity * sci.price_at_addition) AS total_price,
    sci.added_at AS added_datetime
FROM shopping_cart sc
JOIN users u ON sc.user_id = u.id
JOIN shopping_cart_items sci ON sc.cart_id = sci.cart_id
JOIN shoes s ON sci.shoe_id = s.id
ORDER BY u.id, sc.cart_id, sci.cart_item_id;
SHOW WARNINGS;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
USE `sneakerhead`;

DELIMITER $$

USE `sneakerhead`$$
DROP TRIGGER IF EXISTS `sneakerhead`.`calculateSubtotal` $$
SHOW WARNINGS$$
USE `sneakerhead`$$
CREATE DEFINER = CURRENT_USER TRIGGER `sneakerhead`.`calculateSubtotal` 
BEFORE INSERT ON `order_items` 
FOR EACH ROW
BEGIN
	SET NEW.subtotal = NEW.quantity * NEW.price_at_purchase;
END$$

SHOW WARNINGS$$

USE `sneakerhead`$$
DROP TRIGGER IF EXISTS `sneakerhead`.`recalculateSubtotal` $$
SHOW WARNINGS$$
USE `sneakerhead`$$
CREATE DEFINER = CURRENT_USER TRIGGER `sneakerhead`.`recalculateSubtotal` 
BEFORE UPDATE ON `order_items` 
FOR EACH ROW
BEGIN
	SET NEW.subtotal = NEW.quantity * NEW.price_at_purchase;
END$$

SHOW WARNINGS$$

DELIMITER ;
