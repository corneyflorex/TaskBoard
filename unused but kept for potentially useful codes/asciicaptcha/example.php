<?php
include('asciicaptcha.php');

//Salt generator
if(!isset($__salt)){$__salt = sha1($_SERVER['DOCUMENT_ROOT'].$_SERVER['SERVER_SOFTWARE']);}

echo $__salt;
?>
<br />
<br />
<br />

CAPCHA FORM:
<?php
$ascii_capcha = __getCAPCHA($__salt);

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
if( __checkCAPCHA($answer,$digest,$__salt) ){
	echo "success";
}else{
	echo "fail";
}

?>


<br />
<br />
<br />
<br />
<br />
<br />


TIME TOKEN SYSTEM:
<?php
var_dump( __timeToken_CAPCHA(-10,0,'hour') );
?>


<br />
<br />
<br />
<br />


CAPCHA CORE CLASS EXAMPLE
<?php

$captcha = new ASCII_Captcha();
$data = $captcha->create($text);

echo "<b>$text</b> :<br><pre style='font-size:8px;'>$data</pre>"

?>