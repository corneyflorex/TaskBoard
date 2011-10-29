<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" ></meta>
<meta charset="UTF-8"/>

<title> TaskBoard</title>

<link rel="icon" href="favicon.ico" type="image/x-icon">
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon"> 

<link rel="stylesheet" media="screen and (min-width: 480px)" href="css/styles.css" type="text/css" />
<link rel="stylesheet" media="screen and (max-width: 480px)" href="css/mobile.css" type="text/css" />
<link rel="stylesheet" href="css/tagcloud.css" type="text/css" />


<script type="text/javascript" >

/*
	General XmlHttpRequest Object
*/
//Gets the browser specific XmlHttpRequest Object
function getXmlHttpRequestObject() {
	if (window.XMLHttpRequest) {
		return new XMLHttpRequest();
	} else if(window.ActiveXObject) {
		return new ActiveXObject("Microsoft.XMLHTTP");
	} else {
		document.getElementById('p_status').innerHTML = 
		'Status: Cound not create XmlHttpRequest Object.' +
		'Consider upgrading your browser.';
	}
}

/* 
	Autoupdate Sequence (via ajax)
*/
	// Global Tracker Vars
	//prev content
	prev_content = "";
	//number of tries
	waittime = 0;
function autoUpdate(){
	var xmlhttp;
	  xmlhttp = getXmlHttpRequestObject();

	
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
						tries = 0;
					} else {
						tries ++;
					}
					document.getElementById("stopAutoUpdateButton").innerHTML = "Refresh Now - tries:"+tries;
					t=setTimeout('autoUpdate()',1000*4+1000*Math.pow(2,tries));
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
	//FORMAT: $countdown = "countdown($year_c_d,$month_c_d,$day_c_d,$hours_c_d,$minutes_c_d,$seconds_c_d,$timezone_c_d)";
	/*
		check for date and time	
	*/

		// for 2012-09-01T12:35:23+13 OR 2012-09-01 12:35:23 UTC+13
	if( preg_match ( "/(\d{4})[-\/](\d{2})[-\/](\d{2})[T ](\d{2}):(\d{2}):(\d{2})(?:Z| UTC| GMT)?([-+ ]\d{1,2})/i" , $task['message'], $cdmatches ) ){
		$countdown = "countdown($cdmatches[1],$cdmatches[2],$cdmatches[3],$cdmatches[4],$cdmatches[5],$cdmatches[6],$cdmatches[7])";
			
		// for 2012-09-01T12:35:23Z OR 2012-09-01 12:35:23Z UTC
	} else if( preg_match ( "/(\d{4})[-\/](\d{2})[-\/](\d{2})[T ](\d{2}):(\d{2}):(\d{2})(?:Z| UTC| GMT)/i" , $task['message'], $cdmatches ) ){
		$countdown = "countdown($cdmatches[1],$cdmatches[2],$cdmatches[3],$cdmatches[4],$cdmatches[5],$cdmatches[5],00 )";
			
		// for 2012-09-01T12:35+13 OR 2012-09-01 12:35 UTC+13
	} else if( preg_match ( "/(\d{4})[-\/](\d{2})[-\/](\d{2})[T ](\d{2}):(\d{2})(?:Z| UTC| GMT)?([-+ ]\d{1,2})/i" , $task['message'], $cdmatches ) ){
		$countdown = "countdown($cdmatches[1],$cdmatches[2],$cdmatches[3],$cdmatches[4],$cdmatches[5],00,$cdmatches[6])";
			
		// for 2012-09-01T12:35Z or 2012-09-01 12:35 UTC
	} else if( preg_match ( "/(\d{4})[-\/](\d{2})[-\/](\d{2})[T ](\d{2}):(\d{2})(?:Z| UTC| GMT)/i" , $task['message'], $cdmatches ) ){
		$countdown = "countdown($cdmatches[1],$cdmatches[2],$cdmatches[3],$cdmatches[4],$cdmatches[5],00,00)";
		
		// for 2012-09-01 // zulu notation is assumed as at this level of detail, timezone is not required
	} else if( preg_match ( "/(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})/i" , $task['message'], $cdmatches ) ){
		$countdown = "countdown($cdmatches[1],$cdmatches[2],$cdmatches[3],00,00,00,00)";
		
		// for 01-09-2012 // zulu notation is assumed as at this level of detail, timezone is not required
	} else if( preg_match ( "/(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})/i" , $task['message'], $cdmatches ) ){
		$countdown = "countdown($cdmatches[3],$cdmatches[2],$cdmatches[1],00,00,00,00)";
		
	} else{
		$countdown = "";
	}

