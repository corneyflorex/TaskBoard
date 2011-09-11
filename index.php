<?php

	// Settings
	$config_str = <<<SETTINGS
[homepage]
tasks_to_show = 10

[database]
dsn = sqlite:tasks.sq3
username = 
password =
SETTINGS;


	/*
		Main app below here.. don't go changing anything!
	*/

	require("Database.php");
	require("Taskboard.php");

	$config = parse_ini_string($config_str, true);

	Database::openDatabase('rw', $config['database']['dsn'], $config['database']['username'], $config['database']['password']);
	$board = new Taskboard();

	// Insert test data if requested..
	if(isset($_GET['init'])){
		$board->initDatabase();
		$board->createTask('23r34r', 'My first', 'This could be my second.. but blahh', array('first', 'misc'));
		$board->createTask('23r34r', 'Poster needed', 'I kinda need a poster making, gotta be x y z', array('graphics', 'first'));
		$board->createTask('23r34r', 'Make me music!', 'Please.. I need music', array('music', 'misc'));
		$board->createTask('23r34r', 'something', 'something something something something something', array('misc'));
		$board->createTask('23r34r', 'website time', 'Website called google.com, itl be a search engine', array('graphics', 'technical'));
		echo "Inserted test data\n";
		exit;
	}
	

	$tags = isset($_GET['tags']) ? explode(',', $_GET['tags']) : array();
	$tasks = $board->getTasks($tags);
	$top_tags = $board->topTags(10);

	require("layout.php");