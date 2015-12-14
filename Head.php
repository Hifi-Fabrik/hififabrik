<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <title>Internetpreiskontrolle</title>

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

echo "<table style=\"background-color:#F0F0F0\" width=\"1024\">\n\n";

echo "<tr>\n";
    echo "<th align=\"left\"><font face=\"sans-serif\" size=\"2\">";
        $h = "Hersteller";
        echo $h;
    echo "</font></th>\n";

    echo "<th align=\"center\" colspan=\"3\"><font face=\"sans-serif\" size=\"2\">";
        $h = "Suchlauf starten (Suchmaschine klicken)";
        echo $h;
    echo "</font></th>\n";

    echo "<th align=\"center\"><font face=\"sans-serif\" size=\"2\">";
        $h = "Ergebniss(e)";
        echo $h;
    echo "</font></th>\n";

echo "</tr>\n\n";

echo "<tr>\n";

$datei = file("hifi_aktuell_neu_2015_09.txt");
$merchants = array();
foreach ($datei AS $ausgabe) {
    $zerlegen = explode(chr(9), $ausgabe);
        $merchant = explode(' ', $zerlegen[0]);
    $p = strcmp($merchant[0], "Artikel");
    if (($p != 0) & (!(in_array ($merchant[0], $merchants)))){
        $merchants[] = $merchant[0];
    }
};

echo "<td valign=\"middle\">";

    echo "<select name=\"Merchant\">\n";
        echo "<option value='*alle*' selected>*alle*</option>\n";
        foreach ($merchants as $merch){
            echo "<option value='" . $merch . "'>" . $merch . "</option>\n";
        }
    echo "</select>\n";

    echo "</td>\n\n";

    echo "<td width=\"100\" align=\"center\">";
        echo "<input type=\"button\" style=\"width: 100px\" value=\"PSM\"/>";
    echo "</td>\n";

    echo "<td width=\"100\" align=\"center\">";
        echo "<input type=\"button\" style=\"width: 100px\" value=\"IDEALO\"/>";
    echo "</td>\n";

    echo "<td width=\"100\" align=\"center\">";
        echo "<input type=\"button\"  style=\"width: 100px\" value=\"E-Bay\"/>";
    echo "</td>\n";

    echo "<td width=\"100\" align=\"center\">";
        echo "<input type=\"button\"  style=\"width: 100px\" value=\"anzeigen\"/>";
    echo "</td>\n";

echo "</tr>\n\n";

echo "</table>\n";

?>


</body>
</html>