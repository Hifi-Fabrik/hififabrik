<?php

    require_once ("database.php");

    ##################################################################
    $link = OpenDatabase();
    ##################################################################

    # Ist die $_POST Variable submit nicht leer ???
    # dann wurden Logindaten eingegeben, die müssen wir überprüfen !
    if (!empty($_POST["submit"]))
    {
        # Die Werte die im Loginformular eingegeben wurden "escapen",
        # damit keine Hackangriffe über den Login erfolgen können !
        # Mysql_real_escape ist auf jedenfall dem Befehle addslashes()
        # vorzuziehen !!! Ohne sind mysql injections möglich !!!!
        $_username = mysql_real_escape_string($_POST["username"]);
        $_passwort = mysql_real_escape_string($_POST["passwort"]);

        # Befehl für die MySQL Datenbank
        $_sql = "SELECT * FROM login_usernamen WHERE
                    username='$_username' AND
                    passwort='$_passwort' AND
                    user_geloescht=0
                LIMIT 1";

        # Prüfen, ob der User in der Datenbank existiert !
        $_res = mysql_query($_sql, $link);
        $_anzahl = @mysql_num_rows($_res);

        # Die Anzahl der gefundenen Einträge überprüfen. Maximal
        # wird 1 Eintrag rausgefiltert (LIMIT 1). Wenn 0 Einträge
        # gefunden wurden, dann gibt es keinen Usereintrag, der
        # gültig ist. Keinen wo der Username und das Passwort stimmt
        # und user_geloescht auch gleich 0 ist !
        if ($_anzahl > 0)
        {
            echo "Der Login war erfolgreich.<br>";

            # In der Session merken, dass der User eingeloggt ist !
            $_SESSION["login"] = 1;

            # Den Eintrag vom User in der Session speichern !
            $_SESSION["user"] = mysql_fetch_array($_res, MYSQL_ASSOC);
            $_SESSION["user"]["lastlogin"]=date("d.m.Y H:i:s", $_SESSION["user"]["letzter_login"]);

            # Das Einlogdatum in der Tabelle setzen !
            $ts=time();
            $_sql = "UPDATE login_usernamen SET letzter_login=$ts
                     WHERE id=".$_SESSION["user"]["id"];

            mysql_query($_sql);
            $GLOBALS=$_SESSION["user"];

            header("Location: ../hififabrik_intern/index.php?logedin&userid=".$_SESSION["user"]["id"]);
        }
        else
        {
            echo "Die Logindaten sind nicht korrekt.<br>";
        }
    }

    # Ist der User eingeloggt ???
    if ($_SESSION["login"] == 0)
    {
        # ist nicht eingeloggt, also Formular anzeigen, die Datenbank
        # schliessen und das Programm beenden
        include("login-formular.html");
        mysql_close($link);
        exit;
    }

    # Hier wäre der User jetzt gültig angemeldet ! Hier kann
    # Programmcode stehen, den nur eingeloggte User sehen sollen !!

    ##################################################################

    # Datenbank wieder schliessen
    mysql_close($link);
?>