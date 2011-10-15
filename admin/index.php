<!DOCTYPE html>
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="/../css/styles.css" type="text/css" />
		</head>
		<body>
<?php
include("config.php");
session_start();
if($_SERVER["REQUEST_METHOD"] == "POST")
{
// username and password sent from Form
$myusername=addslashes($_POST['username']);
$mypassword=addslashes($_POST['password']);
$password=md5($mypassword);
$sql="SELECT id FROM admin WHERE username='$myusername' and passcode='$password'";
$result=mysql_query($sql);
$row=mysql_fetch_array($result);
$active=$row['active'];
$count=mysql_num_rows($result);


// If result matched $myusername and $mypassword, table row must be 1 row
if($count==1)
{
session_register("myusername");
$_SESSION['login_user']=$myusername;

header("location: welcome.php");
}
else
{
$error="Your Login Name or Password is invalid";
}
}
?>
<center>
<table border="1">
<form action="" method="post">
<tr><td><label>UserName :</label>
<input type="text" name="username"/><br/></td></tr>
<tr><td><label>Password :</label>
<input type="password" name="password"/><br/></td></tr>
<tr><td><center><input type="submit" value=" Submit "/></center><br /><td></tr><center>
</form>
</table>
</body>