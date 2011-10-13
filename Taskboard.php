<?php

/**
 * Our main Taskboard class
 */
class Taskboard {
    //Constants that do something
    const TASK_OPEN = 0;
    const TASK_CLOSED = 1;

    /**
     * Days after the last bump in a task for it to disappear from searches
     * 
     * @var int Value
     */
    public $task_lifespan = 30;

    /**
     * PHP5 Constructor that serves no purpose
     */
    public function __construct(){
        //asdf
    }
	
	/*
	* Check who is online in a particular page.
	*/

    /**
     * Creates a new task and adds it to the database.
     * 
     * @param type $tripcode The tripcode (idk what this is)
     * @param type $title The title of the task
     * @param type $message The message the task contains
     * @param type $tags The tags this task contains
     * @return type The task id
     */
    public function createTask($tripcode, $title, $message, $tags, $imageBinary = NULL, $fileBinary = NULL){
        //Create the array we will store in the database
        $data = array(
            'created' => time(),
            'bumped' => time(),
            'title' => $title,
            'message' => $message,
            'tripcode' => $tripcode,
            'status' => $this::TASK_OPEN,
            'image' => $imageBinary,
			'imagetype' => __image_file_type_from_binary($imageBinary),
            'file' => $fileBinary
        );

        //Data types to put into the database
        $dataType = array(
            'INT',
            'INT',
            'STR',
            'STR',
            'STR',
            'INT',
			'LARGEOBJECT',
			'STR',
			'LARGEOBJECT'
        );

        //Insert the data
        $task_id = Database::insert('tasks', $data, $dataType);
        if(!$task_id) {echo " error in creating new task <br/>";return false;}


        // Create the tags
        if( isset($tags) AND !empty($tags)) {
            $sql_tags = array();
            foreach($tags as $t){
                $t = (string)$t;
                if(empty($t)) continue;

                $sql_tags = array(
                    'task_id' => $task_id,
                    'label' => $t,
                    'created' => time()
                );

                $sql_tagsType = array(
                    'INT',
                    'STR'
                );

                // TODO: These values should really be in 1 insert query
                Database::insert('tags', $sql_tags, $sql_tagsType);
            }
        }

        return $task_id;
    }

    /**
     * Finds a random task.
     * 
     * @param type $limit 
     */
    public function randomTasks($limit=50) {
        //TODO: actually put stuff in here
    }

    /**
     * Deletes a task by either ID or Tripcode
     * NOTE: This does not remove any entries that are still in the 'tags' table
     * 
     * @param type $delType
     * @param array $input 
     */
    public function delTaskBy($delType, $input=array()) {
        if(!is_array($input)) $input = array(); // Input array is always zero

        switch($delType){
            case 'Delete a post':    // $input <-- post ID
                $s_id = $input[0];
                $sql[] = "DELETE FROM tasks WHERE id = ?";
				$data[] = array( $s_id );
                $sql_data[] = array(
                    'id' => $s_id,
                );
                $sql_type[] = array(
                    'INT'
                );
				
                break;

            case 'Delete all post by trip':    // $input <-- Tripcode name ##DANGER## This will delete everything done by a poster
                $s_pass = $input[0];
                $sql[] = "DELETE FROM tasks WHERE tripcode =  ?";
				$sql_data[] = array(
                    'tripcode' => $s_pass,
                );
                $sql_type[] = array(
                    'STR'
                );
                break;

            case 'Delete single task with normal password': // $input <-- Task ID, Task Password
                $s_ID = $input[0];
                $s_pass = $input[1] ;
                $sql[] = "DELETE FROM tasks WHERE id = ? AND tripcode= ?";
				$sql_data[] = array(
                    'id' => $s_ID,
                    'tripcode' => $s_pass,
                );
                $sql_type[] = array(
                    'INT',
                    'STR'
                );
                break;
            
            default:
                echo '\n No action taken as there was an unknown delete option chosen for delTaskBy()\n';
                break;
        }
		     
			 
        try {
            foreach($sql as  $row_num => $s) {
                Database::query($s,$sql_data[$row_num],$sql_type[$row_num]);
                echo 'Delete command sent';
            }
        } catch(PDOException $e) {
            echo $e;
            echo 'Delete Operation failed, did you get your password wrong?';
        }
    }
	
	/*
	* Create a new comment
	*
	*/
    public function createComment($tripcode, $taskID, $replyID=NULL, $message, $vote=1){
        //Create the array we will store in the database
        $data = array(
			'task_id' => $taskID,
            'tripcode' => $tripcode,
			'reply_comment_id' => $replyID,
            'created' => time(),
            'message' => $message,
            'vote' => $vote
        );

        //Data types to put into the database
        $dataType = array(
            'INT',
            'STR',
            'INT',
            'INT',
            'STR',
            'INT'
        );

        //Insert the data
        $task_id = Database::insert('comments', $data, $dataType);
		
		//Bump the topic
		/*Would use this except sqlite doesnt support it... : OUTER JOIN tags ON tasks.id = tags.task_id */
        $sql = "UPDATE tasks
            SET
			bumped = ?
			WHERE id == ?
";

        try {
            $rs = Database::query($sql, array(time(),$taskID) , array("INT","INT") );
        } catch (Exception $e){
            echo $e;
        }
		
		
		
        if(!$task_id) {echo " error in creating new comment <br/>";return false;}

        return $task_id;
    }

