<?php
/*
	account.php
	Copyright (c) 2013 Martin COLEMAN. All rights reserved.
	Released under the BSD 2-Clause license. See LICENSE for details.
*/
include("sqlite3.php");
session_start();

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
	echo "[<a href=\"dashboard.php\">View My Dashboard</a>]";
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

function forgot_password()
{
	print_header();
	?>
	<form action="account.php" method="post">
	<table width="100%" align="center" bgcolor="#ffffff" border="0" cellpadding=4>
	<tr><td align="center" colspan="2"><br><b>Password Reset</b><br><p>Because all passwords are encrypted, it is impossible to retrieve your password. However, you can reset your password to anything you like with this form.</p><p>To reset your password, please enter your email address:<br></p></td></tr>
	<tr><td align="right" valign="top">Email Address</td><td><input type="text" name="email" size="20"></td><td></td></tr>
	<tr><td align="center" colspan="2"><input type="submit" value="Reset My Password"></td></tr>
	</table>
	<input type="hidden" name="action" value="reset_password">
	</form>
	<?php
	print_footer();
}

function reset_password($email)
{
	print_header();
	?>
	<table width="100%" align="center" bgcolor="#ffffff" border="0">
	<tr><td align="center">
	Your password is being reset. Please follow the directions in the email to reset your password.</td></tr></table>
	<?php
	print_footer();
}

function null_page()
{
	print_header();
	echo "Invalid command.";
	print_footer();
}

function show_error($error_msg)
{
	print_header();
	echo "<font size=\"+1\" color=\"#ff0000\"><B>".$error_msg."</B></font>";
	print_footer();
}

function new_member($name, $address, $city, $state, $postcode, $country, $phone, $email, $password, $serial, $monitoring, $supplier, $solar_brand, $solar_model, $solar_panels, $decision)
{
	print_header();
?>
<table width="100%" align="center" bgcolor="#ffffff" border="0">
<tr><td align="center"><br><u><b>Creating your new account</b></u><br><br>
<?php
/* open the database */
$base=sqlite_open("../data/users.sq3", 0666);

$newpassword=md5($password);

/* write the user data */
$query = "INSERT INTO users (joined, name, address, city, state, postcode, country, phone, email, password, monitoring, supplier, decision) VALUES (datetime('NOW'), '".$name."', '".$address."', '".$city."', '".$state."', '".$postcode."', '".$country."', '".$phone."', '".$email."', '".$newpassword."', '".$monitoring."', '".$supplier."', '".$decision."')";
//echo "[".$query."]<br>";
$results = sqlite_query($base, $query);
if (!$results)
{
	echo "Error: [".$base->lastErrorMsg()."]\n";
	$base->close();
	die();
};

//$customerid=sqlite_last_insert_rowid($base);
$customerid=$base->lastInsertRowID();

/* write the inverter record */
$query = "INSERT INTO inverters (serial, custid) VALUES ('".$serial."', '".$customerid."')";
//echo "[".$query."]<br>";
$results = sqlite_query($base, $query);

/* write the solar install stats */
$query = "INSERT INTO solarinstalls (solarbrand, solarmodel, solarpanels) VALUES ('".$solar_brand."', '".$solar_model."', '".$solar_panels."')";
//echo "[".$query."]<br>";
$results = sqlite_query($base, $query);
$base->close();
?>
<font size="+1" color="#00dd00">Success!</font><br>You can now log in <a href="index.html">here</a> using your email address and the password you just entered.
</td></tr>
</table>
<?php
	print_footer();
}

function login($username, $password)
{
	$check_password=md5($password);
	$base=sqlite_open("../data/users.sq3", 0666);
	$query = "SELECT userid, name, level, email FROM users WHERE email='".$username."' AND password='".$check_password."'";
    //echo $query;
	$results = $base->query($query);
	if(!$results)
	{
		show_error("Error: ".$base->lastErrorMsg);
		return;
	}
	$arr=$results->fetchArray();
	if($arr['email'] != $username)
	{
		show_error("Invalid Username or Password".$username.$password);
		return;
	} else {
		$name=$arr['name'];
		$userid=$arr['userid'];
		$level=$arr['level'];
        $_SESSION['userid']=$userid;
        $_SESSION['level']=$level;
	}
	$base->close();
	/*
	print_header();
	echo "Welcome back ".$name."! You may view your inverter dashboard <a href=\"dashboard.php\">here</a>."; 
	print_footer();
	*/
    if($level==1)
    {
        header("location: dashboard.php");
    }
    if($level==3)
    {
        header("location: panel.php");
    }
}

function logout()
{
	unset($_SESSION['userid']);
	header("location: index.html");
	/*
	print_header();
	echo "You have successfully logged out. If you want to log in again, click <a href=\"index.html\">here</a>. Otherwise, close this browser window to completely clear your session."; 
	print_footer();
	*/
}

$action=$_REQUEST['action'];
switch($action)
{
	case "forgot_password":
		forgot_password();
		break;
	case "reset_password":
		$email=$_REQUEST['email'];
		reset_password($email);
		break;
	case "view":
		if(!isset($_SESSION['userid']))
		{
			show_error("You have not signed in yet. Sign in <a href=\"index.html\">here</a>.");
			break;
		} else {
			$userid=$_SESSION['userid'];
		}
		view_account($userid);
		break;
	case "join":
		$name=$_POST['name'];
		$address=$_POST['address'];
		$city=$_POST['city'];
		$state=$_POST['state'];
		$postcode=$_POST['postcode'];
		$country=$_POST['country'];
		$phone=$_POST['phone'];
		$email=$_POST['email'];
		$password=$_POST['password'];
		$serial=$_POST['serial'];
		$monitoring=$_POST['monitoring'];
		$supplier=$_POST['supplier'];
		$solar_brand=$_POST['solar_brand'];
		$solar_model=$_POST['solar_model'];
		$solar_panels=$_POST['solar_panels'];
		$decision=$_POST['decision'];
		new_member($name, $address, $city, $state, $postcode, $country, $phone, $email, $password, $serial, $monitoring, $supplier, $solar_brand, $solar_model, $solar_panels, $decision);
		break;
	case "new_inverter":
		if(!isset($_SESSION['userid']))
		{
			show_error("You have not signed in yet. Sign in <a href=\"index.html\">here</a>.");
			break;
		}
		new_inverter_form();
		break;
	case "add_inverter":
		if(!isset($_SESSION['userid']))
		{
			show_error("You have not signed in yet. Sign in <a href=\"index.html\">here</a>.");
			break;
		}
		if(!isset($_POST['serial']))
		{
			show_error("No serial number specified. Please try again.");
			break;
		} else {
			$serial=$_POST['serial'];
		}
		if(strlen($serial)<15)
		{
			show_error("Invalid serial number. Please try again.");
			break;
		}
		add_inverter($serial);
        break;
	case "login":
		$username=$_POST['username'];
		$password=$_POST['password'];
		login($username, $password);
		break;
	case "logout":
		logout();
		break;
	default:
		null_page();
		break;
}
?>
