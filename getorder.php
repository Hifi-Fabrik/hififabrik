<?php
/**
 * Created by PhpStorm.
 * User: Rudi
 * Date: 19.10.2015
 * Time: 13:03
 */

function GetStatusInfo($sw){
    $si = "N/A Status not found: " . $sw;
    switch($sw){
        case "imported":
            $si = "frisch vom Magento-Shop";
            break;
        case "printed":
            $si = "Reservierungszettel gedruckt";
            break;
        case "res_booked":
            $si = "Reservierung gebucht";
            break;
        case "ver_booked":
            $si = "Versand gebucht";
            break;
        case "ver_storno":
            $si = "Versand storniert";
            break;
        case "res_storno":
            $si = "Reservierung aufgelöst";
            break;
    }
    return $si;
}

function GetLaenge($po){
    $res = "";
    $strs = explode ('s:6:', $po);
    foreach ($strs as $str){
        $rst = strstr ($str, '";s:5:', false);
        $txt = strstr ($rst, '"value";s:5:"', false);
        $rst = substr($txt, 13);
        $txt = strstr ($rst, ";s:11:", true);
        if ($txt != "") {
            $rst = substr($txt,0,strlen($txt) - 1);
            $res = $rst;
        }
    }
    return $res;
}

function GetPayMethod($sw){
    $pm = "paymethod not found: " . $sw;
    switch($sw){
        case "banktransfer":
            $pm = "Bank&uuml;berweisung";
            break;
        case "cashondelivery":
            $pm = "Nachnahme";
            break;
        case "purchaseorder":
            $pm = "Rechnung";
            break;
        case "paypal_express":
            $pm = "PayPal Express";
            break;
        case "paypal_standard":
            $pm = "PayPal Standard";
            break;
        case "checkmo":
            $pm = "Bar-/EC Zahlung bei Abholung";
            break;
        case "m2epropayment":
            $pm = "m2epropayment checkmo ????";
            break;
        case "iways_paypalplus_payment":
            $pm = "PayPal";
            break;
    }
    return $pm;
}

function GetPayMethodASCII($sw){
    $pm = "paymethod not found: " . $sw;
    switch($sw){
        case "banktransfer":
            $pm = "Bankueberweisung";
            break;
        case "cashondelivery":
            $pm = "Nachnahme";
            break;
        case "purchaseorder":
            $pm = "Rechnung";
            break;
        case "paypal_express":
            $pm = "PayPal Express";
            break;
        case "paypal_standard":
            $pm = "PayPal Standard";
            break;
        case "checkmo":
            $pm = "Bar-/EC Zahlung bei Abholung";
            break;
        case "m2epropayment":
            $pm = "m2epropayment checkmo ????";
            break;
        case "iways_paypalplus_payment":
            $pm = "PayPal";
            break;
    }
    return $pm;
}

function GetPackStueck ($sw){
    $ae = "N/A: " . $sw;
    switch($sw) {
        case 94:
            $ae = 1;
            break;
        case 93:
            $ae = 2;
            break;
    }
    return $ae;
}

function GetEinheit ($sw, $vpe){
    $ae = "N/A: " . $sw;
    if ($vpe == "lfdm"){
        $ae = "Meter";
    } else {
        switch($sw) {
            case 81:
                $ae = "St&uuml;ck";
                break;
            case 80:
                $ae = "Paar";
                break;
            case 79:
                $ae = "Set";
                break;
        }
    }
    return $ae;
}

function GetEinheitASCII ($sw, $vpe){
    $ae = "N/A: " . $sw;
    if ($vpe == "lfdm"){
        $ae = "Meter";
    } else {
        switch($sw) {
            case 81:
                $ae = "Stueck";
                break;
            case 80:
                $ae = "Paar";
                break;
            case 79:
                $ae = "Set";
                break;
        }
    }
    return $ae;
}

function GetVPE ($sw){
    $ae = "N/A: " . $sw;
    switch($sw) {
        case 84:
            $ae = "2";
            break;
        case 85:
            $ae = "1";
            break;
        case 95:
            $ae = "lfdm";
            break;
    }
    return $ae;
}

function GetDataError($line){
    $error = "";
    $err = [];
    if (empty($line->einheit) || (!stristr(GetEinheit($line->einheit, $line->vpe), 'N/A') === false)){
        $err[] = "Einheit";
    }
    if (empty(GetVPE($line->vpe)) || (!stristr(GetVPE($line->vpe), 'N/A') === false)){
        $err[] = "Verpackungseinheit";
    }
    if (empty(GetPackStueck($line->packstueck)) || (!stristr(GetPackStueck($line->packstueck), 'N/A') === false)){
        $err[] = "Packstück";
    }

    if (empty($line->product_lort)){
        $err[] = "Lagerort";
    }

    if (sizeof($err) > 0){
        $error = "Fehler im Artikel " . $line->sku . " ". implode(", ", $err);
    }

    return $error;
}

function CalculateOrderPages($qty_ordered, $einheit, $vpe, $packstueck){

    $anzahl = $qty_ordered;
    if ($vpe != "" and $vpe != "lfdm"){
        $anzahl = ceil ($anzahl / $vpe);
    }
    $anzahl = $anzahl * $packstueck;
    return $anzahl;
}