    /**
     * Gets comments by a certain task id.
     * 
     * @param type $id The id of tje task
     */
    public function getCommentsByTaskId($id) {
        $sql = "SELECT * FROM comments WHERE task_id = " . $id." ORDER BY comments.created DESC";
        $result = Database::query($sql);
        return $result;
    }

    /**
     * Gets 1 task from a task id.
     * 
     * @param type $id
     * @return type 
     */
    public function getTaskByID($id=''){
        $sql = "SELECT DISTINCT tasks.id AS task_id, tasks.tripcode, tasks.created, tasks.bumped, tasks.title, tasks.message, tasks.imagetype FROM tasks WHERE tasks.id = $id LIMIT 1";

        try {
            $rs = Database::query($sql);
        } catch (Eception $e){
            return array();
        }

        // If something failed.. return no tasks
        if(!$rs) return array();
        return $rs;
    }
	
    /**
     * Either display an image or show a file from a task id.
     * 
     * @param type $id
     * @return type 
     */
    public function getTaskFileByID($id='',$mode='image'){
		switch($mode){
			case "image":
				$sql = "SELECT DISTINCT tasks.image, tasks.imagetype FROM tasks WHERE tasks.id = $id LIMIT 1";
				break;
			case "file":
				$sql = "SELECT DISTINCT tasks.image FROM tasks WHERE tasks.id = $id LIMIT 1";
				break;
		}

		//Input value
		$data = array(
			'id' => $id,
		);
		//Data types of query input
		$dataType = array(
			'INT',
		);
		
        try {
            $rs = Database::query($sql);
        } catch (Eception $e){
            echo "SQL ERROR! Something in the database has borked up..."; exit;
        }

        // If something failed.. return no tasks
        if(!$rs) {echo "SQL ERROR! Does the file actually exist?";exit;}
		
		$file_assoc_array = $rs[0];
		
		switch($mode){
			case "image":
				$binary = $file_assoc_array['image'];
				$mimetype = $file_assoc_array['imagetype'];
				// Set headers
				header("Cache-Control: public");
				header("Content-Type: $mimetype");
				echo $binary;
				break;
			case "file":
				$binary = $file_assoc_array['file'];
				$filename = $file_assoc_array['filename'];
				// Set headers
				header("Cache-Control: public");
				header("Content-Description: File Transfer");
				header("Content-Disposition: attachment; filename=$filename");
				header("Content-Type: application/octet-stream");
				header("Content-Transfer-Encoding: binary");
				echo $binary;
				break;
		}
		
		exit;
		}

    /**
     * Get a list of tasks (optional tag search)
     * 
     * @param array $tags
     * @param type $limit
     * @return type 
     */
    public function getTasks($tags=array(), $limit=50){
		// string into tag should be placed into an array instead.
		if (is_string($tags)) {$tags = array($tags);}
		// if not array, then make it an array
        if(!is_array($tags)) {$tags = array();}

        $sql_tag_labels = array();
        foreach($tags as $t){
            $tmp = preg_replace("/[^a-zA-Z0-9_\- ]/i", "", $t);
            if(!empty($tmp)) $sql_tag_labels[] = $tmp;
        }

        if(!empty($sql_tag_labels)){
            $sql_where_tags = "AND tags.label IN ('".implode("','", $sql_tag_labels)."')";
        } else {
            $sql_where_tags = '';
        }

        /*Would use this except sqlite doesnt support it... : OUTER JOIN tags ON tasks.id = tags.task_id */
        $sql = "SELECT DISTINCT tasks.id AS task_id, tasks.tripcode, tasks.created, tasks.bumped, tasks.title AS title, tasks.message AS message
            FROM tasks
            LEFT OUTER JOIN tags ON tasks.id = tags.task_id 
            WHERE
            tasks.status = ?
            $sql_where_tags
            AND tasks.bumped > ?
            ORDER BY tasks.bumped DESC
            LIMIT ?";

        try {
            $rs = Database::query($sql, array($this::TASK_OPEN, time() - strtotime('-'.$this->task_lifespan.' days'), $limit) , array("INT","STR","INT") );
        } catch (Exception $e){
            return array();
        }

        // If something failed.. return no tasks
        if(!$rs) return array();

        // TODO: Get the tags for each task!
        return $rs;
    }

