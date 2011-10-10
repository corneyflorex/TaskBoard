<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title> TaskBoard</title>
<link rel="stylesheet" media="screen and (min-width: 480px)" href="css/styles.css" type="text/css" />
<link rel="stylesheet" media="screen and (max-width: 480px)" href="css/mobile.css" type="text/css" />

<script type="text/javascript" >


// Autoupdate
	// Global Tracker Vars
	//prev content
	prev_content = "";
	//number of tries
	tries = 0;
	
function autoUpdate(){
	var xmlhttp;
	if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
	  xmlhttp=new XMLHttpRequest();
	} else {// code for IE6, IE5
	  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	

	
	<?php 
	if ( in_array("tasksView", $mode) or in_array("tasksList", $mode) ) { 
		
		if ( in_array("tasksView", $mode) ){
			$DivLoc = "commentDIV";
		} else if ( in_array("tasksList", $mode) ){
			$DivLoc = "taskDIV";
		}
	
	?>
		
	
		// Function to run on receive.
		xmlhttp.onreadystatechange=function() {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200){
					if(prev_content != xmlhttp.responseText){
						document.getElementById("<?php echo $DivLoc ?>").innerHTML=xmlhttp.responseText;
						// save new content to track it
						prev_content = xmlhttp.responseText;
						// track more often
						t=setTimeout('autoUpdate()',1000*5);
						tries = 0;
					} else {
						tries ++;
						document.getElementById("stopAutoUpdateButton").innerHTML = "Refresh Now - tries:"+tries;
						if (tries>60){
							t=setTimeout('autoUpdate()',1000*60*5);
						} else if (tries>30) {
							t=setTimeout('autoUpdate()',1000*30);
						} else {							
							t=setTimeout('autoUpdate()',1000*10);
						}
					}
			}
		}
		
		<?php
		if ( in_array("tasksView", $mode) ){
		?>
			xmlhttp.open("POST","?q=/ajaxcomments/",true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			randomLargeNumber=Math.floor(Math.random()*10000000);
			xmlhttp.send("taskid=<?php echo $taskid; ?>&sid="+Math.random());		
		<?php
		} else if ( in_array("tasksList", $mode) ){
		?>
			xmlhttp.open("POST","?q=/ajaxtasks/",true);
			xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			randomLargeNumber=Math.floor(Math.random()*10000000);
			xmlhttp.send("tags=<?php echo $tagslist; ?>&sid="+Math.random());
		<?php
		}
		?>

	<?php } ?>
}


// Time and date in local and UTC
function startTime(){
	dateObject=new Date();

	//[local to UTC offset(minutes) -> converted to msec] + [msec since Jan 1 1970 (locally)]
	local = dateObject.getTime();
	utc =  dateObject.getTimezoneOffset()*60*1000 + dateObject.getTime();

	//milisec to string
	utctime = new Date(utc);
	localtime = new Date(local);

	//Update the clock display
	document.getElementById('utcDate').innerHTML= 
													"<b>UTC DATE: </b>"+utctime.toLocaleDateString();
	document.getElementById('utcTime').innerHTML= 
													"<b>UTC TIME: </b>"+utctime.toLocaleTimeString();
	document.getElementById('localTime').innerHTML=
													"<b>CUR TIME: </b>"+localtime.toLocaleTimeString();
	t=setTimeout('startTime()',500);
}
</script>

</head>