function CreatePDFFile($servername, $username, $password, $dbname, $order, $orderlines, $singleorder, $pdf){
    require_once('EAN13.php');

    if ($singleorder == true) {
        $pdf = new PDF_EAN13('P', 'mm', array(105, 148));
    }

    $AnzRes = 0;
    $data_error = false;
    foreach ($orderlines as $line) {
        if ($line->sku != '101') {
            if (GetVPE($line->vpe) == "lfdm"){
                $AnzRes = $AnzRes + 1;
            } else {
                $AnzRes = $AnzRes + CalculateOrderPages($line->qty_ordered, GetEinheit($line->einheit, GetVPE($line->vpe)),
                        GetVPE($line->vpe), GetPackStueck($line->packstueck));
            }
            if($line->data_error != ""){
                $data_error = true;
            }
        }
    }

    if (!$data_error) {
        $page = 0;
        foreach ($orderlines as $orderarticle) {
            $Anz = 1;
            if (GetVPE($orderarticle->vpe) != "lfdm") {
                $Anz = CalculateOrderPages($orderarticle->qty_ordered, GetEinheit($orderarticle->einheit, GetVPE($orderarticle->vpe)),
                    GetVPE($orderarticle->vpe), GetPackStueck($orderarticle->packstueck));
            }
            for ($i = 1; $i <= $Anz; $i++) {
                $page = $page + 1;
                $pdf->AddPage();
                $pdf->SetFont('Helvetica', 'B', 18);

                $po = "";
                if ($orderarticle->product_options != "") {
                    $po = GetLaenge($orderarticle->product_options);
                }
// jetzt gehts  3 zeilig, keine kürzung mehr notwendig
                $orderarticle->name = substr($orderarticle->name, 0, 45);
                $pdf->MultiCell(80, 6, $orderarticle->name, 0, 'L');

                $pdf->SetFont('Helvetica', 'B', 12);
                $pm = GetPayMethodASCII($order->paymethod);

                $bem = "";
                if ($orderarticle->qty_ordered <= GetVPE($orderarticle->vpe)) {
                    $txt = round($orderarticle->qty_ordered);
                    if ($orderarticle->qty_ordered < GetVPE($orderarticle->vpe)){
                        $bem = "ACHTUNG:\n" . round($orderarticle->qty_ordered) . " " . GetEinheitASCII($orderarticle->einheit, GetVPE($orderarticle->vpe)) . " !!!";
                    };
                } else {
                    $txt = round(GetVPE($orderarticle->vpe));
                    $orderarticle->qty_ordered = $orderarticle->qty_ordered - GetVPE($orderarticle->vpe);
                }

                $txt = $txt . " " . GetEinheitASCII($orderarticle->einheit, GetVPE($orderarticle->vpe));
                $txt = $txt . " (Art.Nr.: " . $orderarticle->sku . ") Packstueck(e): ";
                if (GetPackStueck($orderarticle->packstueck) > 1) {
                    $txt = $txt . $i . "/" . GetPackStueck($orderarticle->packstueck);
                } else {
                    $txt = $txt . GetPackStueck($orderarticle->packstueck);
                };
                $pdf->Text(10, 35, $txt, 0, 1, 'L');

                $txt = "";
//            if (GetLaenge($orderarticle->product_options) <> "") {
//                $txt = $txt . "Laenge: " . GetLaenge($orderarticle->product_options) . "  ";
//            }
                if ($orderarticle->product_quotes <> "") {
                    $txt = $txt . $orderarticle->product_quotes . "  ";
                }
                if ($orderarticle->product_ltxt <> "") {
                    $txt = $txt . "Zustand: " . $orderarticle->product_ltxt;
                }
                $pdf->SetFont('Helvetica', 'B', 10);
                $pdf->Text(10, 40, $txt, 0, 1, 'L');
// bemerkung aus Auftrag Artikel !!!
                if ($order->bemerkung != ""){
                    $pdf->SetXY(55, 73);
                    $pdf->SetFillColor(204, 204, 204);
                    $pdf->MultiCell(45, 5, $order->bemerkung, 1, 'R', true);
                    $pdf->SetFillColor(0, 0, 0);
                }
                if ($bem <> "") {
                    $pdf->SetXY(55, 40);
                    $pdf->SetFillColor(204, 204, 204);
                    $pdf->MultiCell(45, 5, $bem, 1, 'R', true);
                    $pdf->SetFillColor(0, 0, 0);
                }

                if ($order->country_id != "DE") {
                    $pdf->SetFont('Helvetica', 'B', 24);
                    $pdf->SetFillColor(204, 204, 204);
                    $pdf->SetXY(80, 85);
                    $pdf->MultiCell(20, 15, "DPD", 1, 'R', true);
                    $pdf->SetFillColor(0, 0, 0);
                    $pdf->SetFont('Helvetica', 'B', 10);
                }

                $pdf->Text(10, 75, $order->ba_lastname . ", " . $order->ba_firstname, 0, 1, 'L');

                $pdf->SetFont('Helvetica', '', 8);
                if ($orderarticle->product_base != "") {
                    $pdf->Text(10, 40, "(" . $orderarticle->product_base . ")");
                }
                $pdf->SetFont('Helvetica', '', 10);
                $txt = "";
                if (($order->lastname != $order->ba_lastname) OR ($order->firstname != $order->ba_firstname)) {
                    $txt = '(LiAdr: ' . $order->lastname . ", " . $order->firstname . ') ';
                }
                $txt = $txt . $pm;

                $pdf->Text(10, 80, $txt, 0, 1, 'L');

                $pdf->SetFont('Helvetica', '', 6);

                $ts = strtotime($order->order_date);

                $pdf->Text(10, 85, $order->order_number . " vom " . date("d.m.Y G:i:s", $ts), 0, 1, 'L');
                $pdf->Text(10, 88, $order->firstname . " " . $order->lastname, 0, 1, 'L');
                $pdf->Text(10, 91, $order->street, 0, 1, 'L');

                $city = "";
                if ($order->country_id != "DE") {
                    $city = $order->country_id . "-";
                }
                $city = $city . $order->postcode . " " . $order->city;
                $pdf->Text(10, 94, $city, 0, 1, 'L');

                $pdf->SetFont('Helvetica', 'I', 6);

                $space = 0;
                if (sizeof($orderlines) > 1) {
                    $space = $space + 1;
                    $pdf->Text(10, 98, 'weitere Artikel:', 0, 1, 'L');
                    $space = $space + 3;
                    for ($j = 0; $j < sizeof($orderlines); $j++) {
                        if ($orderlines[$j]->sku != $orderarticle->sku) {
                            $qtt = round($orderlines[$j]->qty_ordered);
                            $pdf->Text(10, 98 + $space, $qtt . " " . GetEinheitASCII($orderlines[$j]->einheit, GetVPE($orderlines[$j]->vpe)) . " " . $orderlines[$j]->name, 0, 1, 'L');
                            $space = $space + 3;
                        }
                    }
                }

                $pdf->SetFont('Helvetica', 'B', 10);
                $pdf->Text(80, 140, "Seite " . $page . " / " . $AnzRes, 0, 1, 'R');

                $pdf->EAN13(10, 55, $orderarticle->ean, 8, .3);
                $pdf->Text(45, 60, "<= ARTIKELNUMMER");
                $txt = str_pad($order->order_number, 12, '0', STR_PAD_LEFT);
                $pdf->EAN13(10, 130, $txt, 8, .3);
                $pdf->Text(45, 135, "<= Auftragsnummer");
            }
        }

        if ($singleorder == true) {
            $nomFacture = getcwd() . "/upload/Reservierungen_" . $order->order_number . ".pdf";
            $pdf->Output($nomFacture);
            echo "<span style=\"font-size: 12pt;color:#000000;\" >Reservierungszettel f&uuml;r Bestellung <a href=\"upload/Reservierungen_" . $order->order_number . ".pdf" . "\">" .
                $order->order_number . "</a> drucken.</spawn><br><br>";
        }

        if (db_exists($servername, $username, $password, $dbname)) {
// Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            $sql = "UPDATE `orders` SET status=\"printed\" WHERE increment_id like '" . $order->order_number . "'";
            MakeOrderHistoryEntry ($conn, $order->order_number, "RESPRINTED", "Reservierungszettel wurden gedruckt.");
            $result = $conn->query($sql);
        }
    } else {
        echo "<span style=\"font-size: 12pt;color:#ff0000;\"  >Bestellung <a href=\"order.php" . "?MKZ="  . $MKZ . "&UPG=" . $UGP . "&order=". $order->order_number . "&function=ShowTheOrder\">" . $order->order_number . "</a> hat Datenfehler. Bittes zuerst korrigieren</spawn><br><br>";
    }
}

