<?php
//Initialize required files
require("settings.php");
require("LayoutEngine.php");
require("Database.php");
require("Taskboard.php");
require("anonregkit.php");
require("./asciicapcha/asciicaptcha.php");

//Open up the database connection
Database::openDatabase('rw', $config['database']['dsn'], $config['database']['username'], $config['database']['password']);

//Get the desired page
$uri = isset($_GET['q']) ? $_GET['q'] : '/';
$uri_parts = explode('/', trim($uri, '/'));

//Create our Taskboard object
$board = new Taskboard();
$board->task_lifespan = $config['tasks']['lifespan'];

//Determine our task
switch($uri_parts[0]){
    /*
     * Test stuff
     */
    case 'init':
        // Insert test data if requested..
        // activate by typing ?q=/init
		if (!$__initEnable) {echo 'Permission Denied. init command disabled';exit;}
        $board->initDatabase();
        if($__debug)$board->createTask('23r34r', 'My first', 'This could be my second.. but blahh', array('first', 'misc'));
        if($__debug)$board->createTask('23r34r', 'Poster needed', 'I kinda need a poster making, gotta be x y z', array('graphics', 'first'));
        if($__debug)$board->createTask('23r34r', 'Make me music!', 'Please.. I need music', array('music', 'misc'));
        if($__debug)$board->createTask('23r34r', 'something', 'something something something something something', array('misc'));
        if($__debug)$board->createTask('23r34r', 'website time', 'Website called google.com, itl be a search engine', array('graphics', 'technical'));
        if($__debug)echo "Inserted test data\n";
        $board->createTask('Anonymous', 'Welcome To TaskBoard', 'This is the first post of TaskBoard, now try out search and submit function!', array('firstpost'));

        break;

    /*
     * Task-related stuff
     */
    case 'tasks':
        //Check if we want a task
        if (isset($uri_parts[1])) {
            switch($uri_parts[1]){
                /*
                 * Make a new task
                 */
                case 'new':
                    $mode = array('submitForm');
                    break;

                /*
                 * Submit and process the new task
                 */
                case 'submitnew':
                    //Only pass though message and title if it is set already
                    if(!isset($_POST['title'], $_POST['message']) || empty($_POST['title']) || empty($_POST['message'])){
                        echo "Missing title and/or message \n";
                        exit;
                    }
					
					// Also it must pass the capcha test
                    if(!isset($_POST['capcha'], $_POST['digest']) || empty($_POST['capcha']) || empty($_POST['digest'])){
                        echo "Missing CAPCHA Answer \n";
                        exit;
                    }
					$answer = $_POST["capcha"];
					$digest = $_POST["digest"];
					if( __checkCAPCHA($answer,$digest,$__salt) ){
						echo "Capcha Vaid</br>";
					}else{
						echo "Capcha Answer Invalid";
						exit;
					}
					
					// check if message is up to scratch (is not stupid, and does not have spammy words)
					if( ! __postGateKeeper($_POST['message']) ){
                        echo "Your post was rejected by the gatekeeper. Did you make your message too small? 
						Does it have too many mispelling? Or was it just plain stupid? \n";
						exit;
					};

                    //Extract tag to array
					//preg_replace('/[^a-zA-Z0-9\s]/', '', $text) - Removes nonalphanumeric char
                    $s_tag = isset($_POST['tags']) ? preg_replace('/[^a-zA-Z0-9\s]/', '', $_POST['tags']) : "";
					// turn it into an array
					$s_tag_array = explode(' ', $s_tag);
					
                    //Insert password
                    if( ( isset($_POST['password']) AND $_POST['password']!='' ) OR __getKeyFile()!=''){
                        $s_pass=__tripCode($_POST['password'].__getKeyFile());
						echo "<meta http-equiv='refresh' content='3; url=?q=/view/".$uri_parts[2]."'> Refreshing in 3 sec";
                    }else{// If user give blank password, generate a new one for them
						//$newpass = md5(mt_rand());
						$newpass = substr(md5($_SERVER['REMOTE_ADDR']),0,6);
                        $s_pass=__tripCode($newpass);
                        echo      "<div style='z-index:100;background-color:white;color:black;'>Your new password is: '<bold>".$newpass."</bold>' keep it safe! </div>";
						echo		__prettyTripFormatter($s_pass);
                    }

                    $newTaskID = $board->createTask($s_pass, $_POST['title'], $_POST['message'], $s_tag_array);
                    echo "Post submitted!\n";
					echo "<a href='?q=/view/".$newTaskID."'>Click to go to your new task</a>";
					exit;
                    break;
					
                /*
                 * Submit and process the new Comment
                 */
                case 'comment':
                    //Only pass though message and title if it is set already
                    if(!isset( $_POST['comment']) || empty($_POST['comment'])){
                        echo "Missing comment \n";
						echo "<a href='?q=/view/".$uri_parts[2]."'>Click to go back</a>";
						exit;
                        break;
                    }
					
					// check if message is up to scratch (is not stupid, and does not have spammy words)
					if( ! __postGateKeeper($_POST['comment']) ){
                        echo "Your post was rejected by the gatekeeper. Did you make your post too small? 
						Does it have too many mispelling? Or was it just plain stupid? \n";
						exit;
					};

                    //Insert password
                    if( ( isset($_POST['password']) AND $_POST['password']!='' ) OR __getKeyFile()!=''){
                        $s_pass=__tripCode($_POST['password'].__getKeyFile());
						echo "<meta http-equiv='refresh' content='3; url=?q=/view/".$uri_parts[2]."'> Refreshing in 3 sec";
                    }else{
						// If user give blank password, generate a new one for them                  
						//$newpass = md5(mt_rand());
						$newpass = substr(md5($_SERVER['REMOTE_ADDR']),0,6);
                        $s_pass=__tripCode($newpass);
                        echo      "<div style='z-index:100;background-color:white;color:black;'>Your new password is: '<bold>".$newpass."</bold>' keep it safe! </div>";
						echo		__prettyTripFormatter($s_pass);
                    }

					$board->createComment($s_pass, $uri_parts[2], $replyID=NULL, $_POST['comment'], 1);
                    echo "Post submitted!\n";
					echo "<a href='?q=/view/".$uri_parts[2]."'>Click to go back</a>";
					exit;
                    break;

                /*
                 * Search for a task
                 */
                case 'search':
                    // If we're posting a search, redirect to the URL search (helps copy/pasting URLs)
                    if(isset($_POST['tags'])){
                        $tags = explode(' ', $_POST['tags']);
                        header('Location: ?q=/tasks/search/'.implode(',', $tags));
                        //echo 'tags'.implode(',', $tags);
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

                /*
                 * Delete a task
                 */
                case 'delete':
					$pass = $_POST['password'].__getKeyFile();
					if ($pass == ""){
						$pass = substr(md5($_SERVER['REMOTE_ADDR']),0,6);
					}
					
                    $s_array[0]=$_POST['taskID'];

                    $s_array[1]=__tripCode($pass);

                    //print_r($s_array);
                    $command = 'Delete single task with normal password';
                    $board->delTaskBy($command,$s_array);
                    break;
					
            }
        }
        
        break;

    /*
     * Stuff relating to browsing and searching tasks
     */
    case 'view':
        $mode = array('tasksView');
		$taskid =$uri_parts[1];
        if(!is_numeric($uri_parts[1])){Echo "YOU FAIL";exit;}
        //Retrieve the task and get its comments
        $tasks = $board->getTaskByID($taskid);
        $comments = $board->getCommentsByTaskId($taskid);
        break;
		
	case 'ajaxcomments':
		/*Ajax Update Commands go here*/
		$mode = array('tasksView');
		$taskid = $_POST['taskid'];
		
        //Retrieve latest comment
        $comments = $board->getCommentsByTaskId($taskid);
		
		echo __commentDisplay($comments);
		
		exit;
		break;
		
	case 'ajaxtasks':
		/*Ajax Update Commands go here*/
		$mode = array('tasksList');
		
		$tags = explode(',', $_POST['tags']);
		
        //Retrieve latest comment
        $tasks = $board->getTasks($tags);
		
		echo __taskDisplay($tasks);
		
		exit;
		break;

    /*
     * The default thing we want to do is get tags.
     */
    default:
        
    /*
     * Get tags
     */
    case 'tags':
        // Browsing/searching the tasks
        $mode = array('tasksList');
        
        if (isset($uri_parts[1])) {
            $tags = explode(',', $uri_parts[1]);        
        } else if(isset($_POST['tags'])) {
            $tags = explode(' ', $_POST['tags']);        
        } else {
            $tags = array();                            
        }
		
		$tagslist = implode(",",$tags);

        $tasks = $board->getTasks($tags);
        break;
        
}

//Create the layout
if(!isset($mode)) $mode = array(); //set default mode (should be error page perhaps)
$top_tags = $board->topTags(10);
require("layout.php");
            

?>