<?php
if (isset ($_GET["BKC"])) {
    $BKC = $_GET["BKC"];
    echo $BKZ;
//    header("Location: http://www.hifi-fabrik.de/IPKweb/Lager_alt.php?BKC=" . $BKC . "");
//    exit;
} else {
    $BKC = "";
}

if (isset ($_GET["EAN"])) {
    $EAN = $_GET["EAN"];
    echo $EAN;
//    header("Location: http://www.hifi-fabrik.de/IPKweb/Lager_alt.php?BKC=" . $BKC . "");
//    exit;
} else {
    $EAN = "";
}
echo $BKZ . " - ". $EAN;
?>

<html>
<head>
    <title> Lagerbestandsverwaltung</title>
</head>

<body>

<form method ="GET" enctype="multipart/form-data" action="">

<?php
echo "<br>EANCode :<br>\n";
echo "<input name=\"EAN\" type=\"text\" title=\"Barcode\" autofocus /><br><br>\n\n";
echo "Anzahl :<br>\n";
echo "<input name=\"ANZ\" type=\"number\" title=\"Anzahl\" value='1'/><br><br>\n\n";
switch($BKC) {
    case ("WEOVP"):
        echo "Ware OVP vom Hersteller ins Lage";
        break;
    case ("WUOVPRES"):
        echo "Ware OVP reservieren";
        break;
    case ("WACUSRES"):
        echo "Ware an Kunde versenden MIT Reservierung";
        break;
    case ("WEREPSER"):
        echo "Ware aus Reparatur an Service";
        break;
    case ("WUBWARES"):
        echo "Ware aus B-Ware reservieren";
        break;
    case ("WACUSDIR"):
        echo "Ware an Kunde versenden OHNE Reservierung";
        break;
    case ("WESTOOVP"):
        echo "Ware aus Storno in OVP Lager (unge&ouml;ffnet)";
        break;
    case ("WUAUSRES"):
        echo "Ware aus Ausstellung reservieren";
        break;
    case ("WALEIOVP"):
        echo "Leihstellung an Kunden aus OVP-Lager";
        break;
    case ("WESTOBWA"):
        echo "Ware aus Storno ins B-Warenlager (ge&ouml;ffnet)";
        break;
    case ("WURESOVP"):
        echo "Ware aus Reservierung in OVP-Lager";
        break;
    case ("WALEIBWA"):
        echo "Leihstellung an Kunden aus B-Waren Lager";
        break;
    case ("WESTOAUS"):
        echo "Ware aus Storno in Ausstellungsraum";
        break;
    case ("WURESBWA"):
        echo "Ware aus Reservierung in B-Waren Lager";
        break;
    case ("WALEIAUS"):
        echo "Leihstellung an Kunden aus Ausstellung";
        break;
    case ("WUREAUS"):
        echo "Ware aus Reservierung in Ausstellung";
        break;
}

echo $EAN;

echo "<br><br>\n\n";
?>

    <!--  <input type="submit" value="verbuchen" name="Lager_alt.php?BKZ=<?php echo $BKZ ?>;EAN=<?php echo $EAN ?>" />
    -->
</form>
</body>
</html>
