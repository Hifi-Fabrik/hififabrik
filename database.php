<?php
/**
 * Created by PhpStorm.
 * User: Rudi
 * Date: 12.10.2015
 * Time: 13:24
 */

// magento bestellung

global $mySession;

class mySession{
    public $logedin=false;
    public $userid=0;
    public $user="";
    public $user_nachname="";
    public $user_vorname="";
    public $usergroup="";
    public $last_login=0;
    public $is_active_user=false;
}

class order
{
    public $status_mag = "";
    public $created_at = "";
    public $increment_id = "";
    public $entity_id = "";
    public $customer_firstname="";
    public $customer_lastname="";
    public $shipping_description = "";
    public $bemerkung = "";
    public $customer_ba_firstname="";
    public $customer_ba_lastname="";
    public $status = "";
}

class order_line extends mag_order_line
{
    public $lortfrom = "";
    public $lortto  = "";
    public $move_kz = "";
    public $user = "";
    public $book_z = "";
    public $book_date = "";
    public $status = "";
}


class mag_order
{
    public $order_number = "";
    public $order_date = "";
    public $entity_id = "";
    public $adress_id = "";
    public $billing_adress_id = "";
    public $region = "";
    public $postcode = "";
    public $lastname = "";
    public $street = "";
    public $city = "";
    public $email = "";
    public $telephone = "";
    public $country_id = "";
    public $firstname = "";
    public $paymethod = "";
    public $status = "";
    public $bemerkung = "";
}

// magento bestellzeile
class mag_order_line {
    public $order_number = "";
    public $quote_id = "";
    public $qty_ordered = 0;
    public $sku = "";
    public $name = "";
    public $ean = "";
    public $einheit = "";
    public $vpe = "";
    public $packstueck = "";
    public $product_id= "";
    public $product_options = ""; // L�nge
    public $product_eans = "";
    public $product_base = "";
    public $product_lort = "";
    public $product_ltxt = "";
    public $product_quotes = ""; // Farbe
    public $data_error = "";
    public $not_in_store = "";
}

function OpenDatabase(){

    $_db_host = "localhost";            # meist localhost
    $_db_datenbank = "hififabrik_intern";
    $_db_username = "hififabrik_int";
    $_db_passwort = "Hf54mC74slRw";

    SESSION_START();

    # Datenbankverbindung herstellen
    $link = mysql_connect($_db_host, $_db_username, $_db_passwort);

    # Hat die Verbindung geklappt ?
    if (!$link)
    {
        die("Keine Datenbankverbindung möglich: " . mysql_error());
    }

    # Verbindung zur richtigen Datenbank herstellen
    $datenbank = mysql_select_db($_db_datenbank, $link);

    if (!$datenbank)
    {
        echo "Kann die Datenbank nicht benutzen: " . mysql_error();
        mysql_close($link);        # Datenbank schliessen
        exit;                    # Programm beenden !
    }
    return $link;
}

function GetMKZandUGP ($MKZ, $UGP, $order, $function)
{
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
//        echo $order . "<br>";
    } else {
        $order = "";
    }
    if (isset ($_GET["function"])) {
        $function = $_GET["function"];
//        echo $function . "<br>";
    } else {
        $function = "";
    }
    return;
}

function RecordKeyExists (&$conn, $tablename, $keyfield, $keyvalue){
    $fnd = false;
    $sql = "select " . $keyfield . " FROM " . $tablename . " WHERE ". $keyfield . " like " . $keyvalue;
//echo $sql . "<br>";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $fnd = true;
    }
    return $fnd;
}

function CreateOrder (&$conn, $order){
    $sql = "INSERT INTO `orders` (`status_mag`, `created_at`, `increment_id`, `entity_id`, `customer_firstname`, `customer_lastname`,
                                  `shipping_description`, `status`) values (";

    $sql = $sql . "\"" . $order->status_mag . "\", ";
    $sql = $sql . "\"" . $order->created_at . "\", ";
    $sql = $sql . "\"" . $order->increment_id . "\", ";
    $sql = $sql . "\"" . $order->entity_id . "\", ";
    $sql = $sql . "\"" . $order->customer_firstname . "\", ";
    $sql = $sql . "\"" . $order->customer_lastname . "\", ";
    $sql = $sql . "\"" . $order->shipping_description . "\",'imported')";

//echo $sql . "<br>";

    if ($conn->query($sql) === TRUE) {
//        echo "record " . $order->increment_id . " gespeichert.<br>";
    } else {
//        echo "record " . $order->increment_id . " NICHT gespeichert.<br>";
        echo $sql. "<br>";
    }
}

