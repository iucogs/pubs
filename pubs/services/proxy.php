<?php 
require_once("../classes/User.class.php");
$user = new User();

if (isset($GLOBALS['HTTP_RAW_POST_DATA']))
{
	$jsonObj = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
	
	if(isset($jsonObj->{'request'}->{'type'}))
	{
		$type = $jsonObj->{'request'}->{'type'};

		if ($type == "create")
		{
			$arr = array();
			$arr['account'] = $jsonObj->{'request'}->{'account'};
			$arr['username'] = trim($jsonObj->{'request'}->{'username'}); 
			$arr['firstname'] = trim($jsonObj->{'request'}->{'firstname'}); 
			$arr['lastname'] = trim($jsonObj->{'request'}->{'lastname'});
			$arr['submitter'] = $jsonObj->{'request'}->{'submitter'};
			
			$proxy_result = $user->create_proxy($arr['account'], $arr['username'], $arr['firstname'], $arr['lastname'], $arr['submitter']);
			if($proxy_result[0] != false)
			{
				$user_id = $proxy_result[1];
				$new_result = $user->get_user($user_id, $arr['submitter']);
				$result = $user->get_all_proxies($arr['submitter']);
				
				$jsonString = '{"error": "'.$user->error.'", "id": "'.$user_id.'", "username": "'.$new_result[0]['username'].'", "firstname": "'.$new_result[0]['firstname'].'", "lastname": "'.$new_result[0]['lastname'].'", "submitter": "'.$arr['submitter'].'", "result": "true", "createproxy": '.json_encode($result).'}';				
				echo $jsonString;
			} 
			else
			{
				$jsonString = '{"error": "'.$user->error.'", "result": "false", "createproxy": "'.$proxy_result[1].'"}'; // Contains error code
				echo $jsonString;
			}
	
			
		}
		else if($type == "delete")
		{
			$arr = array();
			$arr['id'] = $jsonObj->{'request'}->{'id'}; 
			$arr['submitter'] = $jsonObj->{'request'}->{'submitter'}; 
			
			$id = $user->remove_proxy($arr['id'],$arr['submitter']);
			
			if($id != false)
			{
				$user_id = $id;
			} 
			else
			{
				$user_id = "Error";
			}
			
			$result = $user->get_all_proxies($arr['submitter']);
			
			$jsonString = '{"error": "'.$user->error.'", "id": "'.$user_id.'", "submitter": "'.$arr['submitter'].'", "deleteproxy":'.json_encode($result).'}';
			echo $jsonString;
		}
		else if($type == "view")
		{
			$arr = array();
			$arr['submitter'] = $jsonObj->{'request'}->{'submitter'}; 
		
			$result = $user->get_all_proxies($arr['submitter']);
		
			$jsonString = '{"error22": "'.$user->error.'","error888": "'.$jsonObj->{'request'}->{'submitter'}.'", "viewproxy":'.json_encode($result).'}';
			echo $jsonString;
		}
		
		//Abhinav
		
		else if($type == "sortview")
		{
			$arr = array();
			$arr['submitter'] = $jsonObj->{'request'}->{'submitter'}; 
		
			$result = $user->get_all_sortproxies($arr['submitter']);
		
			$jsonString = '{"error333": "'.$user->error.'", "error333": "'.$jsonObj->{'request'}->{'submitter'}.'","viewproxy":'.json_encode($result).'}';
			echo $jsonString;
		}
		
		else if($type == "manage")
		{
			$arr = array();
			$arr['submitter'] = $jsonObj->{'request'}->{'submitter'}; 
		
			$result = $user->get_all_proxies($arr['submitter']);
		
			$jsonString = '{"error": "'.$user->error.'", "submitter": "'.$arr['submitter'].'", "manageproxy":'.json_encode($result).'}';
			echo $jsonString;
		}
		else if ($type == "viewOwners")
		{
			$arr = array();
			$arr['submitter'] = $jsonObj->{'request'}->{'submitter'}; 
		
			$result = $user->get_all_owners($arr['submitter']);
		
			$jsonString = '{"error": "'.$user->error.'", "owners":'.json_encode($result).'}';
			echo $jsonString;
		}
		else{ }
	}
	else{ }
}



?>