    /**
     * Returns an array of the most frequently used tags
     * 
     * @param type $limit
     * @return type Array
     */
    public function topTags($limit=5){
        $sql = "SELECT label, COUNT(*) AS count
            FROM tags 
            GROUP BY label 
            ORDER BY count DESC 
            LIMIT ?";
        //$rs = Database::query($sql, array($limit));
        //$rs = Database::query("SELECT label, COUNT(*) as count FROM tags GROUP BY label ORDER BY count DESC LIMIT ?", array($limit) , array("INT"));
        $rs = Database::query($sql, array($limit),array("INT"));
        return $rs;
    }

    /**
     * Initializes the database.
     * 
     * @todo Clean up everything
     */
    public function initDatabase(){

        /*
        Note:
        One exception to the typelessness of SQLite is a column whose type is INTEGER PRIMARY KEY. (And you must use "INTEGER" not "INT". A column of type INT PRIMARY KEY is typeless just like any other.) INTEGER PRIMARY KEY columns must contain a 32-bit signed integer. Any attempt to insert non-integer data will result in an error.

        Hence all primary key field must be of INTEGER not INT
        */

        $sql = array();

        //MySQL borks without AUTO_INCREMENT, but sql borks with AUTO_INCREMENT. Hence we must provide two differnt table settings to provide cross compatibility.
        $dbType = Database::getDataBaseType();
        echo $dbType."<br/>";
        switch ( $dbType ){
		
			/*SQLite*/
			case "sqlite":
				$sql[] = <<<SQL
CREATE TABLE IF NOT EXISTS tasks ( 
id INTEGER NOT NULL,
tripcode VARCHAR(25),
status INTEGER ,
created INTEGER ,
bumped INTEGER ,
title VARCHAR(100),
message VARCHAR(2000),
image BLOB,
imagetype VARCHAR(100),
file BLOB,
filename VARCHAR(100),
PRIMARY KEY (id)
);
SQL;

				$sql[] = <<<SQL
CREATE TABLE IF NOT EXISTS tags ( 
task_id INTEGER,
label VARCHAR(50),
created INTEGER
);
SQL;


				//Create comments table
				$sql[] = "CREATE TABLE IF NOT EXISTS comments (
id INTEGER NOT NULL,
task_id INTEGER NOT NULL,
tripcode VARCHAR(25),
reply_comment_id INTEGER,
created INTEGER,
message TEXT,
vote INTEGER,
PRIMARY KEY (id)
);";

				//Uniqueness check (for enforcing originality
				$sql[] = "CREATE TABLE IF NOT EXISTS uniqueHash (
id INTEGER NOT NULL,
hash VARCHAR(25),
created INT
);";
				
				//Generalized 'session' holder. E.g. whos online recently.
				$sql[] = "CREATE TABLE IF NOT EXISTS miniSessionHash (
id INTEGER NOT NULL,
hash VARCHAR(25),
info VARCHAR(25),
intinfo INTEGER,
created INTEGER
);";
				break;
			/*
				SQLite END
			*/
		    
			
			/*
				MYSQL VERSION
			*/
			default:
			case "mysql":
				$sql[] = <<<SQL
CREATE TABLE IF NOT EXISTS tasks ( 
id INTEGER NOT NULL AUTO_INCREMENT,
tripcode VARCHAR(25),
status INT ,
created INT ,
bumped INT ,
title VARCHAR(100),
message VARCHAR(2000),
image BLOB,
imagetype VARCHAR(100),
file BLOB,
filename VARCHAR(100),
PRIMARY KEY (id)
);
SQL;

				$sql[] = <<<SQL
CREATE TABLE IF NOT EXISTS tags ( 
task_id INT,
label VARCHAR(50),
created INT
);
SQL;

				//Create comments table
				$sql[] = "CREATE TABLE IF NOT EXISTS comments (
id INTEGER NOT NULL AUTO_INCREMENT,
task_id INT NOT NULL,
tripcode VARCHAR(25),
reply_comment_id INT,
created INT,
message TEXT,
vote INT,
PRIMARY KEY (id)
);";

				//Uniqueness check (for enforcing originality
				$sql[] = "CREATE TABLE IF NOT EXISTS uniqueHash (
id INTEGER NOT NULL AUTO_INCREMENT,
hash VARCHAR(25),
created INT
);";
				
				//Generalized 'session' holder. E.g. whos online recently.
				$sql[] = "CREATE TABLE IF NOT EXISTS miniSessionHash (
id INTEGER NOT NULL AUTO_INCREMENT,
hash VARCHAR(25),
info VARCHAR(25),
intinfo INT,
created INT
);";
				break;
			/* END OF MYSQL VERSION*/
			


}


        foreach($sql as $s) {
            Database::query($s);
        }
		
		/*
		// hmmm... should i use prepare statements for making tables?
		foreach($sql as $s => $row_num){
			query($s, $data[$row_num],$type=array());
		}
		*/
    }
}