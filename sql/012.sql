CREATE TABLE `hififabrik_intern`.`orders_history` ( `order_increment_id` VARCHAR(50) NOT NULL , `order_activity` VARCHAR(10) NOT NULL ,
                               `order_memo` TEXT NOT NULL , `book_user` VARCHAR(10) NOT NULL , `book_date_first` DATETIME NOT NULL ,
                                `book_date` TIMESTAMP NOT NULL ) ENGINE = InnoDB;

ALTER TABLE `orders_history` ADD PRIMARY KEY( `order_increment_id`, `order_activity`);

ALTER TABLE `orders_history` ADD `order_activity_text` VARCHAR(255) NOT NULL AFTER `order_activity`;