function GetUserInfo(){
    SESSION_START();
    $userinfo="<div style=\"font-family: Arial; font-size: 12px; \"><br>aktueller Benutzer: <b>";
    $userinfo=$userinfo . $_SESSION["user"]["user_vorname"] . " ";
    $userinfo=$userinfo . $_SESSION["user"]["user_nachname"] . "</b></div><div style=\"font-family: Arial; font-size: 10px; \"> (letzte Anmeldung: ";
    $userinfo=$userinfo . $_SESSION["user"]["lastlogin"];
    $userinfo=$userinfo . ")<br><br></div>";
    return $userinfo;
}

function CreateOrderHead($order, $MKZ, $UGP){

    SESSION_START();

    $userinfo = GetUserInfo();

    $ts = strtotime($order->order_date);
    $html = "<div style=\"font-family: Arial; font-size: 14px; \">";
    $html = $html . "Bestellung <b>" . $order->order_number . "</b> vom <b>" . date("d.m.Y G:i:s", $ts) . "</b><br>";
    $html = $html . "Besteller: <b>" . $order->ba_firstname . " " . $order->ba_lastname . "</b><br><br>\n";
    $html = $html . "Status: <b>" . $order->status . "</b><br><br>\n";
    $html = $html . "Lieferadresse:<br>\n";
    $html = $html . $order->firstname . " " . $order->lastname . "<br>\n";
    $html = $html . $order->street . "<br>\n";
    $html = $html . $order->country_id . "-" . $order->postcode . " " . $order->city . "<br><br>\n";

    if (($order->email != "") or ($order->telephone != "" )) {

        $html = $html . "<table width=\"200\" cellpadding=\"1\">\n";
        $html = $html . "<style type=\"text/css\">\n";
        $html = $html . "table { width: 200px; }\n";
        $html = $html . "table { border-collapse: collapse;}\n";
        $html = $html . "table, td, th { border: 1px solid black; }\n";
        $html = $html . "td, th { height: 10px; font-family: Arial; font-Size: 12px};\n";
        $html = $html . "td, th { height: 10px; font-family: Arial; font-Size: 12px};\n";
        $html = $html . "th { text-align: right; };\n";
        $html = $html . "td { text-align: left; };\n";
        $html = $html . "</style>\n";

//        $html = $html. "<div id=\"txtHint\"><b>Person info will be listed here...</b></div>";

        if ($order->email != "") {
            $html = $html . "<tr><td width=\"10%\">E-Mail</td><td width=\"20%\">" . $order->email . "</td><td width=\"60%\"></td></tr>";
        };

        if ($order->telephone != "") {
            $html = $html . "<tr><td width=\"10%\">Telefon</td><td width=\"20%\">" . $order->telephone . "</td><td width=\"60%\"></td></tr>";
        };
        $html = $html . "</table><br>\n";

    }
    $html = $html . "Zahlungsweise: <p style=\"font-family: Arial; font-size:18px;\" ><b>" . GetPayMethod($order->paymethod) . "</b></p>\n";
    $html = $html ."</div>";
    return $userinfo . $html;
}

