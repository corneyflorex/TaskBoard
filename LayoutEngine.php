<?php

	function __tagPageMessage($mode,$tag="",$__tagPageArray){
		if (empty($__tagPageArray) or !isset($__tagPageArray) ){
			return "";
		}
		if (in_array("tasksList", $mode)) {
	
			if (empty($tag) or !isset($tag) ){
				$tag="home";
			}else{
				if ( is_array($tag) ){
					$tag = $tag[0];
					}
			}
			
			if( !empty($__tagPageArray[$tag]) ) {
				return "
					<div style='	border-radius: 10px;
									width:100%;
									background: #1e5799; /* Old browsers */
									background: -moz-linear-gradient(top, #1e5799 0%, #2989d8 50%, #207cca 51%, #7db9e8 100%); /* FF3.6+ */
									background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#1e5799), color-stop(50%,#2989d8), color-stop(51%,#207cca), color-stop(100%,#7db9e8)); /* Chrome,Safari4+ */
									background: -webkit-linear-gradient(top, #1e5799 0%,#2989d8 50%,#207cca 51%,#7db9e8 100%); /* Chrome10+,Safari5.1+ */
									background: -o-linear-gradient(top, #1e5799 0%,#2989d8 50%,#207cca 51%,#7db9e8 100%); /* Opera11.10+ */
									background: -ms-linear-gradient(top, #1e5799 0%,#2989d8 50%,#207cca 51%,#7db9e8 100%); /* IE10+ */
									filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#1e5799', endColorstr='#7db9e8',GradientType=0 ); /* IE6-9 */
									background: linear-gradient(top, #1e5799 0%,#2989d8 50%,#207cca 51%,#7db9e8 100%); /* W3C */
								'>
						<div style='padding:10px;'>
							<b>Admin Message:</b> 
							$__tagPageArray[$tag]
						</div>
					</div>";
			}
		}
		return "";
	}
	

	function __commentDisplay($comments){
		$commentContent ="";
		foreach ($comments as $comment){
			$time = time() - $comment['created'];
			if ( $time < strtotime("5 minutes",0)){
				$style = "border:5px;border-style:solid;border-color:white;";
			}else if ( $time < strtotime("10 minutes",0)){
				$style = "border:4px;border-style:solid;border-color:green;";
			}else if ( $time < strtotime("30 minutes",0)){
				$style = "border:3px;border-style:solid;border-color:yellow;";
			}else if($time < strtotime("60 minutes",0)){
				$style = "border:2px;border-style:solid;border-color:orange;";
			}else if($time < strtotime("12 hours",0)){
				$style = "border:2px;border-style:solid;border-color:orange;";
			}else if($time < strtotime("1 day",0)){
				$style = "border:1px;border-style:solid;border-color:grey;";
			}else {
				$style = "border:1px;border-style:solid;border-color:black;";
			}
			
			$commentContent = $commentContent . "
			<div  style='$style' class='greybox'>
				<a name=".$comment['id']."></a>".
				"Comment ID >>".$comment['id'].
				"</br>".
				date('F j, Y, g:i a', $comment['created']).
				" | ".
				__humanTiming ($comment['created']). " ago"	.
				"</br>".
				__prettyTripFormatter($comment['tripcode'],'#'.$comment['id']).
				nl2br(__encodeTextStyle(htmlentities(stripslashes($comment['message'])))) 	. 
				"</br></br>".
			"</div>";
		};
		return $commentContent;
	}
	
	function __taskDisplay($tasks){
		$taskDisplayContent ="";				
		$i=1;
		foreach($tasks as $task){
		
			$time = time() - $task['bumped'];
			if ( $time < strtotime("5 minutes",0)){
				$style = "border:1px;border-style:solid;border-color:green;";
			}else if($time < strtotime("30 minutes",0)){
				$style = "border:1px;border-style:solid;border-color:white;";
			}else if($time < strtotime("1 day",0)){
				$style = "border:1px;border-style:solid;border-color:grey;";
			}else {
				$style = "border:1px;border-style:solid;border-color:black;";
			}
		
			$taskDisplayContent = $taskDisplayContent."
				<div style='$style' class='task".($i%2)."'>
					<span style='float:right;'>".date('M j, Y', $task['created'])."</span>
					<span style='float:right;clear:right;'>"."<b>".__humanTiming ($task['bumped'])." ago</b>"."</span>
					<span class='title'>
						<a href='?q=/view/".$task['task_id']."' >".substr(htmlentities(stripslashes($task['title'])),0,40)."</a>
					</span>
					<span class='message'>".substr(htmlentities(stripslashes($task['message'])),0,100)."</span>
				</div>";
			$i++;
		};
		return $taskDisplayContent;
	}
	
	
	//Source http://stackoverflow.com/questions/2915864/php-how-to-find-the-time-elapsed-since-a-date-time
	function __humanTiming ($time)
	{

		$time = time() - $time; // to get the time since that moment

		$tokens = array (
			31536000 => 'year',
			2592000 => 'month',
			604800 => 'week',
			86400 => 'day',
			3600 => 'hour',
			60 => 'min',
			1 => 'sec'
		);

		foreach ($tokens as $unit => $text) {
			if ($time < $unit) continue;
			$numberOfUnits = floor($time / $unit);
			return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
		}

	}
