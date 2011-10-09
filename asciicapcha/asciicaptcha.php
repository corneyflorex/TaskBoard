<?php
// This ASCII Capcha was repaired to work on the newest PHP 5.3.8 VC9 (might had a few hacks to make it function though)

/**********************************************************
Simple function implementation of "ASCII CAPTCHA" class below
**********************************************************/
function __getCAPCHA($salt=""){
	// Generate ACSII capcha
	//$text is given from create($text), its the answer
	$captcha = new ASCII_Captcha();
	$data = $captcha->create($text);
	
	//Grab latest time token array, and place one token into $timetoken
	$timetoken_array = __timeToken_CAPCHA(0,0,'hour');
	$timetoken = $timetoken_array[0];
	
	/* Create hash digest */
	$secretcode = $text.$salt.$timetoken;
	//hashing function
	$digest = sha1($secretcode).md5($secretcode).sha1(md5($secretcode)).md5(sha1($secretcode));

	return array(
					"image" => $data,
					"digest" =>$digest
				);
}

function __checkCAPCHA($answer,$digest,$salt=""){
	// Grab the last 30 minutes worth of tokens
	$timetoken_array = __timeToken_CAPCHA(-3,0,'hour');

	foreach($timetoken_array as $timetoken){
		// Try to regenerate hash digest
		/* Create hash digest */
		$secretcode = $answer.$salt.$timetoken;
		//hashing function
		$digest_check = sha1($secretcode).md5($secretcode).sha1(md5($secretcode)).md5(sha1($secretcode));
		
		//check if a valid digest is found
		if($digest==$digest_check){
			return true;
		}
	}
	//non found
	return false;
}

// This spits out an array of unique Token, that changes depending on time.
// This is used to enforce time limit on capcha
function __timeToken_CAPCHA($minrange=-10,$maxrange=0,$timeunit='minute'){
	
	
	//build statement for first param of date()
	$date_format = "";
	switch($timeunit){
		case 'second':	
			$date_format .= " s ";
			
		case 'minute':	
			$date_format .= " i ";		
			
		case 'hour':
			$date_format .= " H ";	
			
		case 'day':
			$date_format .= " d ";			
			
		case 'week':
			$date_format .= " W ";			
			
		case 'month':		
			$date_format .= " F ";

		case 'year':
			$date_format .= " Y ";	
			
			break;
	}
		
	$i=0;
	for($n=$maxrange; $n>=$minrange; $n--){
	
		//to shut up the warning about no offset at a particular location
		$hasharray[$i]="";		
		// at n=1,2,3,etc... we are looking at future hashes
		// at n=-1,-2,-3,etc... we are looking at the past hashes
		
		// Put Token Into Array
		$hasharray[$i] =  date($date_format, strtotime($n.' '.$timeunit));
		$i++;
	}
	
	return $hasharray;
}

/* example.php for above functions

CAPCHA FORM:
<?php
$ascii_capcha = __getCAPCHA();

echo "<br><pre style='font-size:6px;'>".$ascii_capcha["image"]."</pre>"
?>

<FORM action='?' method='post'>
	capcha answer: <INPUT type='text' name='capcha' value=''>
	<INPUT type='hidden' name='digest' value='<?php echo $ascii_capcha["digest"]; ?>'> 
	<INPUT type='submit' value='submit'> 
</FORM>

CAPCHA AUTHENICATION:
<?php 
$answer = $_POST["capcha"];
$digest = $_POST["digest"];
if( __checkCAPCHA($answer,$digest) ){
	echo "success";
}else{
	echo "fail";
}


*/




/***********************************************************
 BELOW THIS SECTION IS THE ORIGINAL CODE FOR ASCII CAPCHA
 BY DEBUG FROM http://www.phpclasses.org/package/4544-PHP-CAPTCHA-validation-using-ASCII-art-text.html
 LICENCE: http://www.php.net/license/2_02.txt
************************************************************/

/** "example.php":
	include('asciicaptcha.php');

	$captcha = new ASCII_Captcha();
	$data = $captcha->create($text);

	echo "<b>$text</b> :<br><pre style='font-size:8px;'>$data</pre>"
*/

