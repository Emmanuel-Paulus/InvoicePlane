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

---

CREATE TABLE `ip_providers` (
  `provider_id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_date_created` datetime NOT NULL,
  `provider_date_modified` datetime NOT NULL,
  `provider_name` text DEFAULT NULL,
  `provider_address_1` text DEFAULT NULL,
  `provider_address_2` text DEFAULT NULL,
  `provider_city` text DEFAULT NULL,
  `provider_state` text DEFAULT NULL,
  `provider_zip` text DEFAULT NULL,
  `provider_country` text DEFAULT NULL,
  `provider_phone` text DEFAULT NULL,
  `provider_fax` text DEFAULT NULL,
  `provider_mobile` text DEFAULT NULL,
  `provider_email` text DEFAULT NULL,
  `provider_web` text DEFAULT NULL,
  `provider_vat_id` text DEFAULT NULL,
  `provider_tax_code` text DEFAULT NULL,
  `provider_language` varchar(255) DEFAULT 'system',
  `provider_active` int(1) NOT NULL DEFAULT 1,
  `provider_surname` varchar(255) DEFAULT NULL,
  `provider_avs` varchar(16) DEFAULT NULL,
  `provider_insurednumber` varchar(30) DEFAULT NULL,
  `provider_veka` varchar(30) DEFAULT NULL,
  `provider_birthdate` date DEFAULT NULL,
  `provider_gender` int(1) DEFAULT 0,
  PRIMARY KEY (`provider_id`),
  KEY `provider_active` (`provider_active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ip_provider_notes` (
  `provider_note_id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `provider_note_date` date NOT NULL,
  `provider_note` longtext NOT NULL,
  PRIMARY KEY (`provider_note_id`),
  KEY `provider_id` (`provider_id`,`provider_note_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ip_provider_custom` (
  `provider_custom_id` int(11) NOT NULL AUTO_INCREMENT,
  `provider_id` int(11) NOT NULL,
  `provider_custom_fieldid` int(11) NOT NULL,
  `provider_custom_fieldvalue` text DEFAULT NULL,
  PRIMARY KEY (`provider_custom_id`),
  UNIQUE KEY `provider_id` (`provider_id`,`provider_custom_fieldid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ip_incoms` (
  `incom_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `provider_id` int(11) NOT NULL,
  `incom_status_id` tinyint(2) NOT NULL DEFAULT 1,
  `incom_date_created` date NOT NULL,
  `incom_date_modified` datetime NOT NULL,
  `incom_date_expires` date NOT NULL,
  `incom_number` varchar(100) DEFAULT NULL,
  `incom_discount_amount` decimal(20,2) DEFAULT NULL,
  `incom_discount_percent` decimal(20,2) DEFAULT NULL,
  `incom_url_key` char(32) NOT NULL,
  `notes` longtext DEFAULT NULL,
  PRIMARY KEY (`incom_id`),
  KEY `user_id` (`user_id`,`provider_id`,`incom_date_created`,`incom_date_expires`,`incom_number`),
  KEY `incom_status_id` (`incom_status_id`),
  KEY `incom_url_key` (`incom_url_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ip_incom_amounts` (
  `incom_amount_id` int(11) NOT NULL AUTO_INCREMENT,
  `incom_id` int(11) NOT NULL,
  `incom_item_subtotal` decimal(20,2) DEFAULT NULL,
  `incom_item_tax_total` decimal(20,2) DEFAULT NULL,
  `incom_tax_total` decimal(20,2) DEFAULT NULL,
  `incom_total` decimal(20,2) DEFAULT NULL,
  PRIMARY KEY (`incom_amount_id`),
  KEY `incom_id` (`incom_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ip_incom_customs` (
  `incom_custom_id` int(11) NOT NULL AUTO_INCREMENT,
  `incom_id` int(11) NOT NULL,
  `incom_custom_fieldid` int(11) NOT NULL,
  `incom_custom_fieldvalue` text DEFAULT NULL,
  PRIMARY KEY (`incom_custom_id`),
  UNIQUE KEY `incom_custom_fieldid` (`incom_id`,`incom_custom_fieldid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ip_incom_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `incom_id` int(11) NOT NULL,
  `item_tax_rate_id` int(11) NOT NULL,
  `item_material_id` int(11) DEFAULT NULL,
  `item_date_added` date NOT NULL,
  `item_name` text DEFAULT NULL,
  `item_description` text DEFAULT NULL,
  `item_quantity` decimal(20,2) DEFAULT NULL,
  `item_price` decimal(20,2) DEFAULT NULL,
  `item_discount_amount` decimal(20,2) DEFAULT NULL,
  `item_order` int(2) NOT NULL DEFAULT 0,
  `item_product_unit` varchar(50) DEFAULT NULL,
  `item_product_unit_id` int(11) DEFAULT NULL,
  `item_picture_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `incom_id` (`incom_id`,`item_date_added`,`item_order`),
  KEY `item_tax_rate_id` (`item_tax_rate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ip_incom_item_amounts` (
  `item_amount_id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `item_subtotal` decimal(20,2) DEFAULT NULL,
  `item_tax_total` decimal(20,2) DEFAULT NULL,
  `item_discount` decimal(20,2) DEFAULT NULL,
  `item_total` decimal(20,2) DEFAULT NULL,
  PRIMARY KEY (`item_amount_id`),
  KEY `item_id` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ip_incom_tax_rates` (
  `incom_tax_rate_id` int(11) NOT NULL AUTO_INCREMENT,
  `incom_id` int(11) NOT NULL,
  `tax_rate_id` int(11) NOT NULL,
  `include_item_tax` int(1) NOT NULL DEFAULT 0,
  `incom_tax_rate_amount` decimal(20,2) DEFAULT NULL,
  PRIMARY KEY (`incom_tax_rate_id`),
  KEY `incom_id` (`incom_id`),
  KEY `tax_rate_id` (`tax_rate_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `ip_incom_custom` (
  `incom_custom_id` int(11) NOT NULL AUTO_INCREMENT,
  `incom_id` int(11) NOT NULL,
  `incom_custom_fieldid` int(11) NOT NULL,
  `incom_custom_fieldvalue` text DEFAULT NULL,
  PRIMARY KEY (`incom_custom_id`),
  UNIQUE KEY `incom_id` (`incom_id`,`incom_custom_fieldid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `ip_provider_notes`
     ADD FOREIGN KEY `fk_provider_notes_provider`(`provider_id`)
        REFERENCES `ip_providers`(`provider_id`);

ALTER TABLE `ip_incoms`
     ADD FOREIGN KEY `fk_incom_user`(`user_id`)
        REFERENCES `ip_users`(`user_id`);
ALTER TABLE `ip_incoms`
     ADD FOREIGN KEY `fk_incom_provider`(`provider_id`)
        REFERENCES `ip_providers`(`provider_id`);

ALTER TABLE `ip_incom_amounts`
     ADD FOREIGN KEY `fk_incom_amounts_incom`(`incom_id`)
        REFERENCES `ip_incoms`(`incom_id`);

ALTER TABLE `ip_incom_items`
     ADD FOREIGN KEY `fk_incom_items_incom`(`incom_id`)
        REFERENCES `ip_incoms`(`incom_id`);
ALTER TABLE `ip_incom_items`
     ADD FOREIGN KEY `fk_incom_items_incom`(`item_tax_rate_id`)
        REFERENCES `ip_tax_rates`(`tax_rate_id`);
ALTER TABLE `ip_incom_items`
     ADD FOREIGN KEY `fk_incom_items_material`(`item_material_id`)
        REFERENCES `ip_materials`(`material_id`);
ALTER TABLE `ip_incom_items`
     ADD FOREIGN KEY `fk_incom_items_pictures`(`item_picture_id`)
        REFERENCES `pictures`(`picture_id`);

ALTER TABLE `ip_incom_amounts`
     ADD FOREIGN KEY `fk_incom_amounts_incom`(`incom_id`)
        REFERENCES `ip_incoms`(`incom_id`);

ALTER TABLE `ip_incom_tax_rates`
     ADD FOREIGN KEY `fk_incom_tax_rates_incom`(`incom_id`)
        REFERENCES `ip_incoms`(`incom_id`);
ALTER TABLE `ip_incom_tax_rates`
     ADD FOREIGN KEY `fk_incom_tax_rates_tax_rate`(`tax_rate_id`)
        REFERENCES `ip_tax_rates`(`tax_rate_id`);

ALTER TABLE `ip_products`
    ADD COLUMN `product_url` TEXT DEFAULT NULL;
ALTER TABLE `ip_materials`
    ADD COLUMN `family_id` TEXT DEFAULT NULL;
ALTER TABLE `ip_materials`
     ADD FOREIGN KEY `fk_material_family_id`(`family_id`)
        REFERENCES `ip_family`(`family_id`);

ALTER TABLE `ip_invoice_items`
     ADD COLUMN `item_material_id` INT(11) DEFAULT NULL,
     ADD FOREIGN KEY `fk_invoice_items_materials`(`item_material_id`)
        REFERENCES `materials`(`material_id`)
        ON DELETE SET NULL;

ALTER TABLE `ip_quote_items`
     ADD COLUMN `item_material_id` INT(11) DEFAULT NULL,
     ADD FOREIGN KEY `fk_quote_items_materials`(`item_material_id`)
        REFERENCES `materials`(`material_id`)
        ON DELETE SET NULL;
