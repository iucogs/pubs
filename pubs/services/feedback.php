<?php 
require_once("../classes/User.class.php");
$user = new User();

if (isset($GLOBALS['HTTP_RAW_POST_DATA']))
{
	$jsonObj = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
	
	if(isset($jsonObj->{'request'}->{'submitter'})){ 
		$submitter = $jsonObj->{'request'}->{'submitter'};
	}
	
	if(isset($jsonObj->{'request'}->{'feedback'})){ 
		$feedback = $jsonObj->{'request'}->{'feedback'};
	}
	if(isset($jsonObj->{'request'}->{'bug_fixed'})){ 
		$bug_fixed = $jsonObj->{'request'}->{'bug_fixed'};
	}
	if(isset($jsonObj->{'request'}->{'comment'})){ 
		$comment = $jsonObj->{'request'}->{'comment'};
	}
	
	if(isset($jsonObj->{'request'}->{'id'})){ 
		$id = $jsonObj->{'request'}->{'id'};
	}
	
	if(isset($jsonObj->{'request'}->{'type'}))
	{
		$type = $jsonObj->{'request'}->{'type'};

		if ($type == 'get_feedback_list')
		{
			$result = $user->get_feedback_list();
			$jsonString = '{"error": "'.$user->error.'", "feedback_list":'.json_encode($result).'}';
			echo $jsonString;
		}
		else if ($type == 'add_feedback')
		{
			$user->add_feedback($submitter, $feedback);
			$result = $user->get_feedback_list();
			$jsonString = '{"error": "'.$user->error.'", "feedback_list":'.json_encode($result).'}';
			echo $jsonString;
		}
		else if ($type == 'update_feedback')
		{
			$user->update_feedback($submitter, $feedback, $id, $bug_fixed, $comment);
			$result = $user->get_feedback_list();
			$jsonString = '{"error": "'.$user->error.'", "feedback_list":'.json_encode($result).'}';
			echo $jsonString;
		}
		else if ($type == 'delete_feedback')
		{
			$user->delete_feedback($submitter, $id);
			$result = $user->get_feedback_list();
			$jsonString = '{"error": "'.$user->error.'", "feedback_list":'.json_encode($result).'}';
			echo $jsonString;
		}
	}
}



?>