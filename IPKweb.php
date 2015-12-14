<html>
    <head>
        <title>Internetpreiskontrolle
        </title>
    <style>
        table, th, td {
            border: 1px solid black;
        }
    </style>

    <?php
        $seite = "no side";
        if (isset($_GET["seite"])) {
            $seite = $_GET["seite"];
        }
    ?>

    </head>
<frameset rows="70,*" frameborder="no" border="0" framespacing="0">
    <frame src="/IPKweb/Head.php" name="topFrame" scrolling="No" noresize="noresize" id="topFrame" title="topFrame" bgcolor="pink" />
    <frame src="/IPKweb/Main.php" name="mainFrame" id="mainFrame" title="mainFrame" bgcolor="gray"/>
</frameset>

<noframes><body></body></noframes></html>


<?php
/**
 * Created by PhpStorm.
 * User: Peter Schmitt
 * Date: 16.09.2015
 * Time: 14:11
 */

echo "Hello Hifi-Fabrik";

?>