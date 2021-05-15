# Module "pictures"
CREATE TABLE IF NOT EXISTS `ip_pictures` (
  `picture_id`          INT(11)       NOT NULL AUTO_INCREMENT,
  `picture_name`        TEXT          NOT NULL,
  `picture_description` LONGTEXT      DEFAULT NULL,
  PRIMARY KEY (`picture_id`)
)
  ENGINE = MyISAM
  DEFAULT CHARSET = utf8;
