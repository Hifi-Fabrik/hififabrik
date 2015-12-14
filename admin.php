<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Administrator Aufgaben</title>

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

</head>
<body>

<?php
/**
 * Created by PhpStorm.
 * User: Rudi
 * Date: 12.10.2015
 * Time: 12:44
 */

// debug step
//echo "admin.php start<br>\n";

// includes
require_once ('database.php');
require_once ('menue.php');
require_once ('getorder.php');

$servername = "localhost";
$username = "hififabrik_int";
$password = "Hf54mC74slRw";
$dbname = "hififabrik_intern";

if (CheckDataBaseReady ($servername, $username, $password, $dbname)){
    echo "<br>database ready to use<br>\n";
} else {
    echo "<br>database NOT ready to use<br>\n";
}
echo "admin.php start<br>\n";

GetMKZandUGP($MKZ,$UGP);
echo "admin.php start<br>\n";

$items = LoadMenuItems ($servername, $username, $password, $dbname, $MKZ, $UGP);
$buchknz = LoadMenuItems($servername, $username, $password, $dbname, 'BUCHKZ', $UGP);

BuildMenuTable ($buchknz, 1050, 1, 3, 6, 50, 350, "center", array("#7fff0", "#f5f5dc", "#6495ed"));

$order = new mag_order();

echo "<br>admin.php end<br>\n";
echo "<br></body><br>\n";
echo "<br></html><br>\n";

echo "vor require<br>\n";
require('fpdf.php');
echo "vor new pdf<br>\n";


//$orientation='P', $unit='mm', $size='A4'

$pdf = new FPDF('P', 'mm', array(105, 148));
echo "vor add page<br>\n";
$pdf->AddPage();
echo "vor setfonte<br>\n";
$pdf->SetFont('Helvetica','B',16);
echo "vor cell<br>\n";
$pdf->Cell(40,10,'Hello World!');
$pdf->AddPage();
echo "vor setfonte<br>\n";
$pdf->SetFont('Helvetica','B',16);
echo "vor cell<br>\n";
$pdf->Cell(40,10,'Hello World! page2');
echo "vor output<br>\n";

$nomFacture = getcwd()."/upload/facture_". "test" . ".pdf";

echo getcwd() . "<br>";
echo $nomFacture . "<br>";

$pdf->Output($nomFacture);
echo "end pdf<br>\n";


// magento connect
$servername = "localhost";
$username = "hififabrik_mag";
$password = "Hf54mC74slRw";
$dbname = "hififabrik_mag";
// import orders
$orders = ImportOrders ($servername, $username, $password, $dbname, '2015-10-22', '14:00:00');
// hifi connect
$servername = "localhost";
$username = "hififabrik_int";
$password = "Hf54mC74slRw";
$dbname = "hififabrik_intern";

PutOrdersInDB($servername, $username, $password, $dbname, $orders);

?>
