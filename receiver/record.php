<?php
/*
    record.php
    By Martin A. COLEMAN.
    A part of the LUNAR project. This just shows one quick and easy way
    to receive and record a reading in PHP.
    Released into the Public Domain or its nearest equivalent in your jurisdiction.
*/
include("sqlite3.php");

if(!isset($_GET['serial']) || !isset($_GET['ac_volts']) || !isset($_GET['ac_current']) || !isset($_GET['ac_freq']) || !isset($_GET['heatsink_temp']) || !isset($_GET['pv1_volts']) || !isset($_GET['pv2_volts']) || !isset($_GET['pv1_cur']) || !isset($_GET['pv2_cur']) || !isset($_GET['watts']))
{
	die("Error");
}
$serial=$_GET['serial'];
//$rightnow=$_GET['rightnow'];
$rightnow=date('ymdhis');
$ac_volts=$_GET['ac_volts'];
$ac_current=$_GET['ac_current'];
$ac_freq=$_GET['ac_freq'];
$heatsink_temp=$_GET['heatsink_temp'];
$pv1_volts=$_GET['pv1_volts'];
$pv2_volts=$_GET['pv2_volts'];
$pv1_cur=$_GET['pv1_cur'];
$pv2_cur=$_GET['pv2_cur'];
$watts=$_GET['watts'];

$base=sqlite_open("../data/records.sq3", 0666);
$query = "INSERT INTO readings(inverter, recordedat, ac_volts, ac_current, ac_freq, heatsink_temp, pv1_volts, pv2_volts, pv1_current, pv2_current, watts) VALUES('".$serial."', datetime('NOW'), '".$ac_volts."', '".$ac_current."', '".$ac_freq."', '".$heatsink_temp."', '".$pv1_volts."', '".$pv2_volts."', '".$pv1_cur."', '".$pv2_cur."', ".$watts.")";
$results = sqlite_query($base, $query);
$base->close();
?>
