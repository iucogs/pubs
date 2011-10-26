<?php 

require_once("../classes/User.class.php");
$user = new User();

if (isset($GLOBALS['HTTP_RAW_POST_DATA']))
{
	$jsonObj = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
	
	if(isset($jsonObj->{'request'}->{'type'}))
	{
		$type = $jsonObj->{'request'}->{'type'};
		if($type == "create")
		{
			// Create
			$arr = array();
			$arr['account'] = $jsonObj->{'request'}->{'account'}; 
			$arr['username'] = trim($jsonObj->{'request'}->{'username'}); 
			$arr['firstname'] = trim($jsonObj->{'request'}->{'firstname'});
			$arr['lastname'] = trim($jsonObj->{'request'}->{'lastname'});
			$arr['submitter'] = $jsonObj->{'request'}->{'submitter'}; 
				
			$user_result = $user->create_user($arr['account'], $arr['username'], $arr['firstname'], $arr['lastname'], $arr['submitter']);		
			if($user_result[0] != false)
			{
				$new_user_id = $user_result[1];
				$new_result = $user->get_user($new_user_id, $arr['submitter']);
				$result = $user->get_all_users($arr['submitter']);

$jsonString = '{"error": "'.$user->error.'", "id": "'.$new_user_id.'", "username": "'.$new_result[0]['username'].'", "firstname": "'.$new_result[0]['firstname'].'", "lastname": "'.$new_result[0]['lastname'].'", "submitter": "'.$arr['submitter'].'", "result": "true", "createuser":'.json_encode($result).'}';
				echo $jsonString;
			}
			else
			{
				$jsonString = '{"error": "'.$user->error.'", "result": "false", "createuser": "'.$user_result[1].'"}';
				echo $jsonString;
			}
		}
		else if($type == "delete")
		{
			$arr = array();
			$arr['id'] = $jsonObj->{'request'}->{'id'}; 
			$arr['submitter'] = $jsonObj->{'request'}->{'submitter'}; 
			
			$id = $user->remove_user($arr['id'],$arr['submitter']);
			
			if($id != false)
			{
				$user_id = $id;
			} 
			else
			{
				$user_id = "Error";
			}
			
			$result = $user->get_all_users($arr['submitter']);
			
			$jsonString = '{"error": "'.$user->error.'", "id": "'.$user_id.'", "submitter": "'.$arr['submitter'].'", "deleteuser":'.json_encode($result).'}';
			echo $jsonString;
		}
		else if($type == "view")
		{
			$arr = array();
			$arr['submitter'] = $jsonObj->{'request'}->{'submitter'}; 
			
			$result = $user->get_all_users($arr['submitter']);
			
			$jsonString = '{"error": "'.$user->error.'", "viewuser":'.json_encode($result).', "submitter": "'.$arr['submitter'].'"}';
			echo $jsonString;
		}
		else if($type == "get_faculty")
		{
			$arr = array();
			
			$result = $user->get_faculty();
			
			$jsonString = '{"error": "'.$user->error.'", "get_faculty":'.json_encode($result).'}';
			echo $jsonString;
		}
		else if($type == "update")
		{
			$arr = array();
			$arr['id'] = $jsonObj->{'request'}->{'id'}; 
			$arr['username'] = $jsonObj->{'request'}->{'username'}; 
			$arr['firstname'] = $jsonObj->{'request'}->{'firstname'};
			$arr['lastname'] = $jsonObj->{'request'}->{'lastname'}; 
			$arr['submitter'] = $jsonObj->{'request'}->{'submitter'}; 
		
			$update = $user->update_user($arr['id'], $arr['username'], $arr['firstname'], $arr['lastname'], $arr['submitter']);
			$result = $user->get_user($arr['id'], $arr['submitter']);
		
			$jsonString = '{"error": "'.$user->error.'", "updateuser":'.json_encode($result).'}';
			echo $jsonString;
		}
		else if($type == "cancel")
		{
			$arr = array();
			$arr['id'] = $jsonObj->{'request'}->{'id'}; 
			$arr['submitter'] = $jsonObj->{'request'}->{'submitter'}; 
		
			$result = $user->get_user($arr['id'], $arr['submitter']);
		
			$jsonString = '{"error": "'.$user->error.'", "getuser":'.json_encode($result).'}';
			echo $jsonString;
		}
		else
		{}
	}
}

?>