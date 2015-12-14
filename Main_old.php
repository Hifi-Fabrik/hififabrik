<html xmlns="http://www.w3.org/1999/html">
<head>
    <title>Artikelstamm laden</title>

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
<form method ="POST" enctype="multipart/form-data">

<?php

$servername = "localhost";
$username = "hififabrik_mag";
$password = "Hf54mC74slRw";
$dbname = "test";
/*
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("<BR>" . "Connection failed: " . $conn->connect_error);
}

$sql = "DROP TABLE Artikel";
if ($conn->query($sql) === TRUE) {
    //echo "<BR>" . "Table Artikel dropped successfully";
} else {
    echo "<BR>" . "Error: " . $sql . "<br>" . $conn->error;
}

$sql = "CREATE TABLE Artikel (Artikelname varchar(512) PRIMARY KEY,
                              Netzpreis DECIMAL(8,2),
                              EANnummer varchar(20),
                              genDate TIMESTAMP)";
if ($conn->query($sql) === TRUE) {
    //echo "<BR>" . "Table Artikel created successfully";
} else {
    echo "<BR>" . "Error: " . $sql . "<br>" . $conn->error;
}
*/

//// dateiliste aus directory
//$thelist = array();
//if ($handle = opendir("/ipkweb")) {
//    while (false !== ($file = readdir($handle))) {
//        if (($file != ".") && ($file != "..") && (strtolower(substr($file, strrpos($file, '.') + 1)) == 'txt')) {
//            $thelist[]  = '<p class="sans-serif" <li><a href="'.$file.'">'.$file.'</a></li></p>';
//        }
//    }
//    closedir($handle);
//    rsort ($thelist);
//    foreach ($thelist as $value) {
//        echo "$value\n";
//    }
//}

//$merchants = array();
//foreach ($datei AS $ausgabe) {
//    $zerlegen = explode(chr(9), $ausgabe);
//    $merchant = explode(' ', $zerlegen[0]);
//    $p = strcmp($merchant[0], "Artikel");
//    if (($p != 0) & (!(in_array ($merchant[0], $merchants)))){
//        $merchants[] = $merchant[0];
//    }
//
//};

//echo "<select name=\"Merchant\">";
//echo "<option value='*alle*' selected>*alle*</option>";
//foreach ($merchants as $merch){
//    echo "<option value='" . $merch . "'>" . $merch . "</option>\n";
//}
//echo "</select><br><br>\n\n";

$hint= "\"This is Title and title&#013and title and title\"";

$datei = file("hifi_aktuell_neu_2015_09.txt");

echo "<table style=\"background-color:#F0F0F0\" width=\"1024\">";

foreach($datei AS $ausgabe)
    {
        echo "<tr>";

        $zerlegen = explode(chr(9), $ausgabe);
        $Artikel = $zerlegen[0];
        $EANnumber = $zerlegen[2];
        $NetPrice = $zerlegen[1];
        $p = strcmp($zerlegen[1], "Netzpreis");
        if ($p != 0){
            $NetPrice = SubStr($zerlegen[1], 0, StrPos($zerlegen[1], '�') - 1);
//            $sql = "REPLACE INTO Artikel (Artikelname, Netzpreis, EANnummer) Values('$Artikel', '$NetPrice', '$EANnumber')";
//            if ($conn->query($sql) === TRUE) {
//                //echo "<BR>" . "New record created successfully";
//            } else {
//                echo "<BR>" . "Error: " . $sql . "<br>" . $conn->error;
//            }
        }
        if ($p!= 0) {
            echo "<td title=$hint align=\"left\"><font face=\"sans-serif\" size=\"2\">$Artikel</font></td>\n
                                      <td align=\"right\"><font face=\"sans-serif\" size=\"2\">$NetPrice</font></td>
                                      <td align=\"right\"><font face=\"sans-serif\" size=\"2\">$EANnumber</font></td>";
        }
        else {
            echo "<th title=$hint align=\"left\"><font face=\"sans-serif\" size=\"2\">$Artikel</font></th>\n
                                      <th align=\"right\"><font face=\"sans-serif\" size=\"2\">$NetPrice</font></th>
                                      <th align=\"right\"><font face=\"sans-serif\" size=\"2\">$EANnumber</font></th>";
        }
        if ($p != 0){
            $QStrIdealo  = "<a target=\"_blank\" href=\"http://www.idealo.de/preisvergleich/MainSearchProductCategory.html?q=" . $EANnumber . "\">";
            $QStrPSM     = "<a target=\"_blank\" href=\"http://www.preissuchmaschine.de/preisvergleich/produkt.cgi?suche=" . $EANnumber . "\">";
            $QStrEBAY    = "<a target=\"_blank\" href=\"http://www.ebay.de/sch/i.html?_nkw=" . $EANnumber . "&_rdc=1" . "\">";
            if ($EANnumber == ""){
                $QStrIdealo  = "<a target=\"_blank\" href=\"http://www.idealo.de/preisvergleich/MainSearchProductCategory.html?q=" . $Artikel . "\">";
                $QStrPSM     = "<a target=\"_blank\" href=\"http://www.preissuchmaschine.de/preisvergleich/produkt.cgi?suche=" . $Artikel . "\">";
                $QStrEBAY    = "<a target=\"_blank\" href=\"http://www.ebay.de/sch/i.html?_nkw=" . $Artikel . "&_rdc=1" . "\">";
            }
            echo "<td width=\"80\" align=\"center\">" . $QStrPSM . "<input type=\"button\" style=\"width: 80px\" value=\"PSM\"/></a></td>
                  <td width=\"80\" align=\"center\">" . $QStrIdealo . "<input type=\"button\" style=\"width: 80px\" value=\"IDEALO\"/></td>
                  <td width=\"80\" align=\"center\">" . $QStrEBAY . "<input type=\"button\" style=\"width: 80px\" value=\"EBAY\"/></td>
                  <td width=\"80\" align=\"center\"><input type=\"button\" style=\"width: 80px\" value=\"KOMBI\"/></td>";
         }
        else {
            echo "<th width=\"80\" align=\"center\"><font face=\"sans-serif\" size=\"2\">PSM</a></th>
                  <th width=\"80\" align=\"center\"><font face=\"sans-serif\" size=\"2\">IDEALO</th>
                  <th width=\"80\" align=\"center\"><font face=\"sans-serif\" size=\"2\">EBAY</th>
                  <th width=\"80\" align=\"center\"><font face=\"sans-serif\" size=\"2\">KOMBI</th>";
        }
        echo "</tr>";

    }

//$conn->close();

echo "</table>";
?>

</form>

</body>
</html>

