<?php

// Deals with the annoying problem of 'get_magic_quotes_gpc' in some shared hosting
// Source: http://stackoverflow.com/questions/517008/how-to-turn-off-magic-quotes-on-shared-hosting
/*
// This appeared to not escape _POST properly (e.g. it also escape \r\n. where /r/n is how linux/UNIX sees it... 
// this means magicquote won't touch \r\n as its not "/", but strip slash would. Which is a recipe for trouble) 
if (get_magic_quotes_gpc() === 1)
{
    $_GET = json_decode(stripslashes(json_encode($_GET, JSON_HEX_APOS)), true);
    $_POST = json_decode(stripslashes(json_encode($_POST, JSON_HEX_APOS)), true);
    $_COOKIE = json_decode(stripslashes(json_encode($_COOKIE, JSON_HEX_APOS)), true);
    $_REQUEST = json_decode(stripslashes(json_encode($_REQUEST, JSON_HEX_APOS)), true);
}
*/
// This one appears to be workable? (Honestly... just disable magic_quotes_gpc )
if ( in_array( strtolower( ini_get( 'magic_quotes_gpc' ) ), array( '1', 'on' ) ) )
{
    $_POST = array_map( 'stripslashes', $_POST );
    $_GET = array_map( 'stripslashes', $_GET );
    $_COOKIE = array_map( 'stripslashes', $_COOKIE );
}


// session system to help store not yet approved 'files'
// or images, while capcha is being processed.
ini_set("session.use_cookies",0);
ini_set("session.use_only_cookies",0);
//ini_set("session.use_trans_sid",1);
session_start();

