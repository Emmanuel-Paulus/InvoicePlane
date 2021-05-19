CREATE TABLE IF NOT EXISTS `ip_pictures` (
  `picture_id`          INT(11)       NOT NULL AUTO_INCREMENT,
  `picture_name`        TEXT          NOT NULL,
  `picture_description` LONGTEXT      DEFAULT NULL,
  PRIMARY KEY (`picture_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `ip_materials` (
  `material_id`            INT(11)       NOT NULL AUTO_INCREMENT,
  `material_name`          TEXT          NOT NULL,
  `material_description`   LONGTEXT      DEFAULT NULL,
  `material_price`         DECIMAL(20,2) DEFAULT NULL,
  `material_price_amount`  INT(11)       DEFAULT NULL,
  `material_price_descr`   TEXT          DEFAULT NULL,
  `material_provider_name` TEXT          DEFAULT NULL,
  `material_url`           TEXT          DEFAULT NULL,
  `picture_id`             INT(11)       DEFAULT NULL,
  PRIMARY KEY (`material_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `ip_product_materials` (
  `product_material_id`  INT(11)       NOT NULL AUTO_INCREMENT,
  `product_id`           INT(11)       NOT NULL,
  `material_id`          INT(11)       NOT NULL,
  `prod_matr_amount`     INT(11)       DEFAULT NULL,
  PRIMARY KEY (`product_material_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;

ALTER TABLE `ip_products` 
     ADD COLUMN `picture_id` INT(11) DEFAULT NULL,
     ADD FOREIGN KEY `fk_products_pictures`(`picture_id`) 
        REFERENCES `pictures`(`picture_id`) 
        ON DELETE SET NULL;

ALTER TABLE `ip_invoice_items` 
     ADD COLUMN `item_picture_id` INT(11) DEFAULT NULL,
     ADD FOREIGN KEY `fk_material_pictures`(`item_picture_id`) 
        REFERENCES `pictures`(`picture_id`) 
        ON DELETE SET NULL;

ALTER TABLE `ip_quote_items`
     ADD COLUMN `item_picture_id` INT(11) DEFAULT NULL,
     ADD FOREIGN KEY `fk_quote_items_pictures`(`item_picture_id`) 
        REFERENCES `pictures`(`picture_id`) 
        ON DELETE SET NULL;

ALTER TABLE `ip_materials` 
     ADD FOREIGN KEY `fk_materials_pictures`(`picture_id`) 
        REFERENCES `pictures`(`picture_id`) 
        ON DELETE SET NULL;

ALTER TABLE `ip_product_materials` 
     ADD FOREIGN KEY `fk_ip_prod_matr_product`(`product_id`) 
        REFERENCES `products`(`product_id`);

ALTER TABLE `ip_product_materials` 
     ADD FOREIGN KEY `fk_ip_prod_matr_material`(`material_id`) 
        REFERENCES `materials`(`material_id`);
