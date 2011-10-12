<?php
$test_array = array();

$file_array = file("dictionary.txt");

//$file_array = file("dictionary2.txt"); $file_array = explode(",",$file_array[0]);

foreach ($file_array as $word){
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

	$dict_sig = $wordhash;
	array_push($test_array,$dict_sig);
}

//http://answers.yahoo.com/question/index?qid=20071107135746AA64EXq
/*
$count = array_count_values($test_array);
foreach($count as $key => $value){
	echo 'the value '.$key.' occurs in the array '.$value.' times.<br/>';
}
*/
$before = count($test_array);
$unique_array = array_unique($test_array);

sort($unique_array);

$stringData="";
$stringData .= "<?php \$dict_sig = array(\"";
foreach($unique_array as $word){
$stringData .= $word."\",\"";
}
$stringData .= "\");";


var_dump( $stringData);

$myFile = "hashdict.php";
$fh = fopen($myFile, 'w') or die("can't open file");
fwrite($fh, $stringData);
fclose($fh);

/*
foreach(array_unique($test_array) as $key){
	echo '"'.$key.'",';
}
*/

$diff =  $before - count($unique_array);
echo "Before: $before After: ".count($unique_array)." reduction by: $diff";

