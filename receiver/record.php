<?php
/*
	Redistribution and use in source and binary forms, with or without
	modification, are permitted provided that the following conditions are met: 

	1. Redistributions of source code must retain the above copyright notice, this
	   list of conditions and the following disclaimer. 
	2. Redistributions in binary form must reproduce the above copyright notice,
	   this list of conditions and the following disclaimer in the documentation
	   and/or other materials provided with the distribution. 

	THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
	ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
	WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
	DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
	ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
	(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
	LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
	ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
	(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
	SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
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
