<?php 

require_once('../lib/adLDAP.php');
require_once('../lib/constants.php');

if (isset($GLOBALS['HTTP_RAW_POST_DATA']))
{
	$jsonObj = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
	
	if(isset($jsonObj->{'request'}->{'type'}))
	{
		if(isset($jsonObj->{'request'}->{'value'})) {
			$value = trim($jsonObj->{'request'}->{'value'});
		}
		if(isset($jsonObj->{'request'}->{'account'})) {
			$account = $jsonObj->{'request'}->{'account'};
		}
		$type = $jsonObj->{'request'}->{'type'};
	
		if($type == "username")
		{
			$result = check_ads($value, $type, $account);
			if(!empty($result))
			{
				$user = get_user_array_from_result($result);
				$jsonString = '{"error": "'.$user->error.'", "users":'.json_encode($user).', "account":"'.$account.'"}';
				echo $jsonString;
			}
			else
			{
				$jsonString = '{"error": "'.$user->error.'", "users":'.json_encode(array()).', "account":"'.$account.'"}';
				echo $jsonString;
			}
		}
		else if($type == "name")
		{
			if(isset($jsonObj->{'request'}->{'lastname'}) && isset($jsonObj->{'request'}->{'firstname'})) { 
				$value2 = $jsonObj->{'request'}->{'firstname'};
				$value = $jsonObj->{'request'}->{'lastname'};
			}

			$result = check_ads(array($value, $value2), $type, $account);
			if(!empty($result))
			{
				$user = get_user_array_from_result($result);
				$jsonString = '{"error": "'.$user->error.'", "users":'.json_encode($user).', "account":"'.$account.'"}';
				echo $jsonString;
			}
			else
			{
				$jsonString = '{"error": "'.$user->error.'", "users":'.json_encode(array()).', "account":"'.$account.'"}';
				echo $jsonString;
			}
		}
		else
		{ 	
			$jsonString = '{"error": "no_type", "users":'.json_encode(array()).', "account":"'.$account.'"}';
			echo $jsonString;
		}	
	}
}

function get_user_array_from_result($result)
{
	$user = array();
	// Found several users, loop through
	$result['count'] > 10 ? $count = 10 : $count = $result['count'];
	for($i = 0; $i < $count; $i++)
	{
		if(!empty($result[$i]['givenname'][0])) $firstname = $result[$i]['givenname'][0]; else $firstname = "";
		if(!empty($result[$i]['sn'][0])) $lastname = $result[$i]['sn'][0]; else $lastname = "";
		if(!empty($result[$i]['department'][0])) $department = $result[$i]['department'][0]; else $department = "";
		if(!empty($result[$i]['samaccountname'][0])) $username = $result[$i]['samaccountname'][0]; else $username = "";
		if(!empty($result[$i]['mail'][0])) $mail = $result[$i]['mail'][0]; else $mail = "";
		
		$user[] = array("firstname" => $firstname, "lastname" => $lastname, "username" => $username, "mail" => $mail, "department" => $department);
	}
	return $user;
}

function check_ads($value, $type, $account)
{
	$ldap = create_ldap_obj($account);
	
	if($ldap)
	{
		if($type == "username")
		{
			if($account == "guest") 
			{
				// Find all User objects that have "mail" or "samaccountname" match $value (value could be 8-digits ID or email address).
				$filter = "(&(objectClass=user) (|(mail=".$value."*) (samaccountname=".$value."*)))";  
			}
			else // ($account == "ads")  
			{ 
				$filter = "(&(objectClass=user) (samaccountname=".$value."*))";
			}
			
			$result=$ldap->search_user($filter,array("givenname","sn","samaccountname","mail","department","displayname","telephonenumber","primarygroupid"));
			return $result;
		}
		else if($type == "name")
		{
			// Find all User objects that have "sn" and "givenName" match $value.
			$filter = "(&(objectClass=user) (&(sn=".$value[0]."*) (givenName=".$value[1]."*)))";
			$result=$ldap->search_user($filter,array("givenname","sn","samaccountname","mail","department","displayname","telephonenumber","primarygroupid"));
			return $result;
		}
	}
}

function create_ldap_obj($account)
{
	$ldap;
	if($account == "guest")
	{
		$options=array("account_suffix" => EADS_ACCOUNT_SUFFIX,
					   "base_dn" => EADS_BASE_DN,
					   "domain_controllers" => array (EADS_DOMAIN_CONTROLLERS),
					   "ad_username" => EADS_AD_USERNAME,
					   "ad_password" => EADS_AD_PASSWORD
					   );
	}
	else // $account == "ads"
	{
		$options=array("account_suffix" => ADS_ACCOUNT_SUFFIX,
					   "base_dn" => ADS_BASE_DN,
					   "domain_controllers" => array (ADS_DOMAIN_CONTROLLERS),
					   "ad_username" => ADS_AD_USERNAME,
					   "ad_password" => ADS_AD_PASSWORD
					   );
	}
	$ldap=new adLDAP($options);	

	return $ldap;
}

?>
