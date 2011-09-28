<?php	//Import settings via require("settings.php");
	// Settings
	
	
// configuration
$__debug = true; // Dev mode
	
	$settingMode = "sqlite";
	switch($settingMode){
		case "mysql":
			$dbType		= "mysql";
			$dbHost		= "localhost";
			$dbName		= "taskboard";
			$dbuser     = "root";
			$dbpass     = "";
			$dbConnection = "host=".$dbHost.";dbname=".$dbName;
			break;
		case "sqlite":
			$dbType		= "sqlite";
			$dbuser     = "";
			$dbpass     = "";
			$dbConnection = "tasks.sq3";
			break;
	}

	
	
	
	// There was a problem with using parse_ini_string, when using it with MySQL
	// Basically it borked at the string "dsn = ' mysql:host=HOSTNAME;dbname=DBNAME' " 
	// NOTE: This is fine, as long as you provide good comments so noobs dont bork it up
	// 			or maybe we should move this to settings.php so we can easily back it up?
	$config = array(
					"homepage"	=>array(
										"tasks_to_show" => 10
										)
					,
					"tasks"		=>array(
										"lifespan" => 1
										)
					,
					"database"	=>array(
										"dsn" => $dbType.":".$dbConnection
										,
										"username" =>$dbuser 
										,
										"password" =>$dbpass
										)
					);
					
	//if($__debug) var_dump($config);
	
	/*
	$config_str = <<<SETTINGS
[homepage]
tasks_to_show = 10

[tasks]
lifespan = 1

[database]
dsn = sqlite:tasks.sq3
username = 
password =
SETTINGS;
$config = parse_ini_string($config_str, true);
	*/
	
	?>