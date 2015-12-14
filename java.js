/**
 * Created by Rudi on 24.11.2015.
 */

var lastOrderChecked = "";

function myFunction(order, val) {
    alert("The input value has changed. The new value is: " + val + " order: " + order);
    header("Location: order.php?function=UpdateOrder&order=" + order + "&bemerkung=" + val);

//    UPDATE `orders` SET `bemerkung` = 'neue bemerkung' WHERE `orders`.`increment_id` = '100018517

}
function PrintCheckedOrders() {
    var checkedValue = "";
    var inputElements = document.getElementsByClassName('ordercheckbox');
    for(var i=0; inputElements[i]; i++){
        if(inputElements[i].checked){
            checkedValue = checkedValue + inputElements[i].value + ";";
        }
    }
    if (checkedValue != "") {
        var phpCall = "PrintCheckedOrderList(\"" + checkedValue + "\");";
        window.open("order.php?function=PrintChecked&orders=" + checkedValue);
    } else {
        alert("Bitte kreuzen Sie mindestens eine Bestellung an.");
    }
}
function isKeyPressed(order, id, event) {
//    alert (order);
//    alert (id);
//    alert (lastOrderChecked);
    var checkit = ! document.getElementsByName('auswahl').checked;
    if (order == "alle"){
        i = 1;
        while (document.getElementById(i)){
            document.getElementById(i).checked = checkit;
            i++;
        }
        document.getElementsByName('auswahl').checked = checkit;
    }else {
        if (event.shiftKey) {
            i = id;
            while ((i >= 0) && (!document.getElementById(i).checked)) {
                document.getElementById(i).checked = true;
                i--;
            }
        }
        lastOrderChecked = order;
    }
}

function eanchanged(name, val){
    var str = name;
    var patt = new RegExp(/[0-9]/g);
    var res = patt.exec(str);
    var row = "row"+res;
    var ean = "EAN"+res;
    if (document.getElementById("Artikel")){
        if (document.getElementById(row)){
            var eannr = document.getElementById(ean).innerHTML;
            if (val == eannr){
                document.getElementById(row).setAttribute("bgcolor", "#66cc66");
            } else {
                document.getElementById(row).setAttribute("bgcolor", "#ff6600");
            }
        }
    }
}

function CheckScan() {
    if (document.getElementById("Artikel")) {
        var x = document.getElementById("Artikel").rows.length;
        var ok = true;
        for (i = 1; i < x; i++) {
            var row = "row"+i;
            var scn = "SCAN"+i;
            var ean = "EAN"+i;
            if (document.getElementById(row)) {
                var eannr = document.getElementById(ean).innerHTML;
                var scanr = document.getElementById(row).getElementsByTagName('input')[0].value;
                if(eannr != scanr) {
                    ok= false;
                }
            }
        }
    }
    if (!ok){
        alert ('EAN Nummer(n) NICHT korrekt.\n\nBitte EAN-Nummer(n) scannen.')
    } else {
        DoBookingWares();
    }
    return ok;
}

function DoBookingWares(){
    alert ('dobooking wares');
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else { // code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET","booking.php?function=BookReservation&order=100018323 ",true);
    xmlhttp.send();
    alert ('dobooking wares end');
}

function showUser(str) {
    if (str=="") {
        document.getElementById("txtHint").innerHTML="";
        return;
    }
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else { // code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById("txtHint").innerHTML=xmlhttp.responseText;
        }
    }
    xmlhttp.open("GET","getuser.php?q="+str,true);
    xmlhttp.send();
}