<?php

global $mySession;

class mySession{
    public $logedin = false;
    public $userid = 0;
    public $user = "";
    public $user_nachname = "";
    public $user_vorname ="";
    public $usergroup = "";
    public $last_login = 0;
    public $is_active_user = false;
}

class Article
{
    public $entity_id = 0;
    public $manufacturer = "";
    public $sku = "";
    public $name = "";
    public $price = 0.00;
    public $lieferbar = "";
    public $ean = "";
    public $qty = 0.00;
    public $min_qty = 0.00;
    public $is_in_stock = 0;
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

// order history
class order_history {
    public $order_number = "";
    public $order_activity = "";
    public $order_activity_text = "";
    public $order_memo = "";
    public $book_user = "";
    public $book_date_first = "";
    public $book_date = "";
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

function OpenMagentoBase(){
    $_db_host = "localhost";            # meist localhost
    $_db_datenbank = "hififabrik_mag";
    $_db_username = "hififabrik_int";
    $_db_passwort = "Hf54mC74slRw";
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
    $_db_host = "localhost";            # meist localhost
    $_db_datenbank = "hififabrik_int";
    $_db_username = "hififabrik_int";
    $_db_passwort = "Hf54mC74slRw";
    return $link;
}