Class ASCII_Captcha
    {
        var $fonts;
        var $bgchar = ".";
        var $length = 6;
        var $spacing = 3;
        var $repset = array('*', "!", "~");
        var $repset_count = 10;
        var $parent;
        
        function ASCII_Captcha()
            {
                $fonts[0]['A'] = 
"   ###    
  ##&##   
 ##&&&##  
##&&&&&## 
######### 
##&&&&&## 
##&&&&&## ";
                $fonts[0]['B'] = 
"########  
##&&&&&## 
##&&&&&## 
########  
##&&&&&## 
##&&&&&## 
########  ";
                $fonts[0]['C'] = 
" ######  
##&&&&## 
##       
##       
##       
##&&&&## 
 ######  ";
                 $fonts[0]['D'] = 
"########  
##&&&&&## 
##&&&&&## 
##&&&&&## 
##&&&&&## 
##&&&&&## 
########  ";
                $fonts[0]['E'] = 
"######## 
##       
##       
######   
##       
##       
######## ";
                $fonts[0]['F'] = 
"######## 
##       
##       
######   
##       
##       
##       ";
                $fonts[0]['G'] = 
" ######   
##&&&&##  
##        
##&&&#### 
##&&&&##  
##&&&&##  
 ######   ";
                $fonts[0]['H'] = 
"##&&&&&## 
##&&&&&## 
##&&&&&## 
######### 
##&&&&&## 
##&&&&&## 
##&&&&&## ";
                $fonts[0]['I'] = 
"#### 
 ##  
 ##  
 ##  
 ##  
 ##  
#### ";
                $fonts[0]['J'] = 
"      ## 
      ## 
      ## 
      ## 
##&&&&## 
##&&&&## 
 ######  ";
                $fonts[0]['K'] = 
"##&&&&## 
##&&&##  
##&&##   
#####    
##&&##   
##&&&##  
##&&&&## ";
                $fonts[0]['L'] = 
"##       
##       
##       
##       
##       
##       
######## ";
                $fonts[0]['M'] = 
"##&&&&&## 
###&&&### 
####&#### 
##&###&## 
##&&&&&## 
##&&&&&## 
##&&&&&## ";
                $fonts[0]['N'] = 
"##&&&&## 
###&&&## 
####&&## 
##&##&## 
##&&#### 
##&&&### 
##&&&&## ";
                $fonts[0]['O'] = 
" #######  
##&&&&&## 
##&&&&&## 
##&&&&&## 
##&&&&&## 
##&&&&&## 
 #######  ";
                $fonts[0]['P'] = 
"########  
##&&&&&## 
##&&&&&## 
########  
##        
##        
##        ";
                $fonts[0]['Q'] = 
" #######  
##&&&&&## 
##&&&&&## 
##&&&&&## 
##&&##&## 
##&&&&##  
 #####&## ";
                $fonts[0]['R'] = 
"########  
##&&&&&## 
##&&&&&## 
########  
##&&&##   
##&&&&##  
##&&&&&## ";
                $fonts[0]['S'] = 
" ######  
##&&&&## 
##       
 ######  
      ## 
##&&&&## 
 ######  ";
                $fonts[0]['T'] = 
"######## 
   ##    
   ##    
   ##    
   ##    
   ##    
   ##    ";
                $fonts[0]['U'] = 
"##&&&&&## 
##&&&&&## 
##&&&&&## 
##&&&&&## 
##&&&&&## 
##&&&&&## 
 #######  ";
                $fonts[0]['V'] = 
"##&&&&&## 
##&&&&&## 
##&&&&&## 
##&&&&&## 
 ##&&&##  
  ##&##   
   ###    ";
                $fonts[0]['W'] = 
"##&&&&&&## 
##&&##&&## 
##&&##&&## 
##&&##&&## 
##&&##&&## 
##&&##&&## 
 ###&&###  ";
                $fonts[0]['X'] = 
"##&&&&&## 
 ##&&&## 
  ##&##   
   ###    
  ##&##   
 ##&&&##  
##&&&&&## ";
                $fonts[0]['Y'] = 
"##&&&&## 
 ##&&##  
  ####   
   ##    
   ##    
   ##    
   ##    ";
                $fonts[0]['Z'] = 
"######## 
     ##  
    ##   
   ##    
  ##     
 ##      
######## ";
                $fonts[0]['0'] = 
" #######  
##&&&&&## 
##&&&#&## 
##&&#&&## 
##&#&&&## 
##&&&&&## 
 #######  ";
                $fonts[0]['1'] = 
"   ##   
 ####   
   ##   
   ##   
   ##   
   ##   
 ###### ";
                $fonts[0]['2'] = 
" #######  
##&&&&&## 
       ## 
 #######  
##        
##        
######### ";
                $fonts[0]['3'] = 
" #######  
##&&&&&## 
       ## 
 #######  
       ## 
##&&&&&## 
 #######  ";
                $fonts[0]['4'] = 
"##
##&&&&##  
##&&&&##  
##&&&&##  
######### 
      ##  
      ##  ";
                $fonts[0]['5'] = 
"######## 
##       
##       
#######  
      ## 
##&&&&## 
 ######  ";
                $fonts[0]['6'] = 
" #######  
##&&&&&## 
##        
########  
##&&&&&## 
##&&&&&## 
 #######  ";
                $fonts[0]['7'] = 
"######## 
##&&&&## 
    ##   
   ##    
  ##     
  ##     
  ##     ";
                $fonts[0]['8'] = 
" #######  
##&&&&&## 
##&&&&&## 
 #######  
##&&&&&## 
##&&&&&## 
 #######  ";
                $fonts[0]['9'] = 
" #######  
##&&&&&## 
##&&&&&## 
 ######## 
       ## 
##&&&&&## 
 #######  ";

                $this->fonts = $fonts;
            }
        
        function create(&$text)
            {
                $text = $this->make($captcha);
                return $captcha;
            }
        
        function bg_distort($string)
            {
                while (substr_count($string, $this->bgchar . $this->bgchar . $this->bgchar . $this->bgchar . $this->bgchar . $this->bgchar))
                    {
                        $rep = "";
                        for ($x = 6;$x > 0;$x--)
                            {
                                $rep .= $this->random_rep();
                            };
                        $string = $this->str_replace_once($this->bgchar . $this->bgchar . $this->bgchar . $this->bgchar . $this->bgchar . $this->bgchar, $rep, $string);
                    };
                return $string;
            }
        
        function random_rep()
            {
                $repset = $this->repset;
                $repset_count = $this->repset_count;
                for (;$repset_count > 0;$repset_count--)
                    {
                        $repset[] = $this->bgchar;
                    };
                $v = $repset[rand(0, count($repset) - 1)];
                return $v;
            }
        
        function str_replace_once($search, $replace, $subject)
            {
                if (($pos = strpos($subject, $search)) !== false)
                    {
                        $ret = substr($subject, 0, $pos).$replace.substr($subject, $pos + strlen($search));
                    }
                else
                    {
                        $ret = $subject;
                    };
                return($ret);
            }
        
        function make(&$captcha)
            {
                $string = "";
                $captcha = "";
                for ($x = $this->length;$x > 0;$x--)
                    {
                        $letter_data = $this->random_letter($letter);
                        $captcha = $this->new_letter($captcha, $letter_data);
                        $string .= $letter;
                    };
                $captcha = $this->touchup($captcha);
                $captcha = $this->bg_distort($captcha);
                return $string;
            }
        
        function touchup($data)
            {
                $data = explode("\n", $data);
                $len = strlen($data[0]);
                $wrapper = "";
                for (;$len > 0;$len--)
                    {
                        $wrapper .= $this->bgchar;
                    };
                $data = implode("\n", $data);
                $data = $wrapper . "\n" . $data . "\n" . $wrapper;
                return $data;
            }
        
        function new_letter($current, $new)
            {										
                $new = $this->parse_letter($new);
                $new = explode("\n", $new);
                $current = explode("\n", $current);
                    //$current = $current!='' ? explode("\n", $current) : array_fill(0,7,'');
                foreach ($new as $n => $w)
                    {
						// See if you can make this section cleaner? (had to use a hack)
						if( array_key_exists ( $n , $current ) ){
							$current[$n] .= $w;
						}else{
							$current[$n] = "";
							$current[$n] = $w;
						}
                    };
                return implode("\n", $current);
            }
        
        function distort($letter_data)
            {
                if (rand(0, 1))
                    {
                        $rand = rand(0, count($letter_data) - 1);
                        $letter_data[$rand] = " " . $letter_data[$rand];
                    };
                return $letter_data;
            }
        
        function parse_letter($letter_data)
            {
                $letter_data = explode("\n", $letter_data);
                $letter_data = $this->distort($letter_data);
                foreach ($letter_data as $line_num => $line)
                    {
                        $letter_data[$line_num] = $this->trim_left($line, $this->bgchar);
                        $letter_data[$line_num] = $this->trim_right($letter_data[$line_num], $this->bgchar);
                        $letter_data[$line_num] = str_replace("&", $this->bgchar, $letter_data[$line_num]);
                    };
                $letter_data = $this->clean_right($letter_data, $this->bgchar);
                $letter_data = $this->wrap($letter_data, $this->bgchar);
                return implode("\n", $letter_data);
            }
        
        function random_letter(&$picked_letter)
            {
                $letters = $this->fonts[rand(0, count($this->fonts) - 1)];
                $pick = rand(0, count($letters) - 1);
                $cur = 0;
                foreach ($letters as $letter => $data)
                    {
                        if ($cur == $pick)
                            {
                                $picked_letter = $letter;
                                $return = $data;
                                break;
                            }
                        else
                            {
                                $cur++;
                            };
                    };
                return $return;
            }
        
        function str_replace_left($search, $replace, $subject)
            {
                if (($pos = @strpos($subject, $search)) !== FALSE)
                     {
                        $ret = substr($subject, 0, $pos).$replace.substr($subject, $pos + strlen($search));
                    }
                else
                    {
                        $ret = $subject;
                       };
                return $ret;
            }
        
        function str_replace_right($search, $replace, $subject)
            {
                $subject = strrev($subject);
                $search = strrev($search);
                $replace = strrev($replace);
                if (($pos = @strpos($subject, $search)) !== FALSE)
                     {
                        $ret = substr($subject, 0, $pos).$replace.substr($subject, $pos + strlen($search));
                    }
                else
                    {
                        $ret = $subject;
                       };
                   $ret = strrev($ret);
                return($ret);
            }
    
        function wrap($letters, $character)
            {
                $prefix = "";
                $quantity = $this->spacing;
                for (;$quantity > 0; $quantity--)
                    {
                        $prefix .= $character;
                    };
                foreach ($letters as $lnum => $line)
                    {
                        $letters[$lnum] = $prefix . $letters[$lnum] . $prefix;
                    };
                return $letters;
            }
    
        function clean_right($array, $char)
            {
                $longest = 0;
                foreach ($array as $line)
                    {
                        if (strlen($line) > $longest)
                            {
                                $longest = strlen($line);
                            };
                    };
                foreach ($array as $lnum => $line)
                    {
                        if (strlen($line) < $longest)
                            {
                                $append = "";
                                for ($x = $longest - strlen($line);$x > 0;$x--)
                                    {
                                        $append .= $char;
                                    };
                                $array[$lnum] .= $append;
                            };
                    };
                return $array;
            }
        
        function trim_right($string, $char)
            {
                if (preg_match('/(\\s*)$/', $string, $regs))
                    {
                        $match = $regs[1];
                        $replace = "";
                        for ($x = strlen($match);$x > 0;$x--)
                            {
                                $replace .= $char;
                            };
                        $string = $this->str_replace_right($match, $replace, $string);
                    };
                return $string;
            }
        
        function trim_left($string, $char)
            {
                if (preg_match('/^(\\s*)/', $string, $regs))
                    {
                        $match = $regs[1];
                        $replace = "";
                        for ($x = strlen($match);$x > 0;$x--)
                            {
                                $replace .= $char;
                            };
                        $string = $this->str_replace_left($match, $replace, $string);
                    };
                return $string;
            }
    };

	// PRINTS SOURCE CODE
	// Directly accessing this .php code will only get you its source code.
	if(count(get_included_files()) ==1) {
		header("Content-Type: text/plain; charset=utf-8");
		$break=Explode('/',$_SERVER["SCRIPT_NAME"]);
		$pfile=$break[count($break)-1];
		$fh=fopen($pfile,"rb");
		echo fread($fh,1000000);
		fclose($fh);
		exit();
	}