<body onload="startTime();autoUpdate();">
	<!--THIS IS THE BACKGROUND SVG DO NOT REMOVE-->
	<div id="svgBackground">
		<svg xmlns="http://www.w3.org/2000/svg" version="1.1"  viewBox="-2794 0 3818 1880" style="width:100%; height:100%; position:absolute; top:0px; left:0px; z-index:-1;" >
			<defs>
				<filter id="f1" x="0" y="0">
					<feGaussianBlur in="SourceGraphic" stdDeviation="0.2" />
				</filter>
			</defs>  
			<g>
				<path style="fill:#0D001A;stroke:#66FFFF;stroke-width:3" filter="url(#f1)" d="M814 679l-76 32 -19 -23 69 -32 26 23zm-104 59l-59 96 -95 49 -27 -32 95 -53 32 -93 54 33zm-14 204l-31 23 -28 -87 19 5 40 59zm51 378l-191 -40 -6 -68 179 45 18 63zm272 370l-67 82 -32 -4 -9 -47 83 -90 -34 -51 19 -28 32 47 8 91zm-514 -488l-36 63 -31 -4 31 -72 36 13zm-67 -13l-38 72 -68 -18 5 -54 59 -59 42 59zm586 -702l-127 14 4 45 -76 100 -15 -91 42 -54 -146 14 -86 45 -11 37 61 -14 4 86 -41 4 -13 42 -83 55 13 36 -53 36 -19 -63 -36 19 9 127 -155 90 41 60 -68 40 -28 -53 -14 4 14 59 55 36 -19 19 -36 -10 9 28 32 4 18 55 83 31 4 28 -95 -17 -133 -146 55 4 -78 -169 0 -46 -22 0 -10 42 -76 59 0 55 -60 17 -37 -104 -26 -8 4 -61 -219 -40 36 59 37 -13 41 40 -41 51 -96 36 -100 -192 -27 0 -4 37 67 136 51 38 78 -6 -141 205 17 18 6 68 -42 32 -5 51 -109 132 -87 -5 -77 -210 24 -32 0 -54 -65 -77 27 -18 -63 -41 -127 -4 -74 -65 -4 -140 164 -142 91 -8 23 26 -5 23 73 23 36 -32 28 23 99 5 19 -19 0 -55 -41 55 -54 -73 9 73 -51 -37 -9 -54 -61 -71 -11 12 49 68 -32 45 -13 -54 -55 -41 -31 28 -19 -10 -104 97 -37 -102 73 5 13 -32 -27 -28 46 -36 27 -4 51 -46 -14 -36 27 -13 18 27 92 -5 4 -26 100 -38 -18 -22 -63 22 -19 -40 46 -36 -27 -28 -59 77 21 18 -4 47 -59 17 -14 -36 -63 8 4 -54 104 -140 115 -28 172 40 -31 47 -68 -6 45 46 132 -87 87 0 -59 -67 68 -70 95 -40 14 32 -90 32 -51 54 104 64 -17 -32 63 -55 114 28 17 -36 206 -78 141 31 -104 60 323 33 55 -55 26 49 156 14 45 24 127 -15 0 173zm-227 1021l-78 145 -63 -5 -123 -91 -4 23 -151 31 18 -63 -32 -13 0 -78 197 -127 90 59 5 -50 27 -4 114 173zm-1604 -921l-4 38 -64 0 20 -38 -42 -45 31 -32 19 45 40 32zm-127 -172l-82 59 -49 -73 91 -23 40 37zm258 394l-28 -10 17 -27 11 37zm-276 -727l-54 46 -10 114 -82 127 -131 40 -32 78 -59 -4 -68 -105 -27 -141 -55 -41 -60 19 -31 -42 99 -123 410 -50 100 82zm65 520l-28 31 -31 -28 26 -45 33 42zm605 756l-41 131 -59 -41 17 -59 59 -46 24 15zm-1294 -402l-36 -18 0 -32 36 50zm133 87l-23 9 -114 -41 8 -23 129 55zm-160 -68l-9 28 -70 -15 7 -34 72 21zm428 318l-36 42 -10 82 -277 296c10,17 13,37 9,59l-36 36 32 40 -28 32 -72 -81 0 -132 45 -260 -60 -28 -63 -158 27 -41 0 -46 -63 0 -28 -55 -36 4 -128 -72 -41 -50 -50 0 -137 -182 6 -91 -47 -68 -31 9 -23 -46 27 4 -68 -72 -90 -14 -220 109 78 -76 -55 -42 -23 -155 91 -46 437 46 -82 -59 19 -78 437 -167 169 8 -150 169 199 163 -63 68 -119 -21 15 -74 -42 0 -18 51 13 44 -90 -8 -47 63 33 27 64 19 59 68 22 -68 -13 -14 0 -68 141 23 114 137 -160 91 0 49 -28 -4 -27 27 6 36 -65 55 19 60 -32 0 -27 -41 -100 0 -38 68 38 36 17 0 36 -22 24 13 -9 51 36 4 9 55 65 0 63 -27 45 17 85 3 6 56 69 0 8 65 47 0 122 81zm-1411 -869l-137 -4 -32 13 36 36 -75 18 0 -173 185 74 23 36zm191 579l-18 32 -63 -42 -42 22 -100 -59 9 -27 114 46 44 -23 56 51zm-255 -106l-50 32 -27 -59 77 27zm514 588l-27 27 -41 -27 28 -28 40 28zm-72 -41l-32 18 -42 -28 32 -40 42 50zm2050 -698l-5 -37 -23 3 3 37 25 -3zm-82 59l-30 -59 25 -34 -13 -14 -26 20 6 97 38 -10zm-141 -61l-33 -53 -31 -6 2 24 -27 3 -23 -30 -41 51 8 32 36 -28 28 0 81 7z"/>
			</g>
		</svg> 
	</div>
	<!--END OF THIS IS THE BACKGROUND SVG DO NOT REMOVE-->
	
	<div class="contentBox">
	<div class="center">
		<?php if($__debug) echo "<div style='width:100%;background-color:darkred;'>This is a development preview of TaskBoard. <br/>
		Please help out with making it better by contributing to <a href='https://github.com/corneyflorex/TaskBoard'>here</a> </div>"?>
		
		<?php if (in_array("tasksList", $mode)) {echo __tagPageMessage($mode,$tags,$__tagPageArray); }?>
	
		<div id='header' class='greybox'>
			<!--Title or logo & Navigation links-->
			<b><a style="font-size:2em;text-decoration:none" href="?">TASKBOARD</a> </b>| <a href="?q=/tasks/search">Search</a> | <a href="?q=/tasks/new">New task</a>
			<!--Title or logo-->
			
			<!-- Perm Tags Board -->
			<div class="tagcloud">
				Boards: 
				<?php 
				if(!empty($__defaultTags) and isset($__defaultTags)){
					foreach($__defaultTags as $tag){ 
				?>
						<a href="?q=/tags/<?php echo htmlentities(stripslashes($tag)); ?>"><?php echo $tag ; ?></a>
				<?php 
					}
				}?>
			</div>
			<!---->
			
			<!--Most commonly accessed tags this week-->
			<div class="tagcloud">
				Top Tags: 
				<?php foreach($top_tags as $tag){ ?>
							<a href="?q=/tags/<?php echo htmlentities(stripslashes($tag['label'])); ?>" title="Count: <?php echo htmlentities(stripslashes($tag['count'])); ?>"><?php echo substr( htmlentities(stripslashes(htmlentities($tag['label']))) ,0,10) ; ?></a>
				<?php } ?>
			</div>
			<!--Most commonly accessed tags this week-->
		</div>
		
		<!--TaskView-->
		<?php if (in_array("tasksView", $mode)) { ?>
		<div class="tasklist">
			<?php foreach($tasks as $task){ ?>
					<div class="task1">
						<?php echo __prettyTripFormatter($task['tripcode']);?>
						<span class="title"><?php echo htmlentities(stripslashes($task['title'])); ?> </span>
						<span><?php echo date('F j, Y, g:i a', $task['created']);?></span>
						<br />
						<br />
						<span class="message"><?php echo nl2br(__encodeTextStyle(htmlentities(stripslashes($task['message'])))); ?></span>
					</div>
					<div class="task1">
						<a href="http://tinychat.com/<?php echo md5($task['message']);?>" target="_blank">Conference via TinyChat - click here</a>
					</div>

					<div class="greybox" id="add_comment">
						<b>Add Comment:</b>
						<form name="add_comment" action="?q=/tasks/comment/<?php echo $task['task_id']; ?>" method="post" enctype='multipart/form-data'>
							<textarea id="comment" name="comment"></textarea>
							<input type="hidden" name="taskID" value="<?php echo $task['task_id']; ?>"><br/>
							<br />
							Passfile: <INPUT type='file' name='keyfile' />
							<br />
                            Password: <INPUT type='text' name='password' >
							<br />
							<br />
							
							<b>CAPCHA(<a style="color:grey;" href="./asciicapcha/asciicaptcha.php">source</a>):</b> 
							<?php
							$ascii_capcha = __getCAPCHA($__salt);
							echo "<pre style='font-size:4px;'>".$ascii_capcha["image"]."</pre>"
							?>
							<INPUT type='text' name='capcha' value=''>
							<INPUT type='hidden' name='digest' value='<?php echo $ascii_capcha["digest"]; ?>'>

							<br />
							
							<input type="submit" value="Submit" />		

						</form>
					</div>
					
					<div id="commentDIV" >
						<?php echo __commentDisplay($comments);?>
					</div>

					
					<br />
					<div class="greybox">
						Task Administration
						<FORM action='?q=/tasks/delete' method='post' enctype='multipart/form-data'>
							<input type="hidden" name="taskID" value="<?php echo $task['task_id']; ?>">
							KeyFile:<input type='file' name='keyfile' />
							<br />
							Password: <INPUT type='text' name='password' value=''>
							<INPUT type='submit' value='delete task'> 
						</FORM>
					</div>
			<?php } ?>
		</div>
		<?php } ?>
		<!--TaskView-->
		
		<!--List of task-->
		<?php if (in_array("tasksList", $mode)) { ?>
		
			<div style="text-align:center" class="greybox">
				<?php if (!empty($tags)){?>
					<a href="?q=/tasks/new&tag=<?php echo $tags[0];?>">Create New '<?php echo $tags[0];?>' Task</a>
				<?php } else {?>
					<a href="?q=/tasks/new">Post new task here</a>
				<?php }?>
			</div>
			
			
			
			<div id="taskDIV" class="tasklist">
				<?php echo __taskDisplay($tasks);?>
			</div>
		<?php } ?>
		<!--List of task-->

		
		
		<!--Search by tag-->
		<?php if (in_array("tagSearch", $mode)) { ?>
		<br />
		<div class="greybox">
			Tag Search:
			<br />
			(Tags seperated by spaces)
			<br />
			<FORM action='?q=/tasks/search' method='post'>
				<INPUT type='text' name='tags' value=''><INPUT type='submit' value='Tag Search'> 
			</FORM>
		</div>
		<?php } ?>
		<!--Search by tag-->
		
		<!--Submit field-->
		<?php if (in_array("submitForm", $mode)) { ?>
		
		<br />
		<div class="greybox">
			New Task Submission Form:
			<br />
			<FORM action='?q=/tasks/submitnew' method='post' enctype='multipart/form-data'>
				<P>
					Title*:<br />		<INPUT type='text' name='title'value=''><br />	
					Message*:<br />	<textarea class='' rows=5 name='message'></textarea><br />			
					Tags:<BR><INPUT type='text' name='tags' value='<?php if(isset($_GET['tag'])){echo $_GET['tag'];}?>'><br />
					<br /> <label for='file'>KeyFile:</label><br /> <input type='file' name='keyfile' />
					<br /> <label>Password:</label><br /> <INPUT type='text' name='password'value=''><br />
					<br />
					
					<b>CAPCHA(<a style="color:grey;" href="./asciicapcha/asciicaptcha.php">source</a>):</b> 
					<input style="size:4px;" type="button" value="Reload" onClick="window.location.reload()">
					
					<?php
					$ascii_capcha = __getCAPCHA($__salt);
					echo "<pre style='font-size:6px;'>".$ascii_capcha["image"]."</pre>"
					?>
					<INPUT type='text' name='capcha' value=''>
					<INPUT type='hidden' name='digest' value='<?php echo $ascii_capcha["digest"]; ?>'> 
					
					<br />
					'*' = Must be filled in
					<br /><INPUT type='submit' value='Send'> <INPUT type='reset'>

				</P>
			</FORM>
			<br />
			Note: Tags are seperated by spaces e.g."cat hat cake"
		</div>
		<?php } ?>
		<!--Submit field-->
		
		<br />
		
		<!--JAVASCRIPT CLOCK-->
		<div class="timebox" id="utcDate"></div>
		<div class="timebox" id="utcTime"></div>
		<div class="timebox" id="localTime"></div>
		<!--JAVASCRIPT CLOCK-->
		<br />
		
		
		<div class="blackbox">
		<?php include("worldmap.html"); ?>
		</div>
		
		<!--QR CODE - To help encourage acesses by mobile phone-->
		<div style="text-align:center;" class="blackbox">
		<b>SCAN ME </b> <a href="http://qrcode.kaywa.com/img.php?s=8&amp;d=http%3A%2F%2F<?php if(isset($_SERVER["SERVER_NAME"]) AND isset($_SERVER["REQUEST_URI"]) )echo $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];?>">QR Code Image</a> 
		| <a href='./anonregkit.php' >AnonRegKit</a> | <button id="stopAutoUpdateButton" onclick="tries=0;">Refresh Now</button>

		</div>
		<!--QR CODE - To help encourage acesses by mobile phone-->

	</div>		
	</div>
</body>
</html>