function UpdateOrder (&$conn, $order){
    $sql = "UPDATE `orders` SET status_mag = \"" . $order->status_mag . "\", created_at = \"" . $order->created_at;
    $sql = $sql .  "\", entity_id=\"" . $order->entity_id . "\", customer_firstname=\"" . $order->customer_firstname;
    $sql = $sql .  "\", customer_lastname=\"" . $order->customer_lastname . "\", shipping_description=\"" . $order->shipping_description;
    $sql = $sql . "\" WHERE increment_id=" .  $order->increment_id;

//echo $sql . "<br>";

    if ($conn->query($sql) === TRUE) {
//        echo "record " . $order->increment_id . " ge�ndert.<br>";
    } else {
//        echo "record " . $order->increment_id . " NICHT ge�ndert.<br>";
        echo $sql. "<br>";
    }
}

function SaveTheOrders ($servername, $username, $password, $dbname, $orders){
    if (db_exists($servername, $username, $password, $dbname)) {
// Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        foreach($orders as $order){
            if (RecordKeyExists ($conn, 'orders', 'increment_id', $order->increment_id)){
                UpdateOrder ($conn, $order);
//echo "<br>record updated order = " . $order->increment_id . "<br>";
            } else {
                CreateOrder ($conn, $order);
            };
        }
    }
}

function GetTheOrderList ($servername, $username, $password, $dbname){

    $orders = array();
    if (db_exists($servername, $username, $password, $dbname)) {
// Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT * FROM orders WHERE (status like 'imported' OR status like 'printed') ORDER BY created_at DESC";

//echo $sql . "<br>";#

        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $order = new order();
                $order->created_at = $row["created_at"];
                $order->customer_firstname = $row["customer_firstname"];
                $order->customer_lastname = $row["customer_lastname"];
                $order->entity_id = $row["entity_id"];
                $order->increment_id = $row["increment_id"];
                $order->status_mag = $row["status_mag"];
                $order->shipping_description = $row["shipping_description"];
                $order->status = $row["status"];
                $order->bemerkung= $row["bemerkung"];
//print_r($order);
                $orders[] = $order;
            }
        } else {
            echo "0 results";
        }
        $conn->close();
    }
    return $orders;
}


function ReadTheOrders (&$servername, &$username, &$password, &$dbname, &$date, &$time){
    $orders = array();
    if (db_exists($servername, $username, $password, $dbname)) {
// Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = 'SELECT status, created_at, shipping_description, customer_firstname, customer_lastname, increment_id, entity_id ';
        $sql = $sql . 'FROM `sales_flat_order` WHERE created_at >= \'' . $date . ' ' . $time . '\' ORDER BY created_at DESC';

        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $order = new order();
                $order->created_at = $row["created_at"];
                $order->customer_firstname = $row["customer_firstname"];
                $order->customer_lastname = $row["customer_lastname"];
                $order->entity_id = $row["entity_id"];
                $order->increment_id = $row["increment_id"];
                $order->status_mag = $row["status"];
                $order->shipping_description = $row["shipping_description"];
                $order->bemerkung = $row["bemerkung"];
                $orders[] = $order;
            }
        } else {
            echo "0 results";
        }
        $conn->close();
    }
    return $orders;
}

function LoadMenuItems ($servername, $username, $password, $dbname, $func, $ugrp){

    require_once 'menue.php';

    $items = array();
    if (db_exists($servername, $username, $password, $dbname)) {
// Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "SELECT * FROM menuitem WHERE function like '" . $func . "'" . " AND usergroup like '" . $ugrp . "' ORDER BY tabrow, tabcol asc";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                $item = new menuitem();
                $item->func = $row["function"];
                $item->ugrp = $row["usergroup"];
                $item->hint = $row["hint"];
                $item->href = $row["href"];
                $item->phpf = $row["phpf"];
                $item->text = $row["text"];
                $item->unam = $row["user"];
                $item->row  = $row["tabrow"];
                $item->col  = $row["tabcol"];
                $items[] = $item;
            }
        } else {
            echo "0 results";
        }
        $conn->close();
    }
    return $items;
}

