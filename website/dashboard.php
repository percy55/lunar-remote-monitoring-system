<?php
/*
	dashboard.php
	Copyright (c) 2013 Martin COLEMAN. All rights reserved.
	Released under the BSD 2-Clause license. See LICENSE for details.
*/
include("sqlite3.php");
session_start();
if(!isset($_SESSION['userid']))
{
	header("location: index.html");
}

function show_error($error_msg)
{
	print_header();
	echo "<font size=\"+1\" color=\"#ff0000\"><B>".$error_msg."</B></font>";
	print_footer();
}

function print_header()
{
?>
<html>
<head>
<title>MyCompany Inverter Monitoring Facility</title>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
<link href="mycompany.css" type="text/css" rel="stylesheet">
<script src="mycompany.js" type="text/javascript"></script>
</head>
<body>
<table width="630" align="center">
<tr><td align="center" bgcolor="#000000">
<table width="100%" align="center" bgcolor="#ffffff" border="0">
<tr><td align="center"><img src="mycompany.png"><br>MC Series Online Monitoring Facility</td></tr>
<tr><td align="right">
<?php
if(isset($_SESSION['userid']))
{
	echo "[<a href=\"dashboard.php\">View My Dashboard</a>]&nbsp;[<a href=\"dashboard.php?action=add_inverter\">Add an Inverter</a>]&nbsp;[<a href=\"dashboard.php?action=mydetails\">View my Details</a>]&nbsp;[<a href=\"account.php?action=logout\">Log out</a>]";
} else {
	echo "<hr>";
}
?>
</td></tr>
<tr><td align="center">
<?php
}

function print_footer()
{
?>
</td></tr>
</table>
<font size="-1" color="#ffffff" face="verdana,arial,sans-serif">Copyright &copy; 2013 MYCOMPANY PTY LTD</font>
</td></tr></table>
</body>
</html>
<?php
}

function show_status($serial)
{
    /* get the data we need */
	$base=sqlite_open("../data/records.sq3", 0666);
	$query = "SELECT * FROM readings WHERE inverter='".$serial."' ORDER BY recordedat DESC LIMIT 1";
    //echo $query;
	$results = $base->query($query);
	if(!$results)
	{
		echo "Error: ".$base->lastErrorMsg;
		return;
	}
	$arr=$results->fetchArray();
    var_dump($arr);
	$base->close();
    return;
}

function my_dashboard()
{
    $userid=$_SESSION['userid'];
	print_header();
    echo "<p>Welcome to your dashboard!</p>\n";

	$usersdb=sqlite_open("../data/users.sq3", 0666);
	$query = "SELECT serial, COUNT(inverters.serial) as num, custid, installer FROM inverters, users WHERE custid='".$userid."'";
    //echo $query."\n<br>";
	$results = $usersdb->query($query);
	if(!$results)
	{
		echo "<font color=\"#ff0000\">Oops! Contact the developer and let them know -Error: ".$usersdb->lastErrorMsg."</font>\n";
		return;
	}
	$arr=$results->fetchArray();
    $myinstaller=$arr['installer'];
	if($arr['num'] < 0)
	{
?>
<b>Add an Inverter</b><br>
<form action="dashboard.php" method="post">
Serial Number# <input type="text" name="serial" size="20"><br>
<input type="submit" value="Add">
<input type="hidden" name="action" value="add_inverter">
</form>
<?php
    } else if($arr['num']==1) {
        echo "You have one inverter, which has the serial number:".$arr['serial'].".<br>";
        show_status($arr['serial']);
        echo "Below is a graph of the most recent 24 hour period:<br>\n<img src=\"view1.php?serial=".$arr['serial']."\">";
    } else {
        $query = "SELECT serial FROM inverters WHERE custid='".$userid."'";
        //echo $query."\n<br>";
        $results = $usersdb->query($query);
        echo "Your inverters:<br>\n<ul>\n";
        //var_dump($arr);
        while($row=$results->fetchArray())
        {
            echo "<li><a href=\"dashboard.php?action=view_inverter&serial=".$row['serial']."\">".$row['serial']."</a></li>\n";
        }
        echo "</ul>\n";
	}
	$usersdb->close();
    
    if($myinstaller>0)
    {
        $installersdb=sqlite_open("../data/installers.sq3", 0666);
        $query = "SELECT * FROM installers WHERE instid=".$myinstaller."";
        $results=$installersdb->query($query);
        if(!$results)
        {
            echo "<font color=\"#ff0000\">Oops! Installer DB error. Contact the developer and let them know -Error: ".$installersdb->lastErrorMsg.".</font>\n";
            return;
        }
        $arr=$results->fetchArray();
?>
Your nominated installer is<br>
<table width="300" align="center">
<tr><td bgcolor="#000000">
<table width="100%" align="center" bgcolor="#ffffff">
<tr><td colspan=2 align=center><b><?php echo $arr['instname']; ?></b><br>ABN: <font size="-1"><?php echo $arr['instabn']; ?></font></td></tr>
<tr><td width="100">Address</td><td><?php echo $arr['instaddress']; ?></td></tr>
<tr><td>City</td><td><?php echo $arr['instcity']; ?></td></tr>
<tr><td>State</td><td><?php echo $arr['inststate']; ?></td></tr>
<tr><td>Post Code</td><td><?php echo $arr['instpcode']; ?></td></tr>
<tr><td>Country</td><td><?php echo $arr['instcountry']; ?></td></tr>
<tr><td>Phone</td><td><?php echo $arr['instphone']; ?></td></tr>
<tr><td>Email</td><td><a href="mailto:<?php echo $arr['instemail']; ?>"><?php echo $arr['instemail']; ?></a></td></tr>
<tr><td>Contact Name:</td><td><?php echo $arr['instperson']; ?></td></tr>
</table></td></tr></table>
<?php
    }
	print_footer();
}

