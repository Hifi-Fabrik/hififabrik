<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Bestellungen / Reservierungen</title>

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
    <script src="java.js"></script>
</head>
<body>


<?php
/**         <?php UpdateOrder(order, val)?>

 * Created by PhpStorm.
 * User: Rudi
 * Date: 23.10.2015
 * Time: 13:36
 */
// debug step
//echo "order.php start<br>\n";
require_once ('database.php');
require_once ('menue.php');
require_once ('getorder.php');

$MKZ = "";
$UGP = "";
$order = "";
$function = "";
//GetMKZandUGP($MKZ, $UGP, $order, $function);

if (isset ($_GET["MKZ"])) {
    $MKZ = $_GET["MKZ"];
//        echo $MKZ . "<br>";
} else {
    $MKZ = "";
}

if (isset ($_GET["UGP"])) {
    $UGP = $_GET["UGP"];
//        echo $UGP . "<br>";
} else {
    $UGP = "";
}
if (isset ($_GET["order"])) {
    $order = $_GET["order"];
//    echo $order . "<br>";
} else {
    $order = "";
}
if (isset ($_GET["function"])) {
    $func = $_GET["function"];
//    echo $function . "<br>";
} else {
    $func = "";
}

if ($func==""){
    echo "<form action=\"order.php" . "?MKZ="  . $MKZ . "&UPG=" . $UGP . "&function=" . $func . "&order=" . $order . "\" method=\"post\">";
}

function PrintCheckedOrderList($servername, $username, $password, $dbname, $orderlist){
    require_once('EAN13.php');
    $pdf=new PDF_EAN13('P', 'mm', array(105, 148));
//    echo "PrintCheckedOrderList for: " . $orderlist . "<br><br>";
    $orders = explode(";", $orderlist);
    foreach ($orders as $order){
        if ($order != "") {
            $order_db = GetOrder($order);
            $orderlines = GetOrderLine($order_db->entity_id, $order);
            CreatePDFFile($servername, $username, $password, $dbname, $order_db, $orderlines, false, $pdf);
        }
    }

    $nomFacture = getcwd() . "/upload/Reservierungen_checked_orders.pdf";
    $pdf->Output($nomFacture);
    echo "Reservierungszettel f&uuml;r <a href=\"" . "upload/Reservierungen_checked_orders.pdf" . "\">" .
        "alle angekreuzten Bestellunen</a> drucken.<br><br>";

    foreach ($orders as $order){
        if ($order != "") {
            $order_db = GetOrder($order);
            $orderlines = GetOrderLine($order_db->entity_id, $order);
            CreatePDFFile($servername, $username, $password, $dbname, $order_db, $orderlines, true, null);
        }
    }

    echo "<b>zum beenden bitte Tab schliessen</b>";
    exit;
}

// die Bestellungen aus magento holen

function RefreshOrders ($datum, $zeit)
{
echo  $datum . "<br>";
$timestamp = strtotime($datum);
echo $timestamp . "<br>";
$datum = date("Y-m-d", $timestamp);
echo  $datum . "<br>";

// magento connect
    $servername = "localhost";
    $username = "hififabrik_mag";
    $password = "Hf54mC74slRw";
    $dbname = "hififabrik_mag";
// import orders
    $orders = ImportOrders($servername, $username, $password, $dbname, $datum, $zeit);
// hifi connect
    $servername = "localhost";
    $username = "hififabrik_int";
    $password = "Hf54mC74slRw";
    $dbname = "hififabrik_intern";

    PutOrdersInDB($servername, $username, $password, $dbname, $orders);

    DeleteClosedOrders($servername, $username, $password, $dbname);

    ModifyCompleteOrdersToPrinted ($servername, $username, $password, $dbname);
}

$servername = "localhost";
$username = "hififabrik_int";
$password = "Hf54mC74slRw";
$dbname = "hififabrik_intern";

if ($func == "PrintChecked"){
    $orders = "KEINE Bestellung ausgewäht";
    if (isset($_GET['orders'])){
        $orders = $_GET['orders'];
    }

    if (isset ($_POST['orders'])){
        $orders = $_POST['orders'];
    }
    PrintCheckedOrderList($servername, $username, $password, $dbname, $orders);
    exit;
}


$datum = $_POST["selDate"];
$time = $_POST["selTime"];

//if (($datum != "") AND ($time != "") AND ($func == "GetMagOrders")){
if (($datum != "") AND ($time != "")){
    RefreshOrders ($datum, $time);
    header("Location: order.php?MKZ=" . $MKZ . "&UGP=" . $UGP ."&function=" . $func);
}

$servername = "localhost";
$username = "hififabrik_int";
$password = "Hf54mC74slRw";
$dbname = "hififabrik_intern";

if ($order == ""){
    $orders = GetTheOrderlist($servername, $username, $password, $dbname);
    echo CreateOrderTableHead($MKZ, $UGP);
    echo CreateOrderTable($orders, $MKZ, $UGP);
} else {
    if ($func == "UpdateOrder"){
        echo $func . " " . $order . " " . $bemerkung;
    }
    if ($func == "PrintChecked"){
        foreach($_POST['orders'] as $string) {
            $where .= $string.",";
        }
        $orders = substr($where,0,-1);

echo "huhu";
echo $orders;
print_r($orders);

//        $order_db = GetOrder($order);
//        $orderlines = GetOrderLine($order_db->entity_id, $order);


    }

    //echo $order;

    if ($func == "ShowTheOrder"){
        $order_db = GetOrder($order);
        $orderlines = GetOrderLine($order_db->entity_id, $order);
        echo CreateOrderHead($order_db, $MKZ, $UGP);
        echo CreateOrderArticle($orderlines, $MKZ, $UGP);

        $func = "PrintReservation";

    };
    if ($func == "PrintReservation") {
        $order_db = GetOrder($order);
        $orderlines = GetOrderLine($order_db->entity_id, $order);
        CreatePDFFile ($servername, $username, $password, $dbname, $order_db, $orderlines, true, null);
        echo "<a href=\"order.php\">zur&uuml;ck zur Bestell&uuml;bersicht</a>";
    }
}
if ($func==""){
    echo "</form><br>\n";
}

//echo "<br>order.php end<br>\n";
echo "<br></body><br>\n";
echo "<br></html><br>\n";

