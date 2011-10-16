<?php
	// To show the html source 
	if( isset( $_GET['source']) ) {
		header("Content-Type: text/plain; charset=utf-8");
	}else{ ?>
	<a href="?url=<?php echo $_GET['url'] ;?>&amp;source=getsource">Click Here to obtain the embed code</a>
<?php } ?>



<p> 1a - This is an embed of the current page you are looking at:</p> 

	<!-- Standard View Of the currently viewed page via IFRAME -->
	<iframe style="border:0px" src="<?php echo $_GET['url'] ?>" width="250px" height="400px">
	  <p>Your browser does not support iframes.</p>
	</iframe>
	<!-- Standard View Of the currently viewed page via IFRAME -->


<hr>

<p> 2a -  This is an embed of the site in a more compact form:</p> 
<!--

	To take advantage of the this function, try adding a tag after "/embed/" in the 'src' url. e.g. "/embed/tag1"
	This will result in a list filtered by tag. 
	
	You could also do more than one tag by doing this "/embed/tag1+tag2+tag3"

-->

	<!-- Compact view via tag -->
	<iframe style="border:0px" src="http://<?php if(isset($_SERVER["SERVER_NAME"]) AND isset($_SERVER['PHP_SELF']) )echo $_SERVER["SERVER_NAME"].dirname($_SERVER['PHP_SELF']);?>/index.php?q=/embed/" width="250px" height="400px">
	  <p>Your browser does not support iframes.</p>
	</iframe>
	<!-- Compact view via tag -->

	
<hr>

Same as above example, but with QR Code for web aware mobile phones

<hr>
<br/>

<p>1b:</p>

<!-- std view with QR Code-->
<div style="width:250px">
	<img border="0" src="http://qrcode.kaywa.com/img.php?s=8&amp;d=<?php echo $_GET['url'] ;?>" alt="Scan Me" width="200px"/>
	<!-- Standard View Of the currently viewed page via IFRAME -->
	<iframe style="border:0px" src="<?php echo $_GET['url'] ?>" width="200px" height="400px">
	  <p>Your browser does not support iframes.</p>
	</iframe>
	<!-- Standard View Of the currently viewed page via IFRAME -->
</div>
<!-- std view with QR Code-->


<hr>
<p>2b:</p>
<!-- 
	Remember to customise the QRcode URL by modifying the "d=" var if you are changing the 'src' var below (when adding 'tags')
-->

<!-- Compact view via tag - With QR Code Support -->
<div style="width:250px">
	<img border="0" src="http://qrcode.kaywa.com/img.php?s=8&amp;d=http://<?php if(isset($_SERVER["SERVER_NAME"]) AND isset($_SERVER['PHP_SELF']) )echo $_SERVER["SERVER_NAME"].dirname($_SERVER['PHP_SELF']);?>/index.php?q=/embed/" alt="Scan Me" width="200px"/>
	<!-- Standard View Of the currently viewed page via IFRAME -->
	<iframe style="border:0px" src="http://<?php if(isset($_SERVER["SERVER_NAME"]) AND isset($_SERVER['PHP_SELF']) )echo $_SERVER["SERVER_NAME"].dirname($_SERVER['PHP_SELF']);?>/index.php?q=/embed/" width="200px" height="400px">
	  <p>Your browser does not support iframes.</p>
	</iframe>
	<!-- Standard View Of the currently viewed page via IFRAME -->
</div>
<!-- Compact view via tag - With QR Code Support -->
