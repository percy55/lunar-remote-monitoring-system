<?php
/*
    By Martin A. COLEMAN.
    It's a stupid little pretend inverter simulator.
    Released into the Public Domain or its nearest equivalent in your jurisdiction.
*/
//$inverterid="6d854a6fdcd414f2b37e9654f3d2303d";
$serial="051312345";
//$rightnow=date('ymdhis');
$ac_volts=rand(0,500);
$ac_current=rand(0,100);
$ac_freq=rand(50,51);
$heatsink_temp=rand(20,60);
$pv1_volts=rand(1,200);
$pv2_volts=rand(1,200);
$pv1_cur=rand(1,100);
$pv2_cur=rand(1,100);
$watts=rand(0,350);
$auth=md5($serial);

$URL="http://myserver.com/recorddb.php?serial=".$serial."&auth=".$auth."
&ac_volts=".$ac_volts."
&ac_current=".$ac_current."
&ac_freq=".$ac_freq."
&heatsink_temp=".$heatsink_temp."
&pv1_volts=".$pv1_volts."
&pv2_volts=".$pv2_volts."
&pv1_cur=".$pv1_cur."
&pv2_cur=".$pv2_cur."
&watts=".$watts;

//$handle=fopen($URL, "r");
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $URL);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$file = curl_exec($ch);
// echo $file;
?>