?>
	/*
		Countdown System JAVASCRIPT FUNCTION
	*/
	function countdown(yr,m,d,hr,min,sec,tz){
		var montharray = Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
		theyear=yr;themonth=m;theday=d;thehour=hr;theminute=min;thesecond=sec;thetimezone=tz;
			
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
		var futurestring1=(montharray[m-1]+" "+d+", "+yr+" "+hr+":"+min+":"+sec);
		var futurestring=Date.parse(futurestring1)-(today.getTimezoneOffset()*(1000*60));
		var dd=futurestring-todaystring;
		var dday=Math.floor(dd/(60*60*1000*24)*1);
		var dhour=Math.floor((dd%(60*60*1000*24))/(60*60*1000)*1);
		var dmin=Math.floor(((dd%(60*60*1000*24))%(60*60*1000))/(60*1000)*1);
		var dsec=Math.floor((((dd%(60*60*1000*24))%(60*60*1000))%(60*1000))/1000*1);
		if(dday<=0&&dhour<=0&&dmin<=0&&dsec<=0){
			document.getElementById('count2').innerHTML="Countdown Completed at "+futurestring1+" UTC\+"+tz+"";
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
        setTimeout("countdown(theyear,themonth,theday,thehour,theminute,thesecond,thetimezone)",1000);
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
			<div class="taglist">
				Boards: 
				<?php 
				if(!empty($__defaultTags) and isset($__defaultTags)){
					foreach($__defaultTags as $tag){ 
				?>
						<a href="?q=/tags/<?php echo htmlentities(stripslashes($tag),null, 'utf-8'); ?>"><?php echo $tag ; ?></a>
				<?php 
					}
				}?>
			</div>
			<!---->
			
			<!--Most commonly accessed tags this week-->
			<div class="taglist">
				Top Tags: 
				<?php foreach($top_tags as $tag){ ?>
							<a href="?q=/tags/<?php echo htmlentities(stripslashes($tag['label'])); ?>" title="Count: <?php echo htmlentities(stripslashes($tag['count'])); ?>"><?php echo substr( htmlentities(stripslashes(htmlentities($tag['label'])),null, 'utf-8') ,0,10) ; ?></a>
				<?php } ?>
			</div>
			<!--Most commonly accessed tags this week-->
		</div>
		
		<!--Admin message-->
		<?php if (in_array("tasksList", $mode)) {echo __tagPageMessage($mode,$tags,$__tagPageArray); }?>
		
		<!--Navigation-->
		<div id="nav" style="" class="greybox">
			<?php
			if(isset($_SERVER['HTTP_REFERER'])){
				$url = htmlspecialchars($_SERVER['HTTP_REFERER']);
				echo "<a style='font-weight:bold;' href='$url'>Back</a>";
			} else {
				echo "<a style='font-weight:bold;' href='?'>Home</a>";
			}
			?>
			|
			<?php if (in_array("tasksList", $mode)) { ?>
					<?php if (!empty($tags)){?>
						<a style="font-weight:bold;" href="?q=/tasks/new&tag=<?php echo $tags[0];?>">Create New '<?php echo $tags[0];?>' Task</a>
					<?php } else {?>
						<a style="font-weight:bold;" href="?q=/tasks/new">Post new task here</a>
					<?php }?>
			|		
			<?php } else {

				  }
			?>
			<!-- THERE IS SOME PROBLEM WITH SEARCH AT THE MOMENT
			<a href="?q=/tasks/search">Search</a>
			|	
			-->

			<a href="?q=/rss">RSS</a>
			|
			<a href="help.html">Help</a>
			|

			<FORM style="float:right;" action='?q=/tags/' method='post'>
				<INPUT style="background:grey; color:white; border-width:1px; border-style:solid; border-color:grey;" type='text' name='tags' size='10' > 
				<INPUT style="background:grey; color:white; border-width:1px; border-color:grey;" type='submit' value='Access Board'> 
			</FORM>
		</div>		
		
		<!--TaskView-->
		<?php if (in_array("tasksView", $mode)) { ?>
		<div class="tasklist">
			<?php //$task = $tasks[0]; //donno why this is here ?>
			
					<div style="text-align:center; border-width:1px; border-radius: 10px;" class="blackbox">
						<a style="color:grey;" href="#OP">View Author's Message</a>
						( <a style="color:grey;" href="?q=/printview/<?php echo $taskid?>">Print</a> )
						<?php if ( isset($_GET['referral_tag']) ) {?>
							<a style="color:grey;" href="?q=/tasks/new&tag=<?php echo $_GET['referral_tag'];?>&respondtaskid=<?php echo $taskid;?>">Create New Version Of This Task</a>
						<?php } else if( isset($taskid) ) {?>
							<a style="color:grey;" href="?q=/tasks/new&respondtaskid=<?php echo $taskid;?>">Create New Version Of This Task</a>
						<?php }?>
					</div>
					
					<?php if(isset($task['responding_to_task_id'])){?>
						<div class="task1">
							>> This Task is a reponse to this <a href="?q=/view/<?php echo $task['responding_to_task_id'] ?>" target="_blank">Parent Task</a>
						</div>	
					<?php } ?>
					
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
						<a href="?q=/image/<?php echo $task['task_id']; ?>"><img border="0" src="?q=/image/<?php echo $task['task_id']; ?>" alt="Pulpit rock" style="max-width:100%"/></a>
					</div>
					<?php } ?>
					
					<?php	// This is to show the users, what tags a post is tagged as.
							if(isset($tagsused) && !empty($tagsused)){//tagsused
								$tagstring = "";
								foreach( $tagsused as $row){
									$tagstring .= " #".$row['label'];
								}
								$tagmessage="\n \n \n \n HashTag(s):".$tagstring;
							}else{
								$tagmessage="";
							} 
					?>
					
					<div id="OP" class="task1">
						<?php echo __prettyTripFormatter($task['tripcode']);?>
						<span class="title"><?php echo htmlentities(stripslashes($task['title']),null, 'utf-8'); ?> </span>
						<span><?php echo date('F j, Y, g:i a', $task['created']);?></span>
						<span style='font-size:0.6em;' ><i><div id='OPGUID' >MD5 Global ID: <?php echo md5($task['message']); ?></div></i></span>
						<br />
						<span class="message">
							<?php echo nl2br(
												__encodeTextStyle(htmlentities(stripslashes(
													$task['message'] 
													.$tagmessage
												),null, 'utf-8'))
											); ?>
						</span>
					</div>
					<!--
					<div class="task1">
						<a href="http://tinychat.com/<?php echo md5($task['message']);?>" target="_blank">Conference via TinyChat - click here</a>
					</div>
					-->
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
		
			<!-- TagCanvus (ACTIVATES ONLY ON FRONTPAGE)-->
			<?php if(isset($tagClouds)){ ?>
			<div class="cloudbox">
				<div class="tagcloud">
				<?php 
				$maxcount = 1;
				foreach($tagClouds as $tag){ 
					if($maxcount<$tag['count']){
					$maxcount = $tag['count'];
					}
				}
				foreach($tagClouds as $tag){ 
					$min_font_size = 0.5;$max_font_size = 3;
					$scalefactor = 1;
					$weight = round( $min_font_size+($max_font_size - $min_font_size)*(stripslashes($tag['count']) / $maxcount) ); 
					$font_size = $scalefactor*$weight .'em';
				?>
					<a 
					style="font-size: <?php echo $font_size ;?>;" 
					href="?q=/tags/<?php echo htmlentities(stripslashes($tag['label']),null, 'utf-8'); ?>" 
					title="Count: <?php echo htmlentities(stripslashes($tag['count'])); ?>"
					>
							<?php echo substr( htmlentities(stripslashes(htmlentities($tag['label']))) ,0,20) ; ?>
					</a>
				<?php } ?>
				</div>
			</div>
			
			<div style="text-align:center;" class="greybox">
			<h4>Recent Updates Below</h4>
			</div>
			<?php } ?>
			<!-- TagCanvus -->		
			
			<?php 
						//referral tag for the 'clone' task feature
						if (!empty($tags)){
							$referraltag = $tags[0];
						} else {
							$referraltag = "";
						}
			?>

			<div id="taskDIV" class="tasklist">
				<?php echo __taskDisplay($tasks,$referraltag);?>
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
					<?php 
							// if tag is suggested.
							if(isset($_GET['tag'])){
								$tagpreset = $_GET['tag'];
								$tagpresetmessage="HashTag: #".implode(" #",explode(" ",$tagpreset));
							}else{
								$tagpreset ="";
								$tagpresetmessage="";
							} 
							// There is no xss protection for 'respond' field, as it is assumed that people won't try to 'copy' post that has obvious xss scripts.
							
					?>
					Title*:<br /> <INPUT type='text' size=50 name='title'value='<?php if(isset($responding_to_task)){echo "(Response To): ".htmlentities($responding_to_task['title'],null, 'utf-8');}?>'><br />
					Message*:<br />	<textarea class='' rows=10 cols=50 name='message'><?php if(isset($responding_to_task)){echo htmlentities($responding_to_task['message'],null, 'utf-8');}?></textarea><br />			
					<?php echo $tagpresetmessage;?><br/>
					Tags:<BR><INPUT type='text' name='tags' value='<?php echo $tagpreset;?>'><br />
					<INPUT type='hidden' name='respondid' value='<?php echo $responding_taskid;?>'><br />
					<label for='file'>Image:</label><br /> <input type='file' name='image' />
					<br />
					<br />
					<br /> Authentication (No Registration Required):
					<br /> <label for='file'>KeyFile:</label><br /> <input type='file' name='keyfile' />
					<br /> <label>Password:</label><br /> <INPUT type='text' name='password' value=''><br />
					<br />
					<br />
					'*' = Must be filled in
					<br /><INPUT type='submit' value='Send'> <INPUT type='reset'>

				</P>
			</FORM>
			<br />
			Note: tags in tag fields are seperated by spaces e.g."cat hat cake". Hashtags in message fields are determined by "#" e.g. #hashtags
			<br />
			Useful Tips: Typing a datetime in ISO_8601 format will show a countdown to that date in javascript ( Formatting guide here: http://en.wikipedia.org/wiki/ISO_8601 )
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
		<?php include("./worldmap/worldmap.html"); ?>
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