function CreateOrderArticle($oarts, $MKZ, $UGP, $ShowHistory){
    $html = "<i><b>bestellte(r) Artikel:</b></i><br>\n";
    $html = $html . "<table id=\"Artikel\" cellpadding=\"1\">\n";
    $html = $html . "<style type=\"text/css\">\n";
    $html = $html . "table { width: 1200px; }\n";
    $html = $html . "table { border-collapse: collapse;}\n";
    $html = $html . "table, td, th { border: 1px solid black; }\n";
    $html = $html . "td, th { height: 10px; font-family: Arial; font-Size: 12px};\n";
    $html = $html . "th { text-align: right; };\n";
    $html = $html . "td { text-align: left; };\n";
    $html = $html . "td { style=\"white-space:nowrap;\" };\n";
    $html = $html . "</style>\n\n";
    $html = $html . "<tr>";
    $html = $html . "<td>Anzahl</td>";
    $html = $html . "<td>Einheit</td>";
    $html = $html . "<td>L&auml;nge</td>";
    $html = $html . "<td>Farbe</td>";
    $html = $html . "<td>VPE</td>";
    $html = $html . "<td>Packst&uuml;ck</td>";
    $html = $html . "<td>Artikel</td>";
    $html = $html . "<td>Bezeichnung</td>";
    $html = $html . "<td>EAN-Nummer</td>";
    $html = $html . "<td>Set Artikel</td>";
    $html = $html . "<td>Lagerort</td>";
    $html = $html . "<td>Barcode</td>";
    $html = $html . "</tr>\n\n";

    $id = 0;
    $lcol = "#ffffff";
    foreach ($oarts as $oart){
        $id = $id + 1;
        $html = $html . "<tr id=row".$id." BGCOLOR=\"".$lcol."\">\n";
        $html = $html . "<td align='right'>" . round($oart->qty_ordered) . "</td>\n";
        $html = $html . "<td id=einheit".$id." align='right'>" . GetEinheit($oart->einheit, GetVPE($oart->vpe)) . "</td>\n";
        $html = $html . "<td align='right'>" . GetLaenge ($oart->product_options) . "</td>\n";
        $html = $html . "<td align='left'>" . $oart->product_quotes . "</td>\n";
        $html = $html . "<td id=vpe".$id." align='right'>" . GetVPE($oart->vpe) . "</td>\n";
        $html = $html . "<td align='right'>" . GetPackStueck ($oart->packstueck) . "</td>\n";
        $html = $html . "<td id=sku".$id." align='left'>" . $oart->sku . "</td>\n";
        $html = $html . "<td id=name".$id." align='left'>" . $oart->name . "</td>\n";
        $html = $html . "<td id=EAN".$id." align='left' >" . $oart->ean . "</td>\n";
        $html = $html . "<td align='left'>" . $oart->product_base . "</td>\n";
        $html = $html . "<td id=ltext".$id." align='left'>" . $oart->product_ltxt . "</td>\n";
        $html = $html . "<td id=SCAN".$id." align='left'><input type=\"text\" name=\"SCAN".$id."\" onchange=\"eanchanged(this.name, this.value)\"></td>\n";
        $html = $html . "</tr>\n";
    }
    $html = $html . "</table><br>\n";
//    $html = $html . "<a href=\"order.php?MKZ=" . $MKZ . "&UPG=" . $UGP .  "&order=" . $oart->order_number . "&function=PrintReservation\"><button style=\"width:200px\">Reservierung drucken</button></a><br>\n";
//    $html = $html . "<button style=\"width:200px\">Reservierung buchen</button><br>\n";

    $htmlh = "";
    if ($ShowHistory === true){
        $hists = GetOrderHistory ('orders_history', 'order_increment_id', $oarts[0]->order_number, $oarts[0]->order_number);
        $htmlh = CreateOrderHistory ($hists);
    }

    return $html . $htmlh;
}

function CreateOrderHistory ($histories)
{
    $html = "<i><b>Historie der Bestellung:</b></i><br>\n";
    if ($histories != null) {
        $html = $html . "<table>";
        $html = $html . "<style type=\"text/css\">\n";
        $html = $html . "table { width: 1200px; }\n";
        $html = $html . "table { border-collapse: collapse;}\n";
        $html = $html . "table, td, th { border: 1px solid black; }\n";
        $html = $html . "td, th { height: 10px; font-family: Arial; font-Size: 12px};\n";
        $html = $html . "th { text-align: right; };\n";
        $html = $html . "td { text-align: left; };\n";
        $html = $html . "td { style=\"white-space:nowrap;\" };\n";
        $html = $html . "</style>\n\n";

        $html = $html . "<tr>";
        $html = $html . "<td style=\"width: 80px; \">Bestellung</td>";
        $html = $html . "<td style=\"width: 100px; \">Status</td>";
        $html = $html . "<td style=\"width: 250px; \">Bemerkung</td>";
        $html = $html . "<td>Notiz</td>";
        $html = $html . "<td style=\"width: 50px; \">Benutzer</td>";
        $html = $html . "<td style=\"width: 120px; \">durchgeführt am</td>";
        $html = $html . "</tr>\n\n";

        foreach ($histories as $hist) {
            $html = $html . "<tr>\n";
            $html = $html . "<td  style=\"width: 80px; \">" . $hist->order_number . "</td>\n";
            $html = $html . "<td  style=\"width: 100px; \">" . $hist->order_activity . "</td>\n";
            $html = $html . "<td style=\"width: 250px; \">" . $hist->order_activity_text . "</td>\n";
            $html = $html . "<td><textarea rows=\"1\" cols=\"80\" >" . $hist->order_memo . "</textarea></td>\n";
            $html = $html . "<td style=\"width: 50px; \" align=\"center\" >" . $hist->book_user . "</td>\n";
            $html = $html . "<td style=\"width: 120px; \">" . $hist->book_date . "</td>\n";
            $html = $html . "</tr>\n";
        }
        $html = $html . "</table><br>\n";
    } else {
        $html = $html . "KEINE HISTORIE GEFUNDEN !!!<br><br>\n";
    }
    return $html;
};