function form_add_inverter()
{
    print_header();
?>
<p>
Have another inverter to add? Enter its serial number here and I can add it to your account so you can monitor it from within your dashboard.
<form action="dashboard.php" method="post">
Serial Number: <input type="text" name="serial" size="16"><input type="submit" value="Add Inverter">
<input type="hidden" name="action" value="newinverter">
</form>
</p>
<?php
    print_footer();
}

function newinverter($serial)
{
    $userid=$_SESSION['userid'];
    if(strlen($serial)<9)
    {
        show_error("Invalid serial number");
    }

    /* open the database */
    $base=sqlite_open("../data/users.sq3", 0666);

    /* write the user data */
    $query = "INSERT INTO inverters (serial, custid) VALUES ('".$serial."', '".$userid."')";
    //echo "[".$query."]<br>";
    $results = sqlite_query($base, $query);
    if (!$results)
    {
        echo "Error: [".$base->lastErrorMsg()."]\n";
        $base->close();
        die();
    };
    $base->close();
    header("location: dashboard.php");
}

function view_inverter($serial)
{
    if(strlen($serial)<9)
    {
        show_error("Invalid serial number");
    }
    print_header();
    echo "<b>Status Reading</b><br>\n"; show_status($serial);
    echo "<br>\n<b>Viewing last 24 hours of inverter serial: ".$serial."</b><br><br>\n";
    echo "<img src=\"view1.php?serial=".$serial."\">";
    print_footer();
}

function my_details()
{
    $userid=$_SESSION['userid'];
	print_header();
    echo "<p><B>My Details</B></p>\n";

    $usersdb=sqlite_open("../data/users.sq3", 0666);
    $query = "SELECT * FROM users WHERE userid=".$userid."";
    $results=$usersdb->query($query);
    if(!$results)
    {
        echo "<font color=\"#ff0000\">Oops! Installer DB error. Contact the developer and let them know -Error: ".$usersdb->lastErrorMsg.".</font>\n";
        return;
    }
    $arr=$results->fetchArray();
?>
<table width="300" align="center">
<tr><td bgcolor="#000000">
<table width="100%" align="center" bgcolor="#ffffff">
<tr><td colspan=2 align=center><b><?php echo $arr['name']; ?></b></td></tr>
<tr><td width="100">Address</td><td><?php echo $arr['address']; ?></td></tr>
<tr><td>City</td><td><?php echo $arr['city']; ?></td></tr>
<tr><td>State</td><td><?php echo $arr['state']; ?></td></tr>
<tr><td>Post Code</td><td><?php echo $arr['postcode']; ?></td></tr>
<tr><td>Country</td><td><?php echo $arr['country']; ?></td></tr>
<tr><td>Phone</td><td><?php echo $arr['phone']; ?></td></tr>
<tr><td>Email</td><td><?php echo $arr['email']; ?></td></tr>
</table></td></tr></table>
<br><br>
<?php

    if($arr['installer']>0)
    {
        echo "<B>Your installer</B>\n";
        $installersdb=sqlite_open("../data/installers.sq3", 0666);
        $query = "SELECT * FROM installers WHERE instid=".$myinstaller."";
        $results=$installersdb->query($query);
        if(!$results)
        {
            echo "<font color=\"#ff0000\">Oops! Installer DB error. Contact the developer and let them know -Error: ".$installersdb->lastErrorMsg.".</font>\n";
            return;
        }
        $arr=$results->fetchArray();
?>
Your nominated installer is<br>
<table width="300" align="center">
<tr><td bgcolor="#000000">
<table width="100%" align="center" bgcolor="#ffffff">
<tr><td colspan=2 align=center><b><?php echo $arr['instname']; ?></b><br>ABN: <font size="-1"><?php echo $arr['instabn']; ?></font></td></tr>
<tr><td width="100">Address</td><td><?php echo $arr['instaddress']; ?></td></tr>
<tr><td>City</td><td><?php echo $arr['instcity']; ?></td></tr>
<tr><td>State</td><td><?php echo $arr['inststate']; ?></td></tr>
<tr><td>Post Code</td><td><?php echo $arr['instpcode']; ?></td></tr>
<tr><td>Country</td><td><?php echo $arr['instcountry']; ?></td></tr>
<tr><td>Phone</td><td><?php echo $arr['instphone']; ?></td></tr>
<tr><td>Email</td><td><a href="mailto:<?php echo $arr['instemail']; ?>"><?php echo $arr['instemail']; ?></a></td></tr>
<tr><td>Contact Name:</td><td><?php echo $arr['instperson']; ?></td></tr>
</table></td></tr></table>
<?php
    }
    print_footer();
}

$action=$_REQUEST['action'];
switch($action)
{
    case "add_inverter":
        form_add_inverter();
        break;
    case "newinverter":
        $serial=$_REQUEST['serial'];
        newinverter($serial);
        break;
    case "view_inverter":
        $serial=$_REQUEST['serial'];
        view_inverter($serial);
        break;
    case "mydetails":
        my_details();
        break;
	default:
		my_dashboard();
		break;
}
?>
