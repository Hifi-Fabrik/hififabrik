<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Reservierung Versand Lagerbestand</title>

    <style>

        table, th, td {
            border: 1px solid black;
        }

        body        { width: 30em; }
        .sans-serif { font-family: sans-serif; }
        .serif      { font-family: serif; }
        .cursive    { font-family: cursive; }
        .fantasy    { font-family: fantasy; }
        .monospace  { font-family: monospace; }
    </style>
    <style src="style.css"></style>
    <script src="java.js"></script>
</head>
<body>

<?php
/**
 * Created by PhpStorm.
 * User: Rudi
 * Date: 24.11.2015
 * Time: 16:13
 */

require_once ('database.php');
require_once ('menue.php');
require_once ('getorder.php');

GetMKZandUGP($MKZ, $UGP, $order, $func);

echo $func;


// damits weitergeht
if ($func == ""){
    $func="WARES";
}

echo "<form action=\"lager.php" . "?MKZ="  . $MKZ . "&UPG=" . $UGP . "&function=" . $func . "&order=" . $order . "\" method=\"get\">";

$servername = "localhost";
$username = "hififabrik_int";
$password = "Hf54mC74slRw";
$dbname = "hififabrik_intern";

if (CheckDataBaseReady ($servername, $username, $password, $dbname)){
//    echo "<br>database ready to use<br>\n";
} else {
    echo "<br>database NOT ready to use<br>\n";
}

if ($func == "") {
    $lager = LoadMenuItems($servername, $username, $password, $dbname, 'LAGER', 'admin');
    BuildMenuTable($lager, 300, 1, 1, 3, 50, 350, "center", array("#7fff0", "#f5f5dc", "#6495ed"));
} else {
// barcode scanfeld
    $eanorder = "";
    if (isset($_GET["order"])){
        $eanorder = $_GET["order"];
    }

    if ($eanorder == "") {
        echo "<h3>Bitte scannen Sie die Auftragsnummer</h3><br>";
        echo "<input autofocus type=\"text\" name=\"order\" style=\"font-family: Arial; font-size: 18pt; height: 24px\" value=\"" . $eanorder . "\">";
        echo "<br><br>";
    } else {
        $order = substr($eanorder, 0, strlen($eanorder) - 1);
        $order = ltrim($order, '0');
        if ($order != "") {
            $order_db = GetOrder($order);
            $orderlines = GetOrderLine($order_db->entity_id, $order);
            if ($order_db->entity_id != "") {
                echo CreateOrderHead($order_db, $MKZ, $UGP);
                echo CreateOrderArticle($orderlines, $MKZ, $UGP);
            } else {
                echo "<span style=\"font-size: 18pt;color:#ff0000;\" >Bestellung " . $order . " existiert nicht.</span>";
                echo "<br><br>";
            }
        }

        if ($func == "WARES") {
            echo "<button class=\"btnExample\" style=\"width:400px\">nächsten Auftrag bearbeiten</button><br><br>";
            echo "<button class=\"btnExample\" style=\"width:400px\" onclick='return CheckScan();'>Auftragsreservierung durchführen</button><br><br>";
            echo "<button class=\"btnExample\" style=\"width:400px\">Reservierung STORNIEREN</button><br><br>";
        }
    }
}

echo "</form><br>\n";

echo "<br></body><br>\n";
echo "<br></html><br>\n";