function GetOrderHistory ($table_name, $keyfield, $keyvalue, $order_number)
{
    require_once ('database.php');

    $servername = "localhost";
    $username = "hififabrik_int";
    $password = "Hf54mC74slRw";
    $dbname = "hififabrik_intern";

    if (db_exists($servername, $username, $password, $dbname)) {
// Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        $sql = "SELECT * FROM " . $table_name . " WHERE " . $keyfield . " LIKE '" . $keyvalue . "'";
        $result = $conn->query($sql);
        $hists = array();
        if ($result->num_rows > 0) {
            // output data of each row
            while ($row = $result->fetch_assoc()) {
                $hist = new order_history();
                $hist->order_number = $row['order_increment_id'];
                $hist->order_activity = $row['order_activity'];
                $hist->order_activity_text = $row['order_activity_text'];
                $hist->order_memo = $row['order_memo'];
                $hist->book_user = $row['book_user'];
                $hist->book_date_first = $row['book_date_first'];
                $hist->book_date = $row['book_date'];
                $hists[] = $hist;
            }
        } else {
            $hists = null;
        }
    } else {
        die("Database not ready to use !!!");
    }
    return $hists;
}

function CreateOrderTableHead($MKZ, $UGP)
{
    $userinfo = GetUserInfo();

    $head = $userinfo;
    $head = $head . "<p style=\"font-family: Arial; font-size:18px;\" >Bestellungen aus Magento</p>\n";
    $timestamp = strtotime("-1 days");
    $head = $head . "<p style=\"font-family: Arial; font-size:12px;\" >ab Datum: \n";
    $head = $head . "<input type=\"text\" maxlength=\"10\" width=\"50\" name=\"selDate\" value=\"". date ("d.m.Y", $timestamp) ."\">";
    $timestamp = strtotime ("16:00:00");
    $head = $head . " Uhrzeit:";
    $head = $head . "<input type=\"text\" maxlength=\"10\" width=\"50\" name=\"selTime\" value=\"". date ("G:i:s", $timestamp) ."\"></p>\n";
    $ahref = "<a href=\"order.php?MKZ=" . $MKZ . "&UPG=" . $UGP . "&function=GetMagOrders\"><button style=\"width:400px\">Bestellungen aus dem Shop holen</button></a>";
    $head = $head . "<p style=\"font-family: Arial; font-size:10px;\" >". $ahref . "</p>";
    $ahref = "<a href=\"order.php?MKZ=" . $MKZ . "&UPG=" . $UGP . "&function=PrintAllReservations\"><button style=\"width:400px\">Reservierungen drucken f&uuml;r ALLE NEU importierten</button></a>";
    $head = $head . "<p style=\"font-family: Arial; font-size:10px;\" >". $ahref . "</p>";
    $aorder = "";
    $ahref = "<a href=\"order.php?MKZ=" . $MKZ . "&UPG=" . $UGP . "&function=PrintChecked\">";
    $ahref = $ahref . "<button onClick=\"PrintCheckedOrders()\" style=\"width:400px\">" .$aorder . "Reservierungen drucken f&uuml;r alle angekreuzten Bestellungen</button></a>";
    $head = $head . "<p style=\"font-family: Arial; font-size:10px;\" >". $ahref . "</p>";
    $head = $head . "<br>";
    return $head;
}

