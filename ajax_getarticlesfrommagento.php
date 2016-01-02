<?php

require_once "database.php";

if (array_key_exists('function', $_POST)){
    $function = $_POST['function'];
}

//echo $function . "\n";

if (array_key_exists('sid', $_POST)){
    $sid = $_POST['sid'];
}

$query = "SELECT `entity_id`, `lieferbar_value`, `manufacturer_value`, `name`, `price`, `sku`, S.qty, S.min_qty, S.is_in_stock ";
$query = $query . "FROM `catalog_product_flat_2` inner JOIN `cataloginventory_stock_item` AS S on `entity_id` = product_id";

$link = OpenMagentoBase();
$res = mysql_query($query, $link);
$anzahl = @mysql_num_rows($res);

if ($anzahl > 0) {
    $erg = $anzahl . " Artikel wurden gelesen und aktualisiert.";
    $arts = array();
    $link2 = OpenDatabase();
    $sql = "DELETE FROM `Artikel`";
    $res2 = mysql_query($sql, $link2);
    while ($unit = mysql_fetch_object($res)) {
        $art = new Article();
        $art->entity_id = $unit->entity_id;
        $art->manufacturer = $unit->manufacturer_value;
        $art->sku = $unit->sku;
        $art->name = $unit->name;
        $art->price = $unit->price;
        $art->lieferbar = $unit->lieferbar_value;
        $art->qty = 0.00;
        $art->min_qty = 0.00;
        $art->is_in_stock = 0;// EAN Nummer aus SELECT `catalog_product_entity_varchar` AS EAN entity_id + on attribute_id = 154

        $sqlean = "SELECT * FROM `catalog_product_entity_varchar` WHERE entity_id = '" . $art->entity_id . "' AND attribute_id = 154";
        $link1 = OpenMagentoBase();
        $res3 = mysql_query($sqlean, $link1);
        if ($unit2 = mysql_fetch_object($res3)){
            $art->ean =$unit2->value;
        }
// ans artikel array anhängen
        $arts[] = $art;
//        print '<a href="index.php?tmpl=telefonatmodus.htm&sid=' . $sid . '&id=' . $unit->name . '">' . ($unit->name) . " (" . ($unit->sku) . ")" . "</a>,";
    }
// Artikelliste in Artikelstamm einfügen
    $link = OpenDatabase();
    foreach ($arts as $art){
//        var_dump($art);
        $sql = "INSERT INTO `Artikel` (`entity_id`, `lieferbar_value`, `manufacturer_value`, `name`, `price`, `sku`, `qty`, `min_qty`, `is_in_stock`, `ean` ) ";
        $sql = $sql . "Values (";
        $sql = $sql . "'$art->entity_id', '$art->lieferbar', '$art->manufacturer', '$art->name', '$art->price', '$art->sku', ";
        $sql = $sql . "'$art->qty', '$art->min_qty', '$art->is_in_stock', '$art->ean')";
//echo $sql . "<br>";
        $res = mysql_query($sql, $link);
    }
} else {
    $erg = "Keine Artikel gefunden !!!";
}

echo $erg;

?>