<?php

	// Settings
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


	/*
		Main app below here.. don't go changing anything!
	*/

	require("Database.php");
	require("Taskboard.php");

	$config = parse_ini_string($config_str, true);

	Database::openDatabase('rw', $config['database']['dsn'], $config['database']['username'], $config['database']['password']);


	// Decide what we're trying to do
	$uri = isset($_GET['q']) ? $_GET['q'] : '/';
	$uri_parts = explode('/', trim($uri, '/'));


	// Since pretty muche very part of the site relies on the board, create it now
	$board = new Taskboard();
	$board->task_lifespan = $config['tasks']['lifespan'];


	// Do stuff based on on the URI sent
	// FORMAT: index.php?q=/['CASE NAME']
	switch($uri_parts[0]){
		case 'init':
			// Insert test data if requested..
			$board->initDatabase();
			$board->createTask('23r34r', 'My first', 'This could be my second.. but blahh', array('first', 'misc'));
			$board->createTask('23r34r', 'Poster needed', 'I kinda need a poster making, gotta be x y z', array('graphics', 'first'));
			$board->createTask('23r34r', 'Make me music!', 'Please.. I need music', array('music', 'misc'));
			$board->createTask('23r34r', 'something', 'something something something something something', array('misc'));
			$board->createTask('23r34r', 'website time', 'Website called google.com, itl be a search engine', array('graphics', 'technical'));
			echo "Inserted test data\n";

			break;
			
		case 'submit':
				
			//NOTE: does create task auto sanatise input? if not, then need to santise from here.
			
			//Placeholder Trip gen (until we settle on a proper system)
			//Source http://www.moparisthebest.com/smf/index.php?topic=439049.0
			function __tripCode($password){
					$password = mb_convert_encoding($password,'SJIS','UTF-8');
					$password = str_replace(
							array( '&',     '"',      "'",     '<',    '>'    ),
							array( '&amp;', '&quot;', '&#38;#39;', '&lt;', '&gt;' ),
							$password
					);
					$salt = substr($password.'H.',1,2);
					$salt = preg_replace('/[^.\/0-9:;<=>?@A-Z\[\\\]\^_`a-z]/','.',$salt);
					$salt = strtr($salt,':;<=>?@[\]^_`','ABCDEFGabcdef');
					return substr(crypt($password,$salt),-10);
			}
			
			// Submit a new post
			//TAGS
			$tags_array = explode(' ', $_POST['tags']);	
			//Posting to database
			$board->initDatabase();
			$board->createTask(__tripCode($_POST['pass']), $_POST['title'], $_POST['message'], tags_array);
			echo "Post submitted!\n";
			break;
			

		default:
		case 'tags':
			$tags = isset($uri_parts[1]) ? explode(',', $uri_parts[1]) : array();
			$tasks = $board->getTasks($tags);
			$top_tags = $board->topTags(10);
			require("layout.php");
			break;
	}