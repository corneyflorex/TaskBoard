<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" ></meta>
<meta charset="UTF-8"/>

<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"> 

	<style type="text/css">
	    .center{
			margin-left: auto;
			margin-right: auto;
		}
		hr {color:sienna;}
		body {background-color:Ivory;}
		#wrapper, #content {
			width: auto;
			border: 0;
			margin: 0 5%;
			padding: 0;
			margin-top: 3em;
			float: none !important;
		}
		#metainfo{
		margin:0.5em
		}
		</style>
		
	<style media="screen" type="text/css">
		#pageborder{
			margin-left: auto;
			margin-right: auto;
			border-style:solid;
			border-width:1px;
			width: 210mm; min-height: 297mm;
			background-color: white;
		}
		#wrapper, #content {
			margin: 0 10%;
			margin-top: 5em;
		}
	</style>
	<style media="print" type="text/css">
		* {
		background-color: white !important;
		background-image: none !important;
		}
		body {background-color:white;}
		#docinfo{display:none;}
		#header{display:none;}
	</style>
</head>


<?php $task = $tasks[0]; //get the first displayed task entry from the array. ?>

<body >
	<div id="header">
		<form><input type="button" value=" Print this page "
		onclick="window.print();return false;" /></form> 
	</div>

	<div id="pageborder">
	<div id="metainfo">
		<span style='font-size:0.6em;' ><i><div id='OPGUID' >Created On: <?php echo substr( date('F j, Y, g:i a', $task['created']) ,0,-9);?></div></i></span>
		<span style='font-size:0.6em;' ><i><div id='DOCID'  >DocID(md5): <?php echo md5($task['message']); ?> </div></i></span>
	</div>
	<div id="content">
		
		
		<?php 
		// Grab latest image if it exist
		if($task['imagetype'] != NULL){ 
		?>
			<div style="text-align:center;margin-bottom:3em;">
				<img border="0" style="max-width:100% ; max-height:60%" src="?q=/image/<?php echo $task['task_id']; ?>" alt="Pulpit rock"/>
			</div>	
		<?php 
		}
		?>
		
		<div id="OP" class="task1">
			<?php 
				$purifier = new HTMLPurifier();
				$clean_html = $purifier->purify( Markdown($task['message']) );
			?>
			<span class="message"><?php echo nl2br($clean_html); ?></span>
		</div>

	</div>
	</div>


	<div id="docinfo">
		<hr>
		<h2>Doc Info:</h2>
		<?php echo __prettyTripFormatter($task['tripcode']);?>
		<span class="title">TITLE: <?php echo htmlentities(stripslashes($task['title']),null, 'utf-8'); ?> </span>
		<br>
		<span>Created: <?php echo date('F j, Y, g:i a', $task['created']);?></span>
		<br>
		<span style='font-size:0.6em;' ><i><div id='OPGUID' >MD5 Global ID: <?php echo md5($task['message']); ?></div></i></span>
		<br>
		<br>
		This page is rendered via markdown <a href="http://en.wikipedia.org/wiki/Markdown"> click here for more infomation about markdown</a>
		<br>
		<br>
		Share this paper:
		<br>
		<img style='width:100px' src="http://qrcode.kaywa.com/img.php?s=8&amp;d=http%3A%2F%2F<?php if(isset($_SERVER["SERVER_NAME"]) AND isset($_SERVER["REQUEST_URI"]) )echo $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];?>"></a> 
		<br>
		<a href="http://<?php if(isset($_SERVER["SERVER_NAME"]) AND isset($_SERVER["REQUEST_URI"]) )echo $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];?>">
			http://<?php if(isset($_SERVER["SERVER_NAME"]) AND isset($_SERVER["REQUEST_URI"]) )echo $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];?>
		</a> 
		<br>
		<br>
		<a href="?q=/view/<?php echo $taskid?>">back to discussion page</a> 
	</div>
	
</body>
</html>