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
		readfile($pfile);
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
	
	/*
		Image MIME type detector (using magic number)
	*/
	//Source: http://stackoverflow.com/questions/2207095/get-image-mimetype-from-resource-in-php-gd
	function __image_file_type_from_binary($binary) {
		if (
			!preg_match(
				'/\A(?:(\xff\xd8\xff)|(GIF8[79]a)|(\x89PNG\x0d\x0a)|(BM)|(\x49\x49(\x2a\x00|\x00\x4a))|(FORM.{4}ILBM))/',
				$binary, $hits
			)
		) {
			//return 'application/octet-stream';
			return NULL; // Defaults to this if no matching file is detected.
			//It was chosen as sometimes people don't choose to upload an image.
		}
		static $type = array (
			1 => 'image/jpeg',
			2 => 'image/gif',
			3 => 'image/png',
			4 => 'image/x-windows-bmp',
			5 => 'image/tiff',
			6 => 'image/x-ilbm',
		);
		return $type[count($hits) - 1];
	}
	
	//When you want to get the uploaded file for insertation into database as a 'blob'
	//Source: http://www.sum-it.nl/en200319.php3
	function __getImageFile($fileSizeLimit=500){
		if(!empty($_FILES['image']['tmp_name'])){
			
			$sPhotoFileName = $_FILES['image']['name']; // get client side file name
			if ($sPhotoFileName) // file uploaded
			{	$aFileNameParts = explode(".", $sPhotoFileName);
				$sFileExtension = end($aFileNameParts); // part behind last dot
				/*
				if ($sFileExtension != "jpg"
					&& $sFileExtension != "JPEG"
					&& $sFileExtension != "JPG")
				{	die ("Choose a JPG for the image");
				}
				*/
				$nPhotoSize = $_FILES['image']['size']; // size of uploaded file
				if ($nPhotoSize == 0)
				{	die ("Sorry. The upload of $sPhotoFileName has failed.
			Search a image smaller than 100K, using the button.");
				}
				if ($nPhotoSize > $fileSizeLimit*1024)
				{	die ("Sorry.
			The file $sPhotoFileName is larger than $fileSizeLimit kb.
			Advice: reduce the photo using a drawing tool.");
				}

				// read image
				$sTempFileName = $_FILES['image']['tmp_name']; // temporary file at server side
				$oTempFile = fopen($sTempFileName, "r");
				$sBinaryImage = fread($oTempFile, fileSize($sTempFileName));

				// Try to read image
				$nOldErrorReporting = error_reporting(E_ALL & ~(E_WARNING)); // ingore warnings
				$oSourceImage = imagecreatefromstring($sBinaryImage); // try to create image
				error_reporting($nOldErrorReporting);

				if (!$oSourceImage) // error, image is not a valid jpg
				{ echo "Sorry.
			It was not possible to read image $sPhotoFileName.
			Choose another photo in JPG format.";
					return NULL;
				}else{
					return $sBinaryImage;
				}
			}
		}else{return NULL;}
	}

	
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
	function __prettyTripFormatter($tripcode='Anonymous',$link=NULL,$displayLimit=0,$width=100,$align='right'){
	
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
	if ($link==NULL){
		$link='#'.$tripcode;
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
					<a style=' color:$fontColour' href='$link' title='$tripcode'>$text</a>
					</div>
				</div> 
			";
	}
	
	/*
	Styles the text so it looks better
	*/
	function __encodeTextStyle($text) {
		$text = preg_replace('/\*([^\*]+)\*/', '<b>\1</b>', $text);
		$text = preg_replace('/_([^_]+)_/', '<i>\1</i>', $text);
		
		// makes link clickable
		$text = __makeClickableLinks($text);
		
		return $text;
    }
	
	/*
	Adds html code to make any links clickable
	 Source: http://www.webhostingtalk.com/showthread.php?t=905469
	 Source: http://www.snipe.net/2009/09/php-twitter-clickable-links/ - This one works, thanks
	*/
	function __makeClickableLinks($text) { 
		$text = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a rel=\"nofollow\" href=\"\\2\" target=\"_blank\">\\2</a>", $text);
		$text = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a rel=\"nofollow\" href=\"http://\\2\" target=\"_blank\">\\2</a>", $text);
		$text = preg_replace("/@(\w+)/", "<a rel=\"nofollow\" href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $text);
		$text = preg_replace("/#(\w+)/", "<a rel=\"nofollow\" href=\"?q=/tags/\\1\" target=\"_blank\">#\\1</a>", $text);
		$text = preg_replace("/&gt;&gt;([0-9]+)\b/i", "<a rel=\"nofollow\" href=\"#\\1\" >>>\\1</a>", $text);
		//$text = preg_replace("/\/\/(\w+)/", "<span style='color:green;font-style:bold;'>//\\1</span>", $text);
		return $text; 
	} 
	
	
	/*
	The aim of this function is to enforce ligibity of the text
	useful info http://weblogtoolscollection.com/regex/regex.php
	*/
	function __postGateKeeper($text,$minWordCount=0,$goodtext=""){		
	
		$minimum_intelligence_level = 40;
	
		$wordarray = explode(" ",$text);
		$wordcount = count($wordarray);
		$intelligence = 6;
		$stupidity = 0;

		
		/*
			Minimum word count enforcement ( 0 is infinite)
		*/
		if ( ($minWordCount != 0) && ($minWordCount > $wordcount ) ){
			return false;
		}
		
		/*
			deduct points when bad/stupid words is detected
		*/
		//add more to the list
$__string_normalwords = "about after again air all along also an and another any are around as at away back be because been before below between both but by came can come could day did different do does don't down each end even every few find first for found from get give go good great had has have he help her here him his home house how I if in into is it its just know large last left like line little long look made make man many may me men might more most Mr. must my name never new next no not now number of off old on one only or other our out over own part people place put read right said same saw say see she should show small so some something sound still such take tell than that the them then there these they thing think this those thought three through time to together too two under up us use very want water way we well went were what when where which while who why will with word work world would write year you your was able above across add against ago almost among animal answer became become began behind being better black best body book boy brought call cannot car certain change children city close cold country course cut didn't dog done door draw during early earth eat enough ever example eye face family far father feel feet fire fish five food form four front gave given got green ground group grow half hand hard heard high himself however I'll I'm idea important inside John keep kind knew known land later learn let letter life light live living making mean means money morning mother move Mrs. near night nothing once open order page paper parts perhaps picture play point ready red remember rest room run school sea second seen sentence several short shown since six slide sometime soon space States story sun sure table though today told took top toward tree try turn United until upon using usually white whole wind without yes yet young alone already although am America anything area ball beautiful beginning Bill birds blue boat bottom box bring build building built can't care carefully carried carry center check class coming common complete dark deep distance doing dry easy either else everyone everything fact fall fast felt field finally fine floor follow foot friend full game getting girl glass goes gold gone happened having heart heavy held hold horse hot hour hundred ice Indian instead itself job kept language lay least leave let's list longer low main map matter mind Miss moon mountain moving music needed notice outside past pattern person piece plant poor possible power probably problem question quickly quite rain ran real river road rock round sat scientist shall ship simple size sky slowly snow someone special stand start state stay stood stop stopped strong suddenly summer surface system taken talk tall ten that's themselves third tiny town tried voice walk warm watch weather whether wide wild winter within writing written ";
		
$__string_stupidwords = <<<STUPIDWORD
lol
omg
l33t
leet
arsehole
arsehole's
arseholes
asshole
asshole's
assholes
bullshit
bullshit's
bullshits
bullshitted
bullshitter
bullshitter's
bullshitters
bullshitting
bullshitting's
chickenshit
chickenshit's
chickenshits
cocksucker
cocksucker's
cocksuckers
cock
cunt
cunt's
cunts
fuck
fuck's
fucked
fucker
fucker's
fuckers
fuckhead
fuckhead's
fuckheads
fucking
fuckings
fucks
horseshit
horseshit's
horseshits
motherfucker
motherfucker's
motherfuckers
motherfucking
shit
shit's
shite
shite's
shites
shitfaced
shithead
shithead's
shitheads
shitload
shits
shitted
shittier
shittiest
shitting
shitting's
shitty
bugger
bugger's
buggers
crap
crap's
craped
craping
craps
dick
dick's
dicked
dickens
dicker
dickers
dicking
dicks
fart
fart's
farted
farting
farts
piss
pissed
pisser
pisses
pissing
nigger
nigger's
niggered
niggering
niggers
STUPIDWORD;
		
		$stupidwords = preg_split( "/( |\n|\r\n)/", $__string_stupidwords );
		$normalwords = preg_split( "/( |\n|\r\n)/", $__string_normalwords );

		$prevWord_array=array("","","","");
		foreach ($wordarray as $word){
			// this wordhash is case insenstive
			$word = strtolower($word);
			// strip contractions like "'s" "ing"
			$word = preg_replace('/\bo\'|y\'all\b|ain\'t\b|n\'t\b|ing\b|s\b|\'[a-z]{1,2}\b/i','',$word);
			// strip all non alphanumeric char
			$word = preg_replace('/[^a-zA-Z0-9]/i','',$word);
			
			$foundstupid = false;
			// checks for word filter matches
			foreach ($stupidwords as $stupidword){
		
				// this wordhash is case insenstive
				$stupidword = strtolower($stupidword);
				//strip contractions like "'s" "ing"
				$stupidword = preg_replace('/\bo\'|y\'all\b|ain\'t\b|n\'t\b|ing\b|s\b|\'[a-z]{1,2}\b/i','',$stupidword);
				//replace typical 'filter evasion chars'
				$stupidword = str_replace(	array('!','@','#','$','&','(','|','\/\/'),
											array('s','a','h','s','a','c','i','w'),
											$stupidword);
				//check now
				if(strtolower($stupidword==strtolower($word))){
					$stupidity += 2; 
					break;
				}

			}
			//
			//NORMAL WORDS
			foreach ($normalwords as $normalword){
		
				// this wordhash is case insenstive
				$normalword = strtolower($normalword);
				//strip contractions like "'s" "ing"
				$normalword = preg_replace('/\bo\'|y\'all\b|ain\'t\b|n\'t\b|ing\b|s\b|\'[a-z]{1,2}\b/i','',$normalword);
				//check now
				if($normalword==strtolower($word)){
					$intelligence += 3;
					break;
				}
			}
			if (strtoupper($word) == $word){
				$stupidity +=2;
				$foundstupid = true;
			}
			//check for duplicate words (if good words not found yet)
			if($foundstupid){
				foreach ($prevWord_array as $prevWord){
					if ( $word == $prevWord ){
						$stupidity +=1;
						$foundstupid = true;
						break;
					}
				}	
			}
			//increment prev words monitor
			array_push($prevWord_array, $word);
			unset($prevWord_array[0]);
			$prevWord_array = array_values($prevWord_array);
			
			if(!$foundstupid){$intelligence +=1;}
		} 
		
		/*
			now... what is a sign of intelligent posting? for now its just wordcount
		*/
		
		// prevents 'divide by zero' problem
		if ($wordcount == 0 ){$wordcount = 1;} 
		
		/*
		Intelligence Ratio calculations
		*/
		// Basically the stink of stupidity is exponental, while intelligence is gradual.
		// Makes sense I hope?
		$intelligenceRatio = ( ( pow($intelligence*(count($wordarray)/2),2) - $stupidity*count($wordarray) )  )/count($wordarray);
		
		//Print out the ratio so people know how well they did
		echo "<br/> intel rank:".$intelligenceRatio." <br/>" ;

		/*
			This parts sets minimum intelligences level to pass.
			It will fail you, if you are below the minimum intelligence rank
		*/
		if( $intelligenceRatio < $minimum_intelligence_level ) { return false;}
		/*
			Pass the post if no problem is detected by the gatekeeper.
		*/
		return true;
	}
	
	// This truncates the text while leaving whole words intact.
	/* Thanks to highstrike at gmail dot com (http://www.php.net/manual/en/function.substr.php#80247) */
	function __cut_text($value, $length)
	{    
		if(is_array($value)) list($string, $match_to) = $value;
		else { $string = $value; $match_to = $value{0}; }
	 
		$match_start = stristr($string, $match_to);
		$match_compute = strlen($string) - strlen($match_start);
	 
		if (strlen($string) > $length)
		{
			if ($match_compute < ($length - strlen($match_to)))
			{
				$pre_string = substr($string, 0, $length);
				$pos_end = strrpos($pre_string, " ");
				if($pos_end === false) $string = $pre_string."...";
				else $string = substr($pre_string, 0, $pos_end)."...";
			}
			else if ($match_compute > (strlen($string) - ($length - strlen($match_to))))
			{
				$pre_string = substr($string, (strlen($string) - ($length - strlen($match_to))));
				$pos_start = strpos($pre_string, " ");
				$string = "...".substr($pre_string, $pos_start);
				if($pos_start === false) $string = "...".$pre_string;
				else $string = "...".substr($pre_string, $pos_start);
			}
			else
			{        
				$pre_string = substr($string, ($match_compute - round(($length / 3))), $length);
				$pos_start = strpos($pre_string, " "); $pos_end = strrpos($pre_string, " ");
				$string = "...".substr($pre_string, $pos_start, $pos_end)."...";
				if($pos_start === false && $pos_end === false) $string = "...".$pre_string."...";
				else $string = "...".substr($pre_string, $pos_start, $pos_end)."...";
			}
	 
			$match_start = stristr($string, $match_to);
			$match_compute = strlen($string) - strlen($match_start);
		}
	   
		return $string;
	}
	
	//Source http://stackoverflow.com/questions/2915864/php-how-to-find-the-time-elapsed-since-a-date-time
	function __humanTiming ($time)
	{

		$time = time() - $time; // to get the time since that moment

		$tokens = array (
			31536000 => 'year',
			2592000 => 'month',
			604800 => 'week',
			86400 => 'day',
			3600 => 'hour',
			60 => 'min',
			1 => 'sec'
		);

		foreach ($tokens as $unit => $text) {
			if ($time < $unit) continue;
			$numberOfUnits = floor($time / $unit);
			return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
		}

	}

	
		

?>
