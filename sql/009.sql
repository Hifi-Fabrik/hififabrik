ALTER TABLE `Artikel` ADD `entity_id` INT(10) UNSIGNED NOT NULL FIRST,
ADD `producer` VARCHAR(50) NOT NULL AFTER `entity_id`,
ADD `sku` VARCHAR(50) NOT NULL AFTER `producer`,
ADD `name` VARCHAR(255) NOT NULL AFTER `sku`,
ADD `status` SMALLINT NOT NULL AFTER `name`, ADD PRIMARY KEY (`entity_id`);


ALTER TABLE `Artikel` ADD `qty` DECIMAL(12,4) NOT NULL AFTER `status`, ADD `min_qty` DECIMAL(12,4) NOT NULL
AFTER `qty`, ADD `is_in_stock` SMALLINT(5) UNSIGNED NOT NULL AFTER `min_qty`;