//Initialize required files
require("settings.php");
require("LayoutEngine.php");
require("Database.php");
require("Taskboard.php");
require("anonregkit.php");
//require("./asciicaptcha/asciicaptcha.php");


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
					/*
						Grab the latest photos and insert into $imageFileBinary
					*/
					$imageFileBinary = __getImageFile();
					if ($imageFileBinary == NULL) {
						if (empty($_SESSION['imageFileBinary'])) {
							$_SESSION['imageFileBinary'] = NULL;
						} 
						$imageFileBinary = $_SESSION['imageFileBinary'];
					} else {
						$_SESSION['imageFileBinary'] = $imageFileBinary;
					}
					
					/*
						Grab the latest keyfile and insert into $keyFileBinary
					*/
					$keyFileBinary = __getKeyFile();
					if ($keyFileBinary == NULL) {
						if (empty($_SESSION['keyFileBinary'])) {
							$_SESSION['keyFileBinary'] = NULL;
						} 
						$keyFileBinary = $_SESSION['keyFileBinary'];
					} else {
						$_SESSION['keyFileBinary'] = $keyFileBinary;
					}
					
                    //Only pass though message and title if it is set already
                    if(!isset($_POST['title'], $_POST['message']) || empty($_POST['title']) || empty($_POST['message'])){
                        echo "Missing title and/or message \n";
                        exit;
                    }
					
					
					// check if message is up to scratch (is not stupid, and does not have spammy words)
					if( ! __postGateKeeper($_POST['message']) ){
                        echo "Your post was rejected by the gatekeeper. Did you make your message too small? 
						Does it have too many mispelling? Or was it just plain stupid? \n";
						exit;
					};

					// Also it must pass the capcha test
					if( isset($_POST['security_code'])) {
						$first = false;
					} else {
						$_POST['security_code'] = "";
						$_SESSION['security_code'] = "";
						$first = true;
					}
					
				   if( $_SESSION['security_code'] == $_POST['security_code'] && !empty($_SESSION['security_code'] ) ) {
						echo 'Your captcha code was valid.';
						unset($_SESSION['security_code']);
				   } else {
						if ($first){
							echo 'Please enter the captcha code to confirm your human status';
						}else{
							echo 'Sorry, you have provided an invalid security code';
						}

						?>
						<br/>
						<br/>
						
						Modify Text:
							<FORM action='?<?php echo htmlspecialchars(SID); ?>&q=/tasks/submitnew' method='post' >
						Title*:<BR>		<INPUT type='text' name='title'value='<?php echo $_POST['title'];?>'><BR>	
						Message*:<br />	<textarea class='' rows=5 name='message'><?php echo $_POST['message'];?></textarea><BR>			
						Tags:<BR><INPUT type='text' name='tags' value='<?php echo $_POST['tags'];?>'><BR>

							<input type="hidden" name="taskID" value="<?php echo $_POST['taskID']; ?>"><br/>
							<INPUT type='hidden' name='keyfile' />
                            <INPUT type='hidden' name='password' value="<?php echo $_POST['password'];?>" >
							<b>CAPTCHA:</b> 
							<img src="./captcha/CaptchaSecurityImages.php?<?php echo htmlspecialchars(SID); ?>&width=100&height=40&characters=5" /><br />
							<label for="security_code">Security Code: </label><input id="security_code" name="security_code" type="text" /><br />
							<br />
							<input type="submit" value="Submit" />	
						</form>
						<?php	
						exit;
					}


                    /*
					Extract tag to array
					*/
					//preg_replace('/[^a-zA-Z0-9\s]/', '', $text) - Removes nonalphanumeric char
                    $s_tag = isset($_POST['tags']) ? preg_replace('/[^a-zA-Z0-9\s]/', '', $_POST['tags']) : "";
					// turn it into an array
					$s_tag_array_1 = explode(' ', $s_tag);
					//also extract any hashtags from the message itself
					$hashtagmatch = preg_match_all( '/#(\w+)/', $_POST['title']." ".$_POST['message'] , $pregmatch);
					if($hashtagmatch){
						 $s_tag_array_2 = $pregmatch[1];
					}else{
						$s_tag_array_2 = array();
					}
					//merge s_tag_array_1 and s_tag_array_2 to s_tag_array
					$s_tag_array = array_merge( $s_tag_array_1 , $s_tag_array_2 );
					$s_tag_array = array_unique( $s_tag_array );
									
                    //Insert password
                    if( ( isset($_POST['password']) AND $_POST['password']!='' ) OR $keyFileBinary!=NULL){
                        $s_pass=__tripCode($_POST['password'].$keyFileBinary);
                    }else{// If user give blank password, generate a new one for them
						//$newpass = md5(mt_rand());
						if($__hiddenServer){
							$newpass = substr(md5(rand()),0,6);
						} else {
							$newpass = substr(md5($_SERVER['REMOTE_ADDR']),0,6);
						}
                        $s_pass=__tripCode($newpass);
                        echo      "<div style='z-index:100;background-color:white;color:black;'>Your new password is: '<bold>".$newpass."</bold>' keep it safe! </div>";
						echo		__prettyTripFormatter($s_pass);
                    }
										
                    $newTaskID = $board->createTask($s_pass, $_POST['title'], $_POST['message'], $s_tag_array, $imageFileBinary);
                    echo "Post submitted!<br/>";
					echo "Tags:".implode(" ",$s_tag_array)."<br/>";
					echo "<a href='?q=/view/".$newTaskID."'>Click to go to your new task</a>";
					echo "<meta http-equiv='refresh' content='10; url=?q=/view/".$newTaskID."'> Refreshing in 10 sec<br/>";
					exit;
                    break;
					
                /*
                 * Submit and process the new Comment
                 */
                case 'comment':
				
					/*
						Grab the latest keyfile and insert into $keyFileBinary
					*/
					$keyFileBinary = __getKeyFile();
					if ($keyFileBinary == NULL) {
						if (empty($_SESSION['keyFileBinary'])) {
							$_SESSION['keyFileBinary'] = NULL;
						} 
						$keyFileBinary = $_SESSION['keyFileBinary'];
					} else {
						$_SESSION['keyFileBinary'] = $keyFileBinary;
					}
				
                    //Only pass though message and title if it is set already
                    if(!isset( $_POST['comment']) || empty($_POST['comment'])){
                        echo "Missing comment \n";
						echo "<a href='?q=/view/".$uri_parts[2]."'>Click to go back</a>";
						exit;
                        break;
                    }
					
					// Also it must pass the capcha test
					if( isset($_POST['security_code'])) {
						$first = false;
					} else {
						$_POST['security_code'] = "";
						$_SESSION['security_code'] = "";
						$first = true;
					}
				   if( $_SESSION['security_code'] == $_POST['security_code'] && !empty($_SESSION['security_code'] ) ) {
						echo 'Your captcha code was valid.';
						unset($_SESSION['security_code']);
				   } else {
						if ($first){
							echo 'Please enter the captcha code to confirm your human status';
						}else{
							echo 'Sorry, you have provided an invalid security code';
						}

						?>
						<br/>
						<br/>
						Modify Text:
						<form name="add_comment" action="?<?php echo htmlspecialchars(SID); ?>&q=/tasks/comment/<?php echo $_POST['taskID']; ?>" method="post" >
							<textarea id="comment" name="comment"><?php echo $_POST['comment'];?></textarea>
							<input type="hidden" name="taskID" value="<?php echo $_POST['taskID']; ?>"><br/>
							<INPUT type='hidden' name='keyfile' />
                            <INPUT type='hidden' name='password' value="<?php echo $_POST['password'];?>" >
							<b>CAPTCHA:</b> 
							<img src="./captcha/CaptchaSecurityImages.php?<?php echo htmlspecialchars(SID); ?>&width=100&height=40&characters=5" /><br />
							<label for="security_code">Security Code: </label><input id="security_code" name="security_code" type="text" /><br />
							<br />
							<input type="submit" value="Submit" />	
						</form>
						<?php	
						exit;
					}
					
					// check if message is up to scratch (is not stupid, and does not have spammy words)
					if( ! __postGateKeeper($_POST['comment']) ){
                        echo "Your post was rejected by the gatekeeper. Did you make your post too small? 
						Does it have too many mispelling? Or was it just plain stupid? \n";
						exit;
					};

                    //Insert password
                    if( ( isset($_POST['password']) AND $_POST['password']!='' ) OR $keyFileBinary!=NULL){
                        $s_pass=__tripCode($_POST['password'].$keyFileBinary);
						echo "<meta http-equiv='refresh' content='3; url=?q=/view/".$uri_parts[2]."'> Refreshing in 3 sec";
                    }else{
						// If user give blank password, generate a new one for them                  
						//$newpass = md5(mt_rand());
						if($__hiddenServer){
							$newpass = substr(md5(rand()),0,6);
						} else {
							$newpass = substr(md5($_SERVER['REMOTE_ADDR']),0,6);
						}
                        $s_pass=__tripCode($newpass);
                        echo      "<div style='z-index:100;background-color:white;color:black;'>Your new password is: '<bold>".$newpass."</bold>' keep it safe! </div>";
						echo		__prettyTripFormatter($s_pass);
                    }

					$board->createComment($s_pass, $uri_parts[2], $replyID=NULL, $_POST['comment'], 1);
                    echo "Post submitted!\n";
					echo "<a href='?q=/view/".$uri_parts[2]."'>Click to go back</a>";
					echo "<meta http-equiv='refresh' content='5; url=?q=/view/".$uri_parts[2]."'> Refreshing in 5 sec<br/>";
					exit;
                    break;

                /*
                 * Search for a task
                 */
                case 'search':
                    // If we're posting a search, redirect to the URL search (helps copy/pasting URLs)
                    if(isset($_POST['tags'])){
						$tags_string = isset($_POST['tags']) ? preg_replace('/[^a-zA-Z0-9\s]/', '', $_POST['tags']) : "";
                        $tags = explode(' ', $tags_string);
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

					if(!is_numeric($_POST['taskID'])){Echo "YOU FAIL";exit;}
                    $s_array[0]=$_POST['taskID'];
					
                    $s_array[1]=__tripCode($pass);
					
					/*
						Moderator delete
					*/
					if (array_key_exists($s_array[1],$__superModeratorByTrip)){
						$command = 'Delete a post';
						$board->delTaskBy($command,$s_array);
						break;
					}
					
					/*
					//normal password delete
					*/
					var_dump($s_array);
                    //print_r($s_array);
                    $command = 'Delete single task with normal password';
                    $board->delTaskBy($command,$s_array);
                    break;
					
            }
        }
        
        break;

    /*
     * Get Image from a task and print it out to user.
     */
    case 'image':
		$taskid =$uri_parts[1];
		
        if(!is_numeric($uri_parts[1])){Echo "YOU FAIL";exit;}
		
		// support thumbnails
        if(isset($_GET['mode'])){
			if($_GET['mode']== 'thumbnail'){
				$tasks = $board->getTaskFileByID($taskid,'thumbnail');
			}
		}
		
		
		//Retrieve the image and display it
        $tasks = $board->getTaskFileByID($taskid,'image');
        break;
		
    /*
     * Stuff relating to browsing and searching tasks
		basically we view specific task here
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
		
	case 'embed':
		
		if (isset($_GET['tags'])){
		$tags = explode(',', $_GET['tags']);
		}else{
		$tags = array();
		}
		if (isset($uri_parts[1])){
		$tags = array_merge( explode(',', $uri_parts[1]) , $tags);
		}

        //Retrieve latest comment
        $tasks = $board->getTasks($tags);
		
		?>
		<!DOCTYPE html>
		<html>
		<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="css/embed.css" type="text/css" />
		</head>
		<body>
		<div id="newTask" class="greybox">
			<?php if (!empty($tags)){?>
				<a target="_blank" href="?q=/tasks/new&tag=<?php echo $tags[0];?>">Post New</a>
			<?php } else {?>
				<a target="_blank" href="?q=/tasks/new">Post New</a>
			<?php }?>
		</div>
		<div id="taskDIV" class="tasklist">
		<?php
		echo __taskDisplay($tasks);
		?>
		</div>
		</body>
		<?php
		
		exit;
		break;

	case 'rss':
		
		if (isset($_GET['tags'])){
		$tags = explode(',', $_GET['tags']);
		}else{
		$tags = array();
		}
		
		//XML headers
		$rssfeed = '<?xml version="1.0" encoding="ISO-8859-1"?>';
		$rssfeed .= "\n";
		$rssfeed .= '<rss version="2.0">';
		$rssfeed .= "\n";
		$rssfeed .= '<channel>';
		$rssfeed .= "\n";
		$rssfeed .= '<title>TaskBoard</title>';
		//$rssfeed .= "\n";
		//$rssfeed .= '<link></link>';
		$rssfeed .= "\n";
		$rssfeed .= '<description>This is the RSS feed for TaskBoard</description>';
		$rssfeed .= "\n";
		$rssfeed .= '<language>en-us</language>';
		//$rssfeed .= "\n";
		//$rssfeed .= '<copyright></copyright>';
		$rssfeed .= "\n\n\n";

		
        //Retrieve latest comment
        $tasks = $board->getTasks($tags);

		foreach($tasks as $rowtask) {	
			// link dir detector
			$url = $_SERVER['REQUEST_URI']; //returns the current URL
			$parts = explode('/',$url);
			$linkdir = $_SERVER['SERVER_NAME'];
			for ($i = 0; $i < count($parts) - 2; $i++) {
			 $linkdir .= $parts[$i] . "/";
			}
			//RSS entry
			$rssfeed .= '<item>';
					$rssfeed .= "\n";
			$rssfeed .= '<title>(Trip:' . preg_replace('/[^a-zA-Z0-9\s]/', '', $rowtask['tripcode']).") - ".preg_replace('/[^a-zA-Z0-9\s]/', '', $rowtask['title'] ). '</title>';
					$rssfeed .= "\n";
			$rssfeed .= '<description>'.preg_replace('/[^a-zA-Z0-9\s]/', '',str_replace(array("\r\n", "\r", "\n", "\t"), ' ', htmlentities(stripslashes($rowtask['message']),null, 'utf-8')) ). '</description>';
					$rssfeed .= "\n";
			if(isset($_SERVER["SERVER_NAME"])){
				$rssfeed .= '<link>http://'.$linkdir.'?q=/view/'.$rowtask['task_id'].'</link>';
					$rssfeed .= "\n";
			}
			$rssfeed .= '<guid>' . md5($rowtask['message']) . '</guid>';
					$rssfeed .= "\n";
			$rssfeed .= '<pubDate>' . date("D, d M Y H:i:s O", $rowtask['created']) . '</pubDate>';
					$rssfeed .= "\n";
			$rssfeed .= '</item>';
					$rssfeed .= "\n\n";
		}
	 
		$rssfeed .= '</channel>';
		$rssfeed .= '</rss>';
	 
		echo $rssfeed;		

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
        } else if(isset($_GET['tags'])) {
            $tags = explode(' ', $_POST['tags']);   			
        } else {
            $tags = array();                            
        }
		
		//for tagclouds
		if(empty($tags)){
			$tagClouds = $board->tagsWeight(500);
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