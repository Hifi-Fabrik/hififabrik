<?php
/**
 * Created by PhpStorm.
 * User: Rudi
 * Date: 05.10.2015
 * Time: 15:53
 */

require_once 'database.php';
require_once 'menue.php';

if (isset ($_GET["BKC"])) {
    $BKC = $_GET["BKC"];
    header("Location: http://www.hifi-fabrik.de/IPKweb/Lager_alt.php?BKC=" . $BKC . "");
    exit;
} else {
    $BKC = "";
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Warenbewegung buchen</title>
</head>
<body>

<form method="get">

    <table border="1" width="1050">
        <tr>
            <th colspan="3" height="100">
                HiFi-Fabrik Lagerbestand erfassen
            </th>
        </tr>
        <tr>
            <th height="50" width="350" bgcolor="#7fff00">Warenzugang</th>
            <th height="50" width="350" bgcolor="#f5f5dc">Ware umlagern</th>
            <th height="50" width="350" bgcolor="#6495ed">Ware versenden</th>
        </tr>
    </table>
    <br>
    <br>

<?php
$servername = "localhost";
$username = "hififabrik_int";
$password = "Hf54mC74slRw";
$dbname = "hififabrik_intern";
if (CheckDataBaseReady ($servername, $username, $password, $dbname)){
} else {
    echo "<br>database NOT ready to use<br>\n";
}
//GetMKZandUGP($MKZ,$UGP);

$UGP = "admin";
//$items = LoadMenuItems ($servername, $username, $password, $dbname, $MKZ, $UGP);
$buchknz = LoadMenuItems($servername, $username, $password, $dbname, 'BUCHKZ', $UGP);

BuildMenuTable ($buchknz, 1050, 1, 3, 6, 50, 350, "center", array("#7fff0", "#f5f5dc", "#6495ed"));

?>


</form>
</body>
</html>