function CreateOrderTable($orders, $MKZ, $UGP){
    $table = "<table cellpadding=\"1\">\n";
    $table = $table . "<style type=\"text/css\">\n";
    $table = $table . "table { width: 1200px; }\n";
    $table = $table . "table { border-collapse: collapse;}\n";
    $table = $table . "table, td, th { border: 1px solid black; }\n";
    $table = $table . "td, th { height: 10px; font-family: Arial; font-Size: 12px};\n";
    $table = $table . "td, th { height: 10px; font-family: Arial; font-Size: 12px};\n";
    $table = $table . "th { text-align: right; };\n";
    $table = $table . "td { text-align: left; };\n";
    $table = $table . "</style>\n";
    $table = $table . "<tr>";
    $table = $table . "<th title=\"alle Bestellungen an-/abw&auml;hlen\" width='20px'><input type=\"checkbox\" name=\"auswahl\" onmousedown=\"isKeyPressed(this.value, 0, event)\" value=\"alle\"><br>\n";
    $table = $table . "</th>\n";
    $table = $table . "<th width='80px'><b>Bestellnummer</b>\n";
    $table = $table . "</th>\n";
    $table = $table . "<th width='40px'><b>Artikel</b>\n";
    $table = $table . "</th>\n";
    $table = $table . "<th width=\"120px\"><b>Datum</b>\n";
    $table = $table . "</th>\n";
    $table = $table . "<th width='100px'><b>Name</b>\n";
    $table = $table . "</th>\n";
    $table = $table . "<th width='80px'><b>Vorname</b>\n";
    $table = $table . "</th>\n";
    $table = $table . "<th width='50px'><b>S-Shop</b>\n";
    $table = $table . "</th>\n";
    $table = $table . "<th width='50px'><b>S-HiFi</b>\n";
    $table = $table . "</th>\n";
    $table = $table . "<th width='100px'><b>Versandweg</b>\n";
    $table = $table . "</th>\n";
    $table = $table . "<th width='90px'><b>Bemerkung</b>\n";
    $table = $table . "</th>\n";
    $table = $table . "<th width='50px'><b>Ware(n)</b>\n";
    $table = $table . "</th></tr>\n";
    $id=0;
    foreach ($orders as $order){
        $id++;
//artikelholen
        $lines = GetOrderLine($order->entity_id, $order->increment_id);
        $lcol = "#FFFFFF";
        foreach ($lines as $line){
            if ($line->data_error != ""){
                $lcol = "#FF0000";
            }
        }
        $table = $table . "<tr BGCOLOR=" . $lcol . ">";
        $table = $table . "<td title=\"Bestellung ausw&auml;hlen\" width='20px'>";
        $table = $table . "<input id=\"" . $id . "\"class= \"ordercheckbox\" type=\"checkbox\" name=\"orders[]\" onmousedown=\"isKeyPressed(this.value, " . $id . ", event)\" value=\"" . $order->increment_id . "\"><br>\n";
        $table = $table . "</td>\n";
        $aorder = "<a href=\"order.php" . "?MKZ="  . $MKZ . "&UPG=" . $UGP . "&order=" . $order->increment_id . "&function=ShowTheOrder\">";
        $aorder = $aorder . $order->increment_id . "</a>";
        $table = $table . "<td title=\"Bestellung aufrufen\" width='80px'>";
        $table = $table . "" . $aorder . " ";
        $table = $table . "</td>\n";
        $hint = "";
        foreach ($lines as $line) {
            $hint = $hint . round($line->qty_ordered) . " " . GetEinheit($line->einheit,GetVPE($line->vpe)) . " " . $line->name. "\n";
            if ($line->data_error != ""){
                $hint = "(DATENFEHLER) " . $line->data_error . " " . $hint;
            }
        }
        $table = $table . "<td width='40px' align='center' title=\"" . $hint . "\">";
        $table = $table . "" . sizeof($lines) . " ";
        $table = $table . "</td>\n";
        $ts = strtotime($order->created_at);
        $table = $table . "<td width='120px'>";
        $table = $table . "" . date("d.m.Y G:i:s", $ts) . " ";
        $table = $table . "</td>\n";
        $table = $table . "<td width='100px'>";
        $table = $table . "" . $order->customer_lastname . " ";
        $table = $table . "</td>\n";
        $table = $table . "<td width='80px'>";
        $table = $table . "" . $order->customer_firstname . " ";
        $table = $table . "</td>\n";
        $table = $table . "<td width='50px'>";
        $table = $table . "" . substr($order->status_mag, 0, 15) . " ";
        $table = $table . "</td>\n";
        $table = $table . "<td width='50px'>";
        $table = $table . "" . $order->status . " ";
        $table = $table . "</td>\n";
        $table = $table . "<td width='100px'>";
        $table = $table . "" . substr($order->shipping_description, 0, 15) . " ";
        $table = $table . "</td>\n";
        $table = $table . "<td  width='90px'>";
        $table = $table . "";
        $table = $table . "<input name=\"" . $order->increment_id . "\" onchange=\"myFunction(this.name, this.value)\" size=55 maxlength=255 type=\"text\" ";
        $table = $table . "value=\"" . $order->bemerkung . "\">";
        $table = $table . "</td>\n";
        $table = $table . "<td  width='50px'>";
        $ahref = "<a href=\"order.php?MKZ=" . $MKZ . "&UPG=" . $UGP . "&order=" . $order->increment_id . "\"><button>buchen</button></a>";
        $table = $table . "". $ahref . "";
        $table = $table . "</td></tr>\n";
    }
    $table = $table . "</table><br>\n";
    return $table;
}

function DeleteClosedOrders($servername, $username, $password, $dbname){
    if (db_exists($servername, $username, $password, $dbname)) {
// Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = 'DELETE FROM `orders` WHERE status_mag = \'closed\'';
        $result = $conn->query($sql);
    }
}

function ModifyCompleteOrdersToPrinted ($servername, $username, $password, $dbname){
    if (db_exists($servername, $username, $password, $dbname)) {
// Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = 'UPDATE `orders` SET status="reserved" WHERE status_mag like \'complete\'';
        $result = $conn->query($sql);
    }
}

function ImportOrders (&$servername, &$username, &$password, &$dbname, $date, $time){
    $orders = ReadTheOrders ($servername, $username, $password, $dbname, $date, $time);
    return $orders;
}

function PutOrdersInDB (&$servername, &$username, &$password, &$dbname, $orders){
    SaveTheOrders ($servername, $username, $password, $dbname, $orders);
}

function GetTableFieldValue (&$conn, $table_name, $keyfield, $keyvalue, $value_column){
    $res = "";
    $sql = "SELECT * FROM " . $table_name . " WHERE " . $keyfield . " LIKE '" . $keyvalue . "'";

//echo $sql . "<br>\n";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $res = $row[$value_column];
        }
    } else {
//        echo "0 results";
    }
    return $res;
}

function GetTableFieldValueByWhere (&$conn, $table_name, $whereclause, $value_column){
    $res = "";
    $sql = "SELECT * FROM " . $table_name . " WHERE " . $whereclause;

//echo $sql . "<br>\n";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $res = $row[$value_column];
            break;
        }
    } else {
//        echo "0 results<br>";
            $res = "n/a";
    }
    return $res;
}

