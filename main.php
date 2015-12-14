<?php
/**
 * Created by PhpStorm.
 * User: Rudi
 * Date: 12.10.2015
 * Time: 11:34
 */

require_once('database.php');

echo "main.html started";

//GetMKZandUGP ($MKZ, $UGP, $order, $function);
//echo $MKZ .  " " .$UGP;

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
    echo $order . "<br>";
} else {
    $order = "";
}
if (isset ($_GET["function"])) {
    $function = $_GET["function"];
    echo $function . "<br>";
} else {
    $function = "";
}

$UGP = "admin";

if (($MKZ != "") AND ($UGP != "")){
    if ($MKZ == 'order'){
        header("Location: order.php?MKZ=" . $MKZ . "&UGP=" . $UGP);
    }
    if ($MKZ == 'lager'){
        header("Location: lager.php?MKZ=" . $MKZ . "&UGP=" . $UGP);
    }
    if ($MKZ == 'preissuche'){
        if(($UGP == 'admin') OR ($UGP == 'master')){
            header("Location: psm.php?MKZ=" . $MKZ . "&UGP=" . $UGP);
        }

    }
    if ($MKZ == 'admin'){
        if(($UGP == 'admin')){
            header("Location: admin.php?MKZ=" . $MKZ . "&UGP=" . $UGP);
        }

    }
}

?>