function CreateMenuItems($servername, $username, $password, $dbname){
    if (db_exists($servername, $username, $password, $dbname)) {
// Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
// sql to create table

        $sql = "INSERT INTO `menuitem` (`function`, `usergroup`, `user`, `href`, `phpf`, `text`, `hint`, `tabcol`, `tabrow`) values (
                                        'admin', 'admin', '', 'admin.php', 'chckbase',
                                        'Datenbank pr&uuml;fen', 'Konsistenz der Datenbank wird gepr&uuml;ft', 1, 1)";
        if ($conn->query($sql) === TRUE) {
//                    echo "Table ". $tablename . " created successfully";
        } else {
            echo $sql;
            echo "Error filling table: " . $conn->error . "<br>";
        }

        $sql = "INSERT INTO `menuitem` (`function`, `usergroup`, `user`, `href`, `phpf`, `text`, `hint`, `tabcol`, `tabrow`) values (
                                        'BUCHKZ', 'admin', '', 'buchen.php', 'WEOVPLAG',
                                        'Ware OVP vom Hersteller/Lieferant ins Lager', 'Wareneingang (Lieferung) vom H&auml;ndler/Hersteller ins Lager', 1, 1)";
        if ($conn->query($sql) === TRUE) {
//                    echo "Table ". $tablename . " created successfully";
        } else {
            echo $sql;
            echo "Error filling table: " . $conn->error . "<br>";
        }

        $sql = "INSERT INTO `menuitem` (`function`, `usergroup`, `user`, `href`, `phpf`, `text`, `hint`, `tabcol`, `tabrow`) values (
                                        'BUCHKZ', 'admin', '', 'buchen.php', 'WUOVPRES',
                                        'Ware OVP reservieren', 'OVP-Ware zur Reservierung bringen', 2, 1)";
        if ($conn->query($sql) === TRUE) {
//                    echo "Table ". $tablename . " created successfully";
        } else {
            echo $sql;
            echo "Error filling table: " . $conn->error . "<br>";
        }

        $sql = "INSERT INTO `menuitem` (`function`, `usergroup`, `user`, `href`, `phpf`, `text`, `hint`, `tabcol`, `tabrow`) values (
                                        'BUCHKZ', 'admin', '', 'buchen.php', 'WACUSRES',
                                        'Ware an Kunde versenden MIT Reserviernug', 'Ware zum versand verpacken', 3, 1)";
        if ($conn->query($sql) === TRUE) {
//                    echo "Table ". $tablename . " created successfully";
        } else {
            echo $sql;
            echo "Error filling table: " . $conn->error . "<br>";
        }

        $sql = "INSERT INTO `menuitem` (`function`, `usergroup`, `user`, `href`, `phpf`, `text`, `hint`, `tabcol`, `tabrow`) values (
                                        'BUCHKZ', 'admin', '', 'buchen.php', 'WESTOSRV',
                                        'Ware aus Storno an Service', 'Eingegangene stornierte Ware an Service', 1, 2)";
        if ($conn->query($sql) === TRUE) {
//                    echo "Table ". $tablename . " created successfully";
        } else {
            echo $sql;
            echo "Error filling table: " . $conn->error . "<br>";
        }

        $sql = "INSERT INTO `menuitem` (`function`, `usergroup`, `user`, `href`, `phpf`, `text`, `hint`, `tabcol`, `tabrow`) values (
                                        'LAGER', 'admin', '', 'lager.php', 'WARES',
                                        'Ware reservieren', 'Ware aus Lager in Reservierung', 1, 1)";
        if ($conn->query($sql) === TRUE) {
//                    echo "Table ". $tablename . " created successfully";
        } else {
            echo $sql;
            echo "Error filling table: " . $conn->error . "<br>";
        }

        $sql = "INSERT INTO `menuitem` (`function`, `usergroup`, `user`, `href`, `phpf`, `text`, `hint`, `tabcol`, `tabrow`) values (
                                        'LAGER', 'admin', '', 'lager.php', 'WAVER',
                                        'Ware in Versand', 'Ware aus Reservierung verpacken und verschicken', 1, 2)";
        if ($conn->query($sql) === TRUE) {
//                    echo "Table ". $tablename . " created successfully";
        } else {
            echo $sql;
            echo "Error filling table: " . $conn->error . "<br>";
        }


        $sql = "INSERT INTO `menuitem` (`function`, `usergroup`, `user`, `href`, `phpf`, `text`, `hint`, `tabcol`, `tabrow`) values (
                                        'LAGER', 'admin', '', 'lager.php', 'WAUMB',
                                        'Ware umbuchen', 'Ware aus neu - OVP, Retoure - B-Ware, Ausstellung und Gebrauchtware umbuchen', 1, 3)";
        if ($conn->query($sql) === TRUE) {
//                    echo "Table ". $tablename . " created successfully";
        } else {
            echo $sql;
            echo "Error filling table: " . $conn->error . "<br>";
        }


        $conn->close();
    }

}