function GetQuotes ($conn, $quote_id){
    $options = "";
    $opt_ids = GetTableFieldValueByWhere($conn, 'sales_flat_quote_item_option',
        'item_id=' . $quote_id . ' and code=\'option_ids\'', 'value');
    if ($opt_ids == "" or $opt_ids == "n/a") {
    }else {
        $optio = explode(",",$opt_ids);
        foreach($optio as $opt){
            $title = GetTableFieldValueByWhere($conn, 'catalog_product_option_title',
                'option_id=' . $opt, 'title');

            $opt_key = GetTableFieldValueByWhere($conn, 'sales_flat_quote_item_option',
                'item_id=' . $quote_id . ' and code=\'option_'.$opt.'\'', 'value');

            $value = GetTableFieldValueByWhere($conn, 'catalog_product_option_type_title',
                'option_type_id=' . $opt_key, 'title');

            $options = $options . $title . ": " . $value;
        }
    }

    return $options;
};

function GetOrderLineArticle (&$conn, $table_name, $keyfield, $keyvalue, $order_number){
    $res = array ();
    $sql = "SELECT * FROM " . $table_name . " WHERE " . $keyfield . " LIKE '" . $keyvalue . "'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            $line = new mag_order_line();
            $line->order_number = $order_number;
            $line->qty_ordered = $row['qty_ordered'];
            $line->sku = $row['sku'];
            $line->name = $row['name'];
            $line->product_id = $row['product_id'];
            $line->product_options = $row['product_options'];

            $line->quote_id= $row['quote_item_id'];
            $line->product_quotes = GetQuotes($conn, $line->quote_id);

            $line->ean = GetTableFieldValueByWhere($conn, 'catalog_product_entity_varchar',
                'entity_id like ' . $line->product_id . ' and attribute_id like 154', 'value');
            $line->einheit = GetTableFieldValueByWhere($conn, 'catalog_product_entity_int',
                'entity_id like ' . $line->product_id . ' and attribute_id like 176', 'value');
            $line->vpe = GetTableFieldValueByWhere($conn, 'catalog_product_entity_varchar',
                'entity_id like ' . $line->product_id . ' and attribute_id like 177', 'value');
            $line->packstueck = GetTableFieldValueByWhere($conn, 'catalog_product_entity_varchar',
                'entity_id like ' . $line->product_id . ' and attribute_id like 178', 'value');
            $line->product_lort = GetTableFieldValueByWhere($conn, 'catalog_product_entity_int',
                'entity_id like ' . $line->product_id . ' and attribute_id like 180', 'value');
            $line->product_ltxt = GetTableFieldValueByWhere($conn, 'eav_attribute_option_value',
                'option_id like ' . $line->product_lort, 'value');
            $line->product_eans = GetTableFieldValueByWhere($conn, 'catalog_product_entity_varchar',
                'entity_id like ' . $line->product_id . ' and attribute_id like 179', 'value');
            if ($line->product_eans == "n/a" OR $line->product_eans == ""){
                $line->product_eans = "";
                $line->product_base = "";
            } else {
                $line->product_base = $line->name;
            }
            if ($line->product_eans != ""){

                $eans = explode(";", $line->product_eans);

                for ($i=0; $i < sizeof($eans);$i++){
                    $linez = new mag_order_line();
                    $linez->order_number = $order_number;
                    $linez->qty_ordered = $row['qty_ordered'];
                    $entity_id = GetTableFieldValueByWhere($conn, 'catalog_product_entity_varchar', '`attribute_id` like 154 AND value like ' .$eans[$i], 'entity_id');
                    $linez->sku = GetTableFieldValueByWhere($conn, 'catalog_product_entity', '`entity_id` like ' . $entity_id , 'sku');
                    $linez->name = GetTableFieldValueByWhere($conn, 'catalog_product_entity_varchar',
                        'entity_id like ' . $entity_id . ' and attribute_id like 71', 'value');
                    $linez->ean = GetTableFieldValueByWhere($conn, 'catalog_product_entity_varchar',
                        'entity_id like ' . $entity_id . ' and attribute_id like 154', 'value');
                    $linez->einheit = GetTableFieldValueByWhere($conn, 'catalog_product_entity_int',
                        'entity_id like ' . $entity_id . ' and attribute_id like 176', 'value');
                    $linez->vpe = GetTableFieldValueByWhere($conn, 'catalog_product_entity_varchar',
                        'entity_id like ' . $entity_id . ' and attribute_id like 177', 'value');
                    $linez->packstueck = GetTableFieldValueByWhere($conn, 'catalog_product_entity_varchar',
                        'entity_id like ' . $entity_id . ' and attribute_id like 178', 'value');
                    $linez->product_lort = GetTableFieldValueByWhere($conn, 'catalog_product_entity_int',
                        'entity_id like ' . $linez->product_id . ' and attribute_id like 180', 'value');
                    $linez->product_ltxt = GetTableFieldValueByWhere($conn, 'eav_attribute_option_value',
                        'option_id like ' . $linez->product_lort, 'value');
                    $linez->product_eans = GetTableFieldValueByWhere($conn, 'catalog_product_entity_varchar',
                        'entity_id like ' . $entity_id . ' and attribute_id like 179', 'value');
                    if ($linez->product_eans == "n/a" OR $linez->product_eans == ""){
                        $linez->product_eans= "";
                    }
                    $linez->product_base = $line->product_base;
                    $linez->data_error = GetDataError($linez);
                    $linez->not_in_store = "";

                    $res[] = $linez;
                }
            } else {
                $line->data_error = GetDataError($line);
                $line->not_in_store = "";
                $res[] = $line;
            }
        }
    } else {
//        echo "0 results";
    }
    return $res;
}

