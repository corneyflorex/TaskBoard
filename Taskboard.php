<?php

	class Taskboard {
		const TASK_OPEN = 0;
		const TASK_CLOSED = 1;

		public $task_lifespan = 30;		// Days after the last bump in a task for it to disapear from searches

		public function __construct(){
		}


		public function createTask($tripcode, $title, $message, $tags){
			// Create the task
			$data = array(
				'created' => time(),
				'bumped' => time(),
				'title' => $title,
				'message' => $message,
				'tripcode' => $tripcode,
				'status' => $this::TASK_OPEN
			);

			$task_id = Database::insert('tasks', $data);
			if(!$task_id) return false;


			// Create the tags
			$sql_tags = array();
			foreach($tags as $t){
				$t = (string)$t;
				if(empty($t)) continue;

				$sql_tags = array(
					'label' => $t,
					'task_id' => $task_id
				);
				// TODO: These values should really be in 1 insert query
				Database::insert('tags', $sql_tags);
			}
			
			return $task_id;
		}




		public function randomTasks($limit=50){
			
		}



		public function getTasks($tags=array(), $limit=50){
			if(!is_array($tags)) $tags = array();

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
			
			$sql = <<<SQL
SELECT
	DISTINCT tasks.id AS task_id, tasks.tripcode, tasks.created, tasks.bumped, tasks.title, tasks.message
FROM tasks
INNER JOIN tags ON tasks.id = tags.task_id
WHERE
	tasks.status = ?
	$sql_where_tags
	AND tasks.bumped > ?

ORDER BY tasks.bumped DESC
LIMIT ?
SQL;
			
			try {
				$rs = Database::query($sql, array($this::TASK_OPEN, time() - strtotime('-'.$this->task_lifespan.' days'), $limit));
			} catch (Eception $e){
				return array();
			}
			
			// If something failed.. return no tasks
			if(!$rs) return array();

			// TODO: Get the tags for each task!

			return $rs;
		}




		public function topTags($limit=5){
			$rs = Database::query("SELECT label, COUNT(*) as count FROM tags GROUP BY label ORDER BY count DESC LIMIT ?", array($limit));
			return $rs;
		}




		public function initDatabase(){
			$sql = array();
			$sql[] = <<<SQL
CREATE TABLE IF NOT EXISTS tasks ( 
	id INTEGER PRIMARY KEY,
	tripcode VARCHAR(25),
	status INTEGER ,
	created INTEGER ,
	bumped INTEGER ,
	title VARCHAR(100),
	message TEXT
);
SQL;
			
			$sql[] = <<<SQL
CREATE TABLE IF NOT EXISTS tags ( 
	task_id INTEGER,
	label VARCHAR(50)
);
SQL;
			
			$sql[] = <<<SQL
CREATE TABLE IF NOT EXISTS messages (
	id INTEGER PRIMARY KEY,
	task_id NOT NULL,
	user_id INTEGER,
	created INT,
	msg_type VARCHAR(25),    
	title VARCHAR(25),
	message VARCHAR(25)
);
SQL;
			
			foreach($sql as $s){
				Database::query($s);
			}
		}
	}