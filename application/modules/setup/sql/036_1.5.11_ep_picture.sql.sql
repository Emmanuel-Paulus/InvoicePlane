# Module "pictures"
CREATE TABLE IF NOT EXISTS `ip_pictures` (
  `picture_id`          INT(11)       NOT NULL AUTO_INCREMENT,
  `picture_name`        TEXT          NOT NULL,
  `picture_description` LONGTEXT      DEFAULT NULL,
  PRIMARY KEY (`picture_id`)
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
     ADD COLUMN `picture_id` INT(11) DEFAULT NULL,
     ADD FOREIGN KEY `fk_materials_pictures`(`picture_id`) 
        REFERENCES `pictures`(`picture_id`) 
        ON DELETE SET NULL;
