CREATE TABLE `hififabrik_intern`.`orders_articles` ( `order_number` INT(10) UNSIGNED NOT NULL ,
`order_line` INT(10) UNSIGNED NOT NULL , `quote_id` INT(10) UNSIGNED NOT NULL ,
`qty_ordered` DECIMAL(12,4) NOT NULL , `sku` VARCHAR(255) NOT NULL , `name` VARCHAR(255) NOT NULL ,
`ean` INT(13) UNSIGNED NOT NULL , `einheit` VARCHAR(10) NOT NULL , `vpe` SMALLINT NOT NULL ,
`packstuecke` SMALLINT NOT NULL , `product_id` INT(10) UNSIGNED NOT NULL , `product_options` TEXT NOT NULL ,
`product_eans` VARCHAR(255) NOT NULL , `product_base` VARCHAR(255) NOT NULL , `product_lort` SMALLINT UNSIGNED NOT NULL ,
`product_ltxt` VARCHAR(50) NOT NULL , `product_quotes` VARCHAR(50) NOT NULL , `data_error` VARCHAR(255) NOT NULL ,
`not_in_store` BOOLEAN NOT NULL , `product_lort_target` SMALLINT UNSIGNED NOT NULL , `product_ltxt_target` VARCHAR(50) NOT NULL ,
`product_move_kz` VARCHAR(10) NOT NULL , `book_kz` VARCHAR(6) NOT NULL , `book_date` TIMESTAMP NOT NULL ,
`book_user` VARCHAR(5) NOT NULL , `status` VARCHAR(10) NOT NULL ) ENGINE = InnoDB;

ALTER TABLE `orders_articles` ADD PRIMARY KEY( `order_number`, `order_line`);

