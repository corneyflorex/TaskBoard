<?php
//Initialize required files
require("settings.php");
require("Database.php");
require("Taskboard.php");
require("anonregkit.php");

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
                        break;
                    }

                    //Extract tag to array
                    $s_tag = isset($_POST['tags']) ? explode(' ', $_POST['tags']) : array();
                    //Insert password
                    if( ( isset($_POST['password']) AND $_POST['password']!='' ) OR __getKeyFile()!=''){
                        $s_pass=__tripCode($_POST['password'].__getKeyFile());
                    }else{// If user give blank password, generate a new one for them
                        $newpass = md5(mt_rand());
                        $s_pass=__tripCode($newpass);
                        echo      "<div style='background-color:white;color:black;'>Your new password is: '<bold>".$newpass."</bold>' keep it safe! </div>
                                <div style='float:left;padding:10px;background-color:#".substr(md5($s_pass),0,6)."'>".$s_pass."</div>";
                    }

                    $board->createTask($s_pass, $_POST['title'], $_POST['message'], $s_tag);
                    echo "Post submitted!\n";
                    break;

                /*
                 * Search for a task
                 */
                case 'search':
                    // If we're posting a search, redirect to the URL search (helps copy/pasting URLs)
                    if(isset($_POST['tags'])){
                        $tags = explode(' ', $_POST['tags']);
                        header('Location: ?q=/tasks/search/'.implode(',', $tags));
                        echo 'tags'.implode(',', $tags);
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
                    $s_array[0]=$_POST['taskID'];

                    $s_array[1]=__tripCode($_POST['password'].__getKeyFile());

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
        
        //Retrieve the task and get its comments
        $tasks = $board->getTaskByID($uri_parts[1]);
        $comments = $board->getCommentsByTaskId($uri_parts[1]);
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

        $tasks = $board->getTasks($tags);
        break;
        
}

//Create the layout
if(!isset($mode)) $mode = array(); //set default mode (should be error page perhaps)
$top_tags = $board->topTags(10);
require("layout.php");
            

?>