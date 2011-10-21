<?php
	//$dict_sig = array("b3","ff","30","a8","5c","fe","59","6c","09","7b","ed","3a","90","c9","20","8e","0d","39","5a","69","1d","74","c8","67","e7","b9","a9","46","79","62","b6","f0","16","ad","2a","94","05","ba","9a","1a","6e","e0","2b","3c","68","1f","de","38","a3","1b","85","f1","dd","47","8f","51","fb","7c","14","3d","27","4f","81","c3","0c","6b","37","12","87","5e","c0","b8","7d","88","bf","8a","a0","ef","45","a4","52","e1","73","cc","d1","2e","cf","a6","db","57","18","07","ea","fa","82","f4","e4","25","f2","8b","e5","4c","75","86","70","d9","29","53","9b","21","c2","40","a2","d2","01","10","f6","cb","0f","3e","23","c7","35","58","71","31","55","d0","af","f5","76","fd","bd","6d","63","5f","9e","bc","d7","5d","03","7a","08","33","e3","4d","5b","eb","1c","17","e2","7e","28","77","15","13","7f","3b","ec","e6","d6","44","bb","0b","a5","ce","22","d4","60","b4","78","84","43","8d","64","3f","df","66","b5","9f","dc","6a","ca","c5","a1","72","f9","56","f8","48","b0","d8","91","0a","d3","54","e8","42","c1","32","c4","aa","26","8c","19","80","f3","96","36","83","49","99","2d","00","d5","2c","1e","4b","06","11","ee","41","9c","4e","97","b7","89","f7","a7","24","ac","95","02","b1","92","ae","c6","6f","be","ab","04","93","0e","65","50","e9","fc","da","34","cd","98","4a","9d","2f","b2","61");
//$file = file('dict.txt');
//$word = explode(",",$file[0]);

$dict_sig = file_get_contents('hashdict.txt');
$dict_sig = explode(",",$dict_sig);



if(isset($_GET["word"])){

$word = $_GET["word"];
	/*
		word hashing algo
	*/	
	// this wordhash is case insenstive
	$word = strtolower($word);
	//strip contractions like "'s" "ing"
	$word = preg_replace('/\bo\'|y\'all\b|ain\'t\b|n\'t\b|ing\b|s\b|\'[a-z]{1,2}\b/i','',$word);
	//hash step
	$wordhash = substr($word,1,1).substr($word,3,1).substr(md5($word),2,2);
	//remove troublesome '\' and other non alphanumeric char (because "\n" sometimes occour)
	$wordhash = preg_replace('/[^a-zA-Z0-9]/i','',$wordhash);
	/*
		word hashing algo
	*/
$word_sig = $wordhash;

echo "word sig:".$word_sig."<br/>";

	$found = false;
	foreach($dict_sig as $sig){
		if($sig ==	$word_sig ){
			$found = true;
			break;
		}
	}
	if ($found){
		echo "match";
	}else{
		echo "no match";
	}
}
?>
<form name="input" action="?" method="get">
word: <input type="text" name="word" />
<input type="submit" value="Submit" />
</form> 


<?php
//var_dump($dict_sig);
?>