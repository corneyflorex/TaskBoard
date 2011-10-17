<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title> TaskBoard</title>

<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"> 

<link rel="stylesheet" media="screen and (min-width: 480px)" href="css/styles.css" type="text/css" />
<link rel="stylesheet" media="screen and (max-width: 480px)" href="css/mobile.css" type="text/css" />

<script type="text/javascript" >


/* 
	Autoupdate Sequence (via ajax)
*/
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


/*
	Time and date in local and UTC
*/
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



		<!--COUNTDOWN SYSTEM (DETECTION FORMAT EXAMPLE: 2012-09-01 12:35 UTC+13 )-->
<?php
if (in_array("tasksView", $mode)) {
	$task = $tasks[0];
	
	//var_dump($tasks[0]);
	if( preg_match ( "/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}) UTC([-+ ]\d{1,2})/" , $task['message'], $countdown_matches ) ){
		$year_countdown			= $countdown_matches[1];
		$month_countdown		= $countdown_matches[2];
		$day_countdown			= $countdown_matches[3];
		$hour_countdown			= $countdown_matches[4];
		$minutes_countdown		= $countdown_matches[5];
		$timezone_countdown		= $countdown_matches[6];
		$countdown 				= "countdown($year_countdown,$month_countdown,$day_countdown,$hour_countdown,$minutes_countdown,$timezone_countdown)";
		echo $countdown;
	}else{
		$countdown = "";
	}

?>
	/*
		Countdown System JAVASCRIPT FUNCTION
	*/
	function countdown(yr,m,d,hr,min,tz){
		var montharray = Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
		theyear=yr;themonth=m;theday=d;thehour=hr;theminute=min;thetimezone=tz;
			
		var today=new Date();
		var todayy=today.getYear();
		if (todayy < 1000) {todayy+=1900;}
		var todaym=today.getMonth();
		var todayd=today.getDate();
		var todayh=today.getHours();
		var todaymin=today.getMinutes();
		var todaysec=today.getSeconds();
		var todaystring1=montharray[todaym]+" "+todayd+", "+todayy+" "+todayh+":"+todaymin+":"+todaysec;
		var todaystring=Date.parse(todaystring1)+(tz*1000*60*60);
		var futurestring1=(montharray[m-1]+" "+d+", "+yr+" "+hr+":"+min);
		var futurestring=Date.parse(futurestring1)-(today.getTimezoneOffset()*(1000*60));
		var dd=futurestring-todaystring;
		var dday=Math.floor(dd/(60*60*1000*24)*1);
		var dhour=Math.floor((dd%(60*60*1000*24))/(60*60*1000)*1);
		var dmin=Math.floor(((dd%(60*60*1000*24))%(60*60*1000))/(60*1000)*1);
		var dsec=Math.floor((((dd%(60*60*1000*24))%(60*60*1000))%(60*1000))/1000*1);
		if(dday<=0&&dhour<=0&&dmin<=0&&dsec<=0){
			document.getElementById('count2').innerHTML="CountDown Completed";
			document.getElementById('count2').style.display="inline";
			document.getElementById('count2').style.width="390px";
			document.getElementById('dday').style.display="none";
			document.getElementById('dhour').style.display="none";
			document.getElementById('dmin').style.display="none";
			document.getElementById('dsec').style.display="none";
			document.getElementById('days').style.display="none";
			document.getElementById('hours').style.display="none";
			document.getElementById('minutes').style.display="none";
			document.getElementById('seconds').style.display="none";
			document.getElementById('spacer1').style.display="none";
			document.getElementById('spacer2').style.display="none";
			return;
		} else {
			document.getElementById('count2').innerHTML="Countdown to "+futurestring1+" UTC\+"+tz+"";
			document.getElementById('count2').style.display="inline";
			document.getElementById('count2').style.width="400px"; 
			<!--document.getElementById('count2').style.display="none";-->
			document.getElementById('dday').innerHTML=dday;
			document.getElementById('dhour').innerHTML=dhour;
			document.getElementById('dmin').innerHTML=dmin;
			document.getElementById('dsec').innerHTML=dsec;
        setTimeout("countdown(theyear,themonth,theday,thehour,theminute,thetimezone)",1000);
		}
	}
<?php
}else{
		$countdown = ""; // Disable the countdown system
}
?>
		<!--COUNTDOWN SYSTEM-->

</script>

</head>



