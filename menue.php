<?php

/**
 * Created by PhpStorm.
 * User: Rudi
 * Date: 12.10.2015
 * Time: 12:37
 */

class menuitem {
    public $func = "";
    public $ugrp = "";
    public $unam = "";
    public $phpf = "";
    public $href = "";
    public $text ="";
    public $hint = "";
    public $col = null;
    public $row = null;
}

function BuildCell($entries, $bgcol, $row, $col, $tabwidth, $tabborder, $colanz, $rowanz, $cellhight, $cellwidth, $cellalign, $bgcol){
    $td = "";
    foreach ($entries as $entry){
        if (($entry->row == $row) AND ($entry->col) == $col){
            $td ="<td height=\"" . $cellhight . "\" width=\"" . $cellwidth . "\" bgcolor=\"" . $bgcol[$col-1] . "\" align=\"" . $cellalign;
            $td = $td . "\" title=\"" . $entry->hint . "\">";
            $td = $td . "<a href=\"" . $entry->href . "?"  . $entry->phpf . "\">";
            $td = $td . $entry->text . "</a></td>\n";
        }
    }
    return $td;
}

function BuildMenuTable ($entries, $tabwidth, $tabborder, $colanz, $rowanz, $cellhight, $cellwidth, $cellalign, $bgcol){
    echo "<table border=\"". $tabborder . "\" width=\"" .$tabwidth. "\">\n";
    for ($row = 1; $row <= $rowanz; $row++){
        echo "<tr>\n";
        for ($col = 1; $col <= $colanz; $col++){
            echo BuildCell($entries, $bgcol, $row, $col, $tabwidth, $tabborder, $colanz, $rowanz, $cellhight, $cellwidth, $cellalign, $bgcol);
        }
        echo "</tr>\n";
    }
    echo "</table><br>\n";
}
?>