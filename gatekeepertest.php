<?php

require("anonregkit.php");

//	function __postGateKeeper($text,$minWordCount=0,$goodtext=""){		

if ( isset($_POST["text"]) ){
	if	( __postGateKeeper($_POST["text"]) ){
		echo "win";
		}else{
		echo "epic fail";
		}
}

?>
			<FORM action='?' method='post'>
				<P>
					<textarea class='' rows=5 name='text'></textarea>
					<br />			
					<INPUT type='submit' value='Send'> <INPUT type='reset'>
				</P>
			</FORM>