function CheckDataBaseReady($servername, $username, $password, $dbname){
    $tables = array();
    $tables[] = 'Artikel';
    $tables[] = 'menuitem';
    $tables[] = 'orders';

    $res = true;

    if (db_exists($servername, $username, $password, $dbname)) {
        foreach ($tables as $table) {
            if (table_exists($servername, $username, $password, $dbname, $table)) {
            } else {
                $res = false;
                create_table($servername, $username, $password, $dbname, $table);
                echo "Tabelle " . $table ." angelegt...<br>\n";
                if ($table == 'menuitem'){
                    CreateMenuItems ($servername, $username, $password, $dbname);
                }
            }
        }
    } else {
        $res = false;
        echo $dbname . " existiert nicht auf Server ". $servername . " oder falscher Benutzer " . $username;
    }

    return $res;
}

function db_exists($servername, $username, $password, $dbname){
// Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
    if ($conn->connect_error) {
        $res = false;
        echo ("<BR>" . "Connection failed: " . $conn->connect_error);
    } else {
        $res = true;
    }
    return $res;
}

function table_exists($servername, $username, $password, $dbname, $tablename){
    if (db_exists($servername, $username, $password, $dbname)) {
// Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
// build sql
        $sql = "SHOW TABLES LIKE '" . $tablename . "'";
// execute sql
        $result = mysqli_query($conn, $sql);
        $rows = mysqli_num_rows($result);
        $tableExists = $rows > 0;
        if ($tableExists){
            $res = true;
        } else {
            $res = false;
            echo "Table " . $tablename . " does not exist<br>\n";
        }
    }
    return $res;
}


function create_table($servername, $username, $password, $dbname, $tablename)
{

    if (db_exists($servername, $username, $password, $dbname)) {
// Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
// build sql
// sql to create table

                $sql = "CREATE TABLE " . "$tablename" . " (";

                 switch($tablename) {
                     case "menuitem":
                         $sql = $sql . "id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                                        function VARCHAR(30) NOT NULL,
                                        usergroup VARCHAR(30) NOT NULL,
                                        user VARCHAR(30),
                                        href VARCHAR(30),
                                        phpf VARCHAR(30),
                                        text VARCHAR (50),
                                        hint VARCHAR(50),
                                        tabcol INT,
                                        tabrow INT, ";
                         break;
                     case "orders":
                         $sql = $sql . "increment_id VARCHAR(50) UNIQUE PRIMARY KEY,
                                        status_mag VARCHAR(32) NOT NULL,
                                        created_at DATETIME,
                                        entity_id INT (10),
                                        customer_firstname VARCHAR(255),
                                        customer_lastname VARCHAR(255),
                                        shipping_description VARCHAR (255),
                                        status VARCHAR (32),
                                        bemerkung VARCHAR (100), ";
                         break;
                     case "Artikel":
                         break;
                 }

                $sql = $sql . "reg_date TIMESTAMP)";
// echo $sql . "<br>";
                if ($conn->query($sql) === TRUE) {
//                    echo "Table ". $tablename . " created successfully";
                } else {
                    echo "Error creating table: " . $conn->error;
                }
        $conn->close();
        echo $sql;
    }
}
