# Module "products"
CREATE TABLE IF NOT EXISTS `ip_materials` (
  `material_id`          INT(11)       NOT NULL AUTO_INCREMENT,
  `product_id`           INT(11)       DEFAULT NULL,
  `material_name`        TEXT          NOT NULL,
  `material_description` LONGTEXT      DEFAULT NULL,
  `material_price`       DECIMAL(20,2) DEFAULT NULL,
  `material_provider_name` TEXT        DEFAULT NULL,
  PRIMARY KEY (`material_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;
