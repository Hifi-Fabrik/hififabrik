CREATE TABLE `hififabrik_intern`.`lagerbewegungen` ( `order_number` INT(10) UNSIGNED NOT NULL , `order_line` INT(10) UNSIGNED NOT NULL ,
`sku` VARCHAR(255) NOT NULL , `quantity` INT NOT NULL , `vpe` SMALLINT NOT NULL , `packstuecke` SMALLINT NOT NULL , `Einheit` VARCHAR(10) NOT NULL ,
`book_kz` VARCHAR(10) NOT NULL , `book_sign` SMALLINT NOT NULL , `book_date` TIMESTAMP NOT NULL , `book_user` VARCHAR(10) NOT NULL ,
`storeid` INT UNSIGNED NOT NULL , `storename` VARCHAR(10) NOT NULL ) ENGINE = InnoDB;

ALTER TABLE `lagerbewegungen` ADD `name` VARCHAR(255) NOT NULL AFTER `sku`;

ALTER TABLE `lagerbewegungen` CHANGE `quantity` `quantity` DECIMAL(12,4) NOT NULL;

ALTER TABLE `lagerbewegungen` CHANGE `book_kz` `book_kz` VARCHAR(6) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `lagerbewegungen` CHANGE `book_user` `book_user` VARCHAR(5) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `lagerbewegungen` ADD `status` VARCHAR(10) NOT NULL AFTER `book_user`;