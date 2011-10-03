<?php

	// PROJECT ANON REG KIT	
	// About: This is a care package for php programmers as a starting point for
	//			creating sites which are friendly for anons to use
	// What is anon friendly? Well for starters it requires no registration.
	// USAGE: add 'require("anonregkit.php")' to any files you use, and refer to comments
	//			for more instructions
	//95CHAR############################################################################95CHAR
	// PRINTS SOURCE CODE
	// Directly accessing this .php code will only get you its source code.
	// e.g. require("anonregkit.php")
	if(count(get_included_files()) ==1) {
		header("Content-Type: text/plain; charset=utf-8");
		$break=Explode('/',$_SERVER["SCRIPT_NAME"]);
		$pfile=$break[count($break)-1];
		$fh=fopen($pfile,"rb");
		echo fread($fh,1000000);
		fclose($fh);
		exit();
	}
	//95CHAR############################################################################95CHAR


	// FUCTION:KeyFile Password Generator
	// Usage: After sending a 'keyfile'__, call $password=getKeyFile($salt), $salt is optional
	/*	//SUGGESTED FORM TEMPLATE
		<form action='?' method='post'
		enctype='multipart/form-data'>
		<label for='file'>KeyFile:</label>
		<input type='file' name='keyfile' />
		<input type='submit' name='submit' value='Submit' />
		</form>
	*/
	function __getKeyFile($fileSizeLimit=1000){
		$pass = ''; // Gives Empty String on Error
		if(!empty($_FILES['keyfile']['tmp_name'])){
			if($_FILES["keyfile"]["size"] < ($fileSizeLimit*1024)){ // $fileSizeLimit is in kb
				if ($_FILES["keyfile"]["error"] > 0){ 		// For Errors
					echo "Error: " . $_FILES["keyfile"]["error"] . "<br />";
				}else{
					$pass=file_get_contents ( $_FILES["keyfile"]["tmp_name"] );
					//Ensure minimum length for password is maintained
					//Even if an extreamly small keyfile is uploaded
					$pass=sha1($pass).md5($pass).$pass.sha1(md5($pass)).md5(sha1($pass));
				}
			}else{echo "Invalid keyfile (Too Big?)";}
		}
		return $pass;
	}
	//NOTES FOR IMPROVEMENT: http://crackstation.net/hashing-security.html
	
	//Try using crypt() instead lol
	// Usage __getHaser($username.$userpassword.__getKeyFile(),$salt)
	function __getHasher($password,$salt='Add Your Site Specific Hash Here'){
		$Hash=$password;	 									// Inital password hash.
		$salt = $salt.$Hash.sha1($Hash); 					 	// $salt should never be empty
		mt_srand ((int) (sha1($Hash)) ); 					 	// Set the psudorandom seed
		for($i=0; $i<1000;$i++){								
			$subSalt= sha1 ($salt.mt_rand().$salt.mt_rand()); 	// Obtain the next sub hashes
			$Hash=sha1( $subSalt . $Hash . $subSalt ); 			// Hash with subsalt
		}
		return $Hash;
	}
	
	// FUNCTION:TripCode Generator
	// Source: kusaba, modded by zachera, adapted and modernised by anonsec 
	// USAGE: $tripCode = __tripCode($password)
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
	
	// FUNCTION: PRETTY TRIP DISPLAY
	// Usage: just place  echo __prettyTripFormatter($tripcode); to your desired location.
	// e.g. echo __prettyTripFormatter($task['tripcode']);
	function __prettyTripFormatter($tripcode='Anonymous',$displayLimit=0,$width=80,$align='right',$link=''){
	
	// colour hashing
	$hash = md5($tripcode);
	$colorHash = substr($hash,0,6);
	$colorHash2 = substr($hash,6,6);
	if ($displayLimit>0){
		$text=substr($tripcode,0,$displayLimit);
	}else{
		$text=$tripcode;
	}
	// For link
	if ($link==''){
		$link=$tripcode;
	}
	// font colour
	$avgBrightness = ( HEXDEC(SUBSTR($colorHash,0,2))+HEXDEC(SUBSTR($colorHash,2,2))+HEXDEC(SUBSTR($colorHash,4,2)) )/3;
	if( $avgBrightness > (255/2) ){
		$fontColour = 'black';
	}else{
		$fontColour = 'white';
	}
	
	$width=$width."px";
	return "							
				<div style='
							width:$width; 
							border-radius:15px;
							border-width: 2px;
							border-style: outset;
							padding:5px;
							float:$align;
							background-color:#$colorHash;
							border-color:#$colorHash2;
							overflow:hidden;
							'>
					<div style='text-align:center;'>
					<a style=' color:$fontColour' href='#$link'>$text</a>
					</div>
				</div> 
			";
	}
?> 