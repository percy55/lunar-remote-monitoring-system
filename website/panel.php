<?php
include("sqlite3.php");
session_start();
if(!isset($_SESSION['userid']))
{
	header("location: index.html");
}
if(!isset($_SESSION['level']))
{
	header("location: index.html");
} else {
    $level=$_SESSION['level'];
}
if($level<3)
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
<title>MyCompany Inverter Monitoring Facility - Control Panel</title>
<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
<link href="mycompany.css" type="text/css" rel="stylesheet">
</head>
<body>
<table width="630" align="center">
<tr><td align="center" bgcolor="#000000">
<table width="100%" align="center" bgcolor="#ffffff" border="0">
<tr><td align="center"><img src="mycompany.png"><br><B>Control Panel</B></td></tr>
<tr><td align="right">[<a href="panel.php">Control Panel</a>]&nbsp;[<a href="account.php?action=logout">Log out</a>]</td></tr>
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

function cpanel()
{
    print_header();
    echo "<B>Customer Map (Australia)</B><br>\n";
    ?>
<!--
<img src="http://maps.googleapis.com/maps/api/staticmap?center=-26.79086,153.13439&zoom=13&size=300x300&markers=color:black|label:L|-26.79086,153.13439&sensor=false"><br>
-->
<img src="http://maps.googleapis.com/maps/api/staticmap?center=-26.79086,153.13439&zoom=13&size=400x400&markers=color:blue|label:I|-26.79086,153.13439&sensor=false">
<br>
Search By Serial Number:<br>
<form action="panel.php" method="post">
Serial: <input type="text" name="serial" size="20"><input type="submit" value="Search">
<input type="hidden" name="action" value="search">
</form>
    <?php
    print_footer();
}

function view_inverter($serial)
{
    if(strlen($serial)<9){ show_error("Invalid serial number"); };
    print_header();

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

    echo "<b>Operational Status</b>: <font color=\"#009900\">NORMAL</font> (".$arr['recordedat'].")<br>\n";
    echo "<b>Last Reading</b><br>\n";
    var_dump($arr);
	$base->close();
    echo "\n<br>\n";
    echo "<b>Recent 24 hour reading</b><br>\n<img src=\"view1.php?serial=".$serial."\">";
    echo "<p><font size=\"+1\" color=\"#ff0000\"><b>!!! CAUTION !!!</b></font>";
    ?>
<form action="panel.php" method="post">
<input type="hidden" name="serial" value="<?php echo $serial; ?>">
<input type="hidden" name="action" value="special_administrative">
<input type="submit" value="Enable Administrative Mode">
</form></p>
    <?php
    print_footer();
}

function admin_mode($serial)
{
    if(strlen($serial)<9){ show_error("Invalid serial number"); };
    print_header();
    echo "Please wait... <font color=\"#ff0000\"><b>[ ADMINISTRATIVE MODE ACTIVATED ]</b></font><br>This will last approximately 5 minutes.\n";
    print_footer();
}

$action=$_POST['action'];
switch($action)
{
    case "search":
        $serial=$_POST['serial'];
        view_inverter($serial);
        break;
    case "special_administrative":
        $serial=$_POST['serial'];
        admin_mode($serial);
        break;
    default:
        cpanel();
        break;
}

?>
