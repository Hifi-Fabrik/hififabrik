SELECT `entity_id`, `lieferbar_value`, `manufacturer_value`, `name`, `price`, `sku`, S.qty, S.min_qty, S.is_in_stock, E.value;
FROM `catalog_product_flat_2`
inner JOIN `cataloginventory_stock_item` AS S on `entity_id` = product_id
inner JOIN `catalog_product_entity_varchar` AS E on `entity_id` = product_id AND and attribute_id = 154
