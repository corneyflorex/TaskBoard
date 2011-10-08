<?php

	function __commentDisplay($comments){
		$commentContent ="";
		foreach ($comments as $comment){
			$commentContent =
			$commentContent												.
			"<div  class='greybox'>
				<a name=".$comment['id']."></a>"									.
				date('F j, Y, g:i a', $comment['created'])				.
				"</br>"													.
				__prettyTripFormatter($comment['tripcode'],'#'.$comment['id'])				.
				nl2br(__encodeTextStyle(htmlentities(stripslashes($comment['message'])))) 	. 
				"</br></br>"													.
			"</div>";
		};
		return $commentContent;
	}
	
	function __taskDisplay($tasks){
		$taskDisplayContent ="";				$i=1;
		foreach($tasks as $task){
			$taskDisplayContent = $taskDisplayContent."
				<div class='task".($i%2)."'>
					<span style='float:right;'>".date('M j, Y', $task['created'])."</span>
					<span class='title'>
						<a href='?q=/view/".$task['task_id']."' >".substr(htmlentities(stripslashes($task['title'])),0,40)."</a>
					</span>
					<span class='message'>".substr(htmlentities(stripslashes($task['message'])),0,100)."</span>
				</div>";
			$i++;
		};
		return $taskDisplayContent;
	}
?>