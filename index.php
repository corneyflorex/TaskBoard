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


	// Define a few functions
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
			// activate by typing ?q=/init
			$board->initDatabase();
			$board->createTask('23r34r', 'My first', 'This could be my second.. but blahh', array('first', 'misc'));
			$board->createTask('23r34r', 'Poster needed', 'I kinda need a poster making, gotta be x y z', array('graphics', 'first'));
			$board->createTask('23r34r', 'Make me music!', 'Please.. I need music', array('music', 'misc'));
			$board->createTask('23r34r', 'something', 'something something something something something', array('misc'));
			$board->createTask('23r34r', 'website time', 'Website called google.com, itl be a search engine', array('graphics', 'technical'));
			echo "Inserted test data\n";

			break;
			
			
			
		case 'tasks':
			
			$sub_act = isset($uri_parts[1]) ? $uri_parts[1] : '';
			if($sub_act){
				switch($uri_parts[1]){
					case 'new':
						//mode (what to display in layout.php)
						$mode = array('submitForm');
						break;

					case 'submitnew':
						//Only pass though message and title if it is set already
						if(!isset($_POST['title'], $_POST['message']) || empty($_POST['title']) || empty($_POST['message'])){
							echo "Missing title and/or message \n";
							break;
						}

						//Extract tag to array
						$s_tag = isset($_POST['tags']) ? explode(' ', $_POST['tags']) : array();
						$s_pass = isset($_POST['password']) ? __tripCode($_POST['password']) : 'Anonymous';

						$board->createTask($s_pass, $_POST['title'], $_POST['message'], $s_tag);
						echo "Post submitted!\n";
						break;
					

					case 'search':
						// If we're posting a search, redirect to the URL search (helps copy/pasting URLs)
						if(isset($_POST['tags'])){
							$tags = explode(' ', $_POST['tags']);
							header('Location: ?q=/tags/search/'.implode(',', $tags));
							exit;
						}
						if(isset($uri_parts[2])){
							$tags = explode(',', $uri_parts[2]);
							$mode = array('tasksList');
						} else {
							$mode = array('tagSearch');
						}
						if(!empty($tags)){
							$tasks = $board->getTasks($tags);
						} else {
							$tasks = array();
						}
						break;
						
					case 'delete':
						$s_array[0]=$_POST['taskID'];
						$s_array[1]=$_POST['password'];
						print_r($s_array);
						$command = 'Delete single task with normal password';
						$board->delTaskBy($command,$s_array);
						break;

				}//							//end of:	switch($uri_parts[1]){
			}//								//end of:	if($sub_act){
			break;//						//End of:	case 'tasks':

		case 'view':
			// Browsing/searching the tasks
			$mode = array('tasksView');					//mode (what to display in layout.php)
			$tasks = $board->getTaskByID($uri_parts[1]);			//Retrieve a task entry by id
			break;
			
		default:
		case 'tags':
			// Browsing/searching the tasks
			$mode = array('tasksList');					//mode (what to display in layout.php)
						
			if(isset($uri_parts[1])){					//Standard search via "/tags/NAMEOFTAG"
				$tags = explode(',', $uri_parts[1]);		
			}else if(isset($_POST['tags'])){ 			//OVERRIDE IF SEARCHING FOR TAGS VIA $_POST
				$tags = explode(' ', $_POST['tags']);		
			}else{										//If we are simply in the front page.
				$tags = array();							
			}
			
			$tasks = $board->getTasks($tags);
			
			break;
	}
			//Display Layout
			if(!isset($mode)) $mode = array(); //set default mode (should be error page perhaps)
			$top_tags = $board->topTags(10);
			require("layout.php");