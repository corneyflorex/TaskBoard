<?php
include('lock.php');
?>
<html>
<body>
<head>
<h1>Welcome <?php echo $login_session; ?></h1>
<!DOCTYPE html>
		
		
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="/../css/styles.css" type="text/css" />
		</head>
		
		
<form name="form" action="search.php" method="get">
<input type="text" name="q" />
<input type="submit" name="Submit" value="Search" />
</form>
</body>