<body onload="startTime();autoUpdate();<?php echo $countdown ?>">
	<div class="center">
		<?php if($__debug) echo "<div style='width:100%;background-color:darkred;'>This is a development preview of TaskBoard. <br/>
		Please help out with making it better by contributing to <a href='https://github.com/corneyflorex/TaskBoard'>here</a> </div>"?>
	
		<div id='header' class='greybox'>
			<!--Title or logo & Navigation links-->
			<a style="font-size:2em;text-decoration:none" href="?">TASKBOARD</a>
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
		
		<!--Admin message-->
		<?php if (in_array("tasksList", $mode)) {echo __tagPageMessage($mode,$tags,$__tagPageArray); }?>
		
		<!--Navigation-->
		<div id="nav" style="text-align:center" class="greybox">
			|
			<a href="?q=/tasks/new">New task</a>
			| 
			<a href="?q=/tasks/search">Search</a>
			|											
			<?php if (in_array("tasksList", $mode)) { ?>
					<?php if (!empty($tags)){?>
						<a href="?q=/tasks/new&tag=<?php echo $tags[0];?>">Create New '<?php echo $tags[0];?>' Task</a>
					<?php } else {?>
						<a href="?q=/tasks/new">Post new task here</a>
					<?php }?>
			|
			<?php } ?>
			<a href="?q=/rss">RSS</a>
			|
			<a href="help.html">Help</a>
			|
		</div>		
		
		<!--TaskView-->
		<?php if (in_array("tasksView", $mode)) { ?>
		<div class="tasklist">
			<?php $task = $tasks[0]; ?>
			
					<div style="text-align:center; border-width:1px; border-radius: 10px;" class="blackbox">
						<a style="color:grey;" href="#OP">View Authors Message</a>
					</div>
					
					<!--COUNTDOWN SYSTEM-->
					<?php if($countdown != ""){ ?>
					<div style="text-align:center; border-width:1px; border-radius: 10px;" class="blackbox">
						<table id="table" style="margin: 0px auto;" border="0">
							<tr>
								<td align="center" colspan="6"><div class="numbers" id="count2" style="padding: 5px 0 0 0; "></div></td>
							</tr>
							<tr id="spacer1">
								<td align="center" ><div class="numbers" ></div></td>
								<td align="center" ><div class="numbers" id="dday"></div></td>
								<td align="center" ><div class="numbers" id="dhour"></div></td>
								<td align="center" ><div class="numbers" id="dmin"></div></td>
								<td align="center" ><div class="numbers" id="dsec"></div></td>
								<td align="center" ><div class="numbers" ></div></td>
							</tr>
							<tr id="spacer2">
								<td align="center" ><div class="title" ></div></td>
								<td align="center" ><div class="title" id="days">Days</div></td>
								<td align="center" ><div class="title" id="hours">Hours</div></td>
								<td align="center" ><div class="title" id="minutes">Minutes</div></td>
								<td align="center" ><div class="title" id="seconds">Seconds</div></td>
								<td align="center" ><div class="title" ></div></td>
							</tr>
						</table>
					</div>
					<?php } ?>
					<!--COUNTDOWN SYSTEM-->

					
					<?php if($task['imagetype'] != NULL){ ?>
					<div style="text-align:center;" class="blackbox">
						<a href="?q=/image/<?php echo $task['task_id']; ?>"><img border="0" src="?q=/image/<?php echo $task['task_id']; ?>" alt="Pulpit rock" width="100%" /></a>
					</div>
					<?php } ?>
					
					<div id="OP" class="task1">
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
					
					<div style="text-align:center; border-width:1px; border-radius: 10px;" class="greybox">
						<a style="color:grey;" href="#add_comment">Post Comment</a>
					</div>
					
					<div id="commentDIV" >
						<?php echo __commentDisplay($comments);?>
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
							<INPUT type='hidden' name='capcha' value=''>
							<INPUT type='hidden' name='digest' value='<?php echo $ascii_capcha["digest"]; ?>'>

							<br />
							
							<input type="submit" value="Submit" />		

						</form>
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
		</div>
		<?php } ?>
		<!--TaskView-->
		
		<!--List of task-->
		<?php if (in_array("tasksList", $mode)) { ?>
			
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
					Title*:<br /> <INPUT type='text' name='title'value=''><br />	
					Message*:<br />	<textarea class='' rows=10 name='message'></textarea><br />			
					Tags:<BR><INPUT type='text' name='tags' value='<?php if(isset($_GET['tag'])){echo $_GET['tag'];}?>'><br />
					<label for='file'>Image:</label><br /> <input type='file' name='image' />
					<br />
					<br /> Authentication (No Registration Required):
					<br /> <label for='file'>KeyFile:</label><br /> <input type='file' name='keyfile' />
					<br /> <label>Password:</label><br /> <INPUT type='text' name='password'value=''><br />
					<br />
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
		
		
		<div style="overflow:auto; text-align:center; colour:grey" class="blackbox">
		<a name="WorldMap" href="#WorldMap">WorldMap</a><br /> click map to view posts from each region<br /> 
		<?php include("worldmap.html"); ?>
		</div>
		
		<!--QR CODE - To help encourage acesses by mobile phone-->
		<div style="text-align:center;" class="blackbox">
		<b>SCAN ME </b> <a href="http://qrcode.kaywa.com/img.php?s=8&amp;d=http%3A%2F%2F<?php if(isset($_SERVER["SERVER_NAME"]) AND isset($_SERVER["REQUEST_URI"]) )echo $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];?>">QR Code Image</a> 
		| <a href='./anonregkit.php' >AnonRegKit</a> | <button id="stopAutoUpdateButton" onclick="tries=0;">Refresh Now</button> |
		<a href="./embedme.php?url=http%3A%2F%2F<?php if(isset($_SERVER["SERVER_NAME"]) AND isset($_SERVER["REQUEST_URI"]) )echo $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];?>">Embed Me</a> 

		</div>
		<!--QR CODE - To help encourage acesses by mobile phone-->

	</div>		
</body>
</html>