function GetOrder($order_number){
// debug step
//    echo "function getorder start<br>\n";

// includes
    require_once ('database.php');
    require_once ('menue.php');

    $servername = "localhost";
    $username = "hififabrik_mag";
    $password = "Hf54mC74slRw";
    $dbname = "hififabrik_mag";

    if (db_exists($servername, $username, $password, $dbname)) {
//        echo "database found<br>\n";

// Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        $order = new mag_order();
        $order->order_number = $order_number;
        $order->order_date = GetTableFieldValue ($conn, 'sales_flat_order', 'increment_id', $order_number, 'created_at');
// Get order_id
        $order->entity_id = GetTableFieldValue ($conn, 'sales_flat_order', 'increment_id', $order_number, 'entity_id');
// Get address_id
        $order->billing_address_id = GetTableFieldValue ($conn, 'sales_flat_order', 'increment_id', $order_number, 'billing_address_id');
        $order->address_id = GetTableFieldValue ($conn, 'sales_flat_order', 'increment_id', $order_number, 'shipping_address_id');
// get Address infos
        $order->region = GetTableFieldValue ($conn, 'sales_flat_order_address', 'entity_id', $order->address_id, 'region');
        $order->postcode = GetTableFieldValue ($conn, 'sales_flat_order_address', 'entity_id', $order->address_id, 'postcode');
        $order->lastname = GetTableFieldValue ($conn, 'sales_flat_order_address', 'entity_id', $order->address_id, 'lastname');
        $order->street = GetTableFieldValue ($conn, 'sales_flat_order_address', 'entity_id', $order->address_id, 'street');
        $order->city = GetTableFieldValue ($conn, 'sales_flat_order_address', 'entity_id', $order->address_id, 'city');
        $order->email = GetTableFieldValue ($conn, 'sales_flat_order_address', 'entity_id', $order->address_id, 'email');
        $order->telephone = GetTableFieldValue ($conn, 'sales_flat_order_address', 'entity_id', $order->address_id, 'telephone');
        $order->country_id = GetTableFieldValue ($conn, 'sales_flat_order_address', 'entity_id', $order->address_id, 'country_id');
        $order->firstname = GetTableFieldValue ($conn, 'sales_flat_order_address', 'entity_id', $order->address_id, 'firstname');
        $order->paymethod = GetTableFieldValue ($conn, 'sales_flat_order_payment', 'entity_id', $order->entity_id, 'method');

        $order->ba_lastname = GetTableFieldValue ($conn, 'sales_flat_order_address', 'entity_id', $order->billing_address_id, 'lastname');
        $order->ba_firstname = GetTableFieldValue ($conn, 'sales_flat_order_address', 'entity_id', $order->billing_address_id, 'firstname');
// status Hifi-Fabrik holen
//        echo "huhu";
        $servername = "localhost";
        $username = "hififabrik_int";
        $password = "Hf54mC74slRw";
        $dbname = "hififabrik_intern";

        if (db_exists($servername, $username, $password, $dbname)) {
//        echo "database found<br>\n";
//// Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);
            $order->status = GetStatusInfo(GetTableFieldValue($conn, 'orders', 'increment_id', $order->order_number, 'status'));
            $order->bemerkung = GetTableFieldValue($conn, 'orders', 'increment_id', $order->order_number, 'bemerkung');
        }
    }
//    echo "function getorder end<br>\n";
    if ($order->order_date == ""){
        $order = new mag_order();
    }
    return $order;
}

function GetOrderLine($entity_id, $order_number){

// debug step
//    echo "function getorderline start<br>\n";

// includes
    require_once ('database.php');
    require_once ('menue.php');

    $servername = "localhost";
    $username = "hififabrik_mag";
    $password = "Hf54mC74slRw";
    $dbname = "hififabrik_mag";

    if (db_exists($servername, $username, $password, $dbname)) {
//        echo "database found<br>\n";

// Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        $orderlines = array();
// Get order_id and billing_address_id
        $orderlines = GetOrderLineArticle ($conn, 'sales_flat_order_item', 'order_id', $entity_id, $order_number);
    }

//    echo "function getorderline end<br>\n";
    return $orderlines;
}

function GetMultiCellHeight($w, $h, $txt, $border=null, $align='J') {
    // Calculate MultiCell with automatic or explicit line breaks height
    // $border is un-used, but I kept it in the parameters to keep the call
    //   to this function consistent with MultiCell()
    $cw = &$this->CurrentFont['cw'];
    if($w==0)
        $w = $this->w-$this->rMargin-$this->x;
    $wmax = ($w-2*$this->cMargin)*1000/$this->FontSize;
    $s = str_replace("\r",'',$txt);
    $nb = strlen($s);
    if($nb>0 && $s[$nb-1]=="\n")
        $nb--;
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $ns = 0;
    $height = 0;
    while($i<$nb)
    {
        // Get next character
        $c = $s[$i];
        if($c=="\n")
        {
            // Explicit line break
            if($this->ws>0)
            {
                $this->ws = 0;
                $this->_out('0 Tw');
            }
            //Increase Height
            $height += $h;
            $i++;
            $sep = -1;
            $j = $i;
            $l = 0;
            $ns = 0;
            continue;
        }
        if($c==' ')
        {
            $sep = $i;
            $ls = $l;
            $ns++;
        }
        $l += $cw[$c];
        if($l>$wmax)
        {
            // Automatic line break
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
                if($this->ws>0)
                {
                    $this->ws = 0;
                    $this->_out('0 Tw');
                }
                //Increase Height
                $height += $h;
            }
            else
            {
                if($align=='J')
                {
                    $this->ws = ($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
                    $this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
                }
                //Increase Height
                $height += $h;
                $i = $sep+1;
            }
            $sep = -1;
            $j = $i;
            $l = 0;
            $ns = 0;
        }
        else
            $i++;
    }
    // Last chunk
    if($this->ws>0)
    {
        $this->ws = 0;
        $this->_out('0 Tw');
    }
    //Increase Height
    $height += $h;

    return $height;
}