<?php

class Database {
	/*
	   * Static methods
	   *
	*/

	static $pool = array('r'=>null, 'w'=>null);
	static $db;
	static $table_prefix = "";

	static function openDatabase($mode='r', $dsn=null, $username=null, $password=null){
		// Is this a correct mode? (read/write)
		if(!in_array($mode, array('r', 'w', 'rw', 'wr'))) return false;
		
		try {
			$tmp = new PDO($dsn, $username, $password);
			
			$tmp->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			// Put the connection in its relevant pool
			if($mode=='rw' || $mode='wr'){
				self::$pool['r'] = self::$pool['w'] = $tmp;
			} else {
				self::$pool[$mode] = $tmp;
			}
			
		} catch(PDOException $e){
			var_dump($e); exit;
			$tmp = false;
		}
		
		return $tmp;
	}
	
	static function switchDatabase($mode){
		self::$db = self::$pool[$mode];
	}
	
	
	
	


	static function query($sql, $data=array(),$type=array()){
		$cmd = strtoupper(strtok($sql, ' '));
		$updates = array('UPDATE', 'INSERT', 'CREATE', 'DELETE', 'DROP', 'GRANT');
		if(in_array($cmd, $updates)){
			self::switchDatabase('w');
            $write = true;
		} else {
			self::switchDatabase('r');
            $write = false;
		}
		
		
		// Are we connected to a database?
		if(!self::$db) return false;

		// Add the table prefixes, if any
		$sql_parsed = str_replace('tbl:', self::$table_prefix, $sql);
		
		#try {
			
            $stmt = self::$db->prepare($sql_parsed);
			
			//We must set each value as 'execute($data)' does not work
			// this is since it always place 'quotes' around each value
			// hence causes error in cases like "LIMIT ?" -> "LIMIT '1'" (which fails in mysql)
			$i_data=0;
			foreach($data as $value){
				if(!empty($type[$i_data])){
					switch($type[$i_data]){
						case 'INT':
							$valueType=PDO::PARAM_INT;
							break;
						case 'BOOL':
							$valueType=PDO::PARAM_BOOL;
							break;
						default:
						case 'STR':
							$valueType=PDO::PARAM_STR;
							break;
					}
				}else{$valueType=PDO::PARAM_STR;}
				$stmt->bindValue($i_data+1, $value, $valueType);
				$i_data++;			
			}
			
			$stmt->execute();
			
            //var_dump($sql_parsed);
			//$stmt->execute($data);
            
            if($write){
                $rs = true;
            } else {
                $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
			
			//$rs = $rs->fetchAll();
		#} catch (Exception $e){
		#	return false;
		#}

		return $rs;
	}


	/*
	   * $table: Table name to insert into
	   * $data: Array of rows to be inserted.
	   * If inserted rows > 1, return true on success. insert_id if 1 inserted row.
	   * Returns false on error.
	*/
	static function insert($table, $data, $type=array() ){
		// Make sure we actually have data to insert
		if(!is_array($data)) return false;
		if(count($data) == 0) return true;
        
		// If only one row has been specified, make sure the data is set as a row in an array
		if(!isset($data[0]) || !is_array($data[0])) $data = array($data);
        
        
		self::switchDatabase('w');
		// More than 1 insert? Start a transaction to speed it up
		self::$db->beginTransaction();
        
		// Build the statement
        $table = str_replace('tbl:', self::$table_prefix, $table);
		$sql = "INSERT INTO $table (`".join('`, `', array_keys($data[0]))."`) VALUES ";
		$insert_row = array_fill(0, count($data[0]), '?');
		$insert_row = '('.join(', ', $insert_row).')';
		$insert_rows = array_fill(0, count($data), $insert_row);
		$sql .= join(', ', $insert_rows);
		
				//var_dump($sql);
				//var_dump($data);
				//var_dump($type);
				
		try {
			/*echo $sql;
			echo "<pre>";
			print_r($data[0]);
			exit;*/
			$stmt = self::$db->prepare($sql);
			
			//We must set each value as 'execute($data)' does not work
			// this is since it always place 'quotes' around each value
			// hence causes error in cases like "LIMIT ?" -> "LIMIT '1'" (which fails in mysql)
			$i_data=0;
			foreach(array_values($data[0]) as $value){
				if(!empty($type[$i_data])){
					switch($type[$i_data]){
						case 'INT':
							$valueType=PDO::PARAM_INT;
							break;
						case 'BOOL':
							$valueType=PDO::PARAM_BOOL;
							break;
						default:
						case 'STR':
							$valueType=PDO::PARAM_STR;
							break;
					}
				}else{$valueType=PDO::PARAM_STR;}
				$stmt->bindValue($i_data+1, $value, $valueType);
				$i_data++;			
			}
			//echo "loop".$i_data."times";
			
			$stmt->execute();
			
			//$stmt->execute(array_values($data[0]));
			
            $ret = self::$db->lastInsertId();
			self::$db->commit();
			
		} catch(PDOException $e){
		
			echo $e."<br/>";
		
			self::$db->rollBack();
			$ret = false;
		}
		
		self::switchDatabase('r');
		
		return $ret;
	}


	static function escape($input){
		if(!self::$db) return $input;
		
		$ret = self::$db->quote($input);
		return $ret;
	}
	
	static function getDataBaseType(){
		// makes sure $db is set to read
		self::switchDatabase('r');
		//check that $db actually exist
		if(self::$db){
			//If exist, then show the attribute
			return self::$db->getAttribute(PDO::ATTR_DRIVER_NAME);
		}else{
			return false;
		}
	}

}
