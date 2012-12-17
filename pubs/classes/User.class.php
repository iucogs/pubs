<?php 

class User
{
	var $link;
	var $table;
	var $proxy;
	var $notify;
	var $error;
	var $ldap;

	function User()
	{
		require_once('../lib/mysql_connect.php');
		require_once('../lib/adLDAP.php');
        require_once('Collections.class.php');
        $this->table = 'users';
		$this->proxy = 'proxy_of';
        $this->error = 0;
		$this->ldap=new adLDAP();
	}
	
	function get_feedback_list()
	{
		$this->link = $this->connectDB();
		
		$query = "SELECT * FROM feedback ORDER BY date_submitted";
		$result = $this->doQuery($query, $this->link);
		
		if (mysql_num_rows($result) > 0)
			{
				$result_arr = array();
				while(($result_arr[] = mysql_fetch_assoc($result)) || array_pop($result_arr));  // Copy result into an array
				mysql_free_result($result);
			//	print_r($result_arr);
				return $result_arr;
			}
			else
			{
				$this->error .= 2;
				return false;
			}
	}
	
	//Abhinav
	
	function get_sortedfeedback_list()
	{
		$this->link = $this->connectDB();
		
		$query = "SELECT * FROM feedback ORDER BY submitter";
		$result = $this->doQuery($query, $this->link);
		
		if (mysql_num_rows($result) > 0)
			{
				$result_arr = array();
				while(($result_arr[] = mysql_fetch_assoc($result)) || array_pop($result_arr));  // Copy result into an array
				mysql_free_result($result);
			//	print_r($result_arr);
				return $result_arr;
			}
			else
			{
				$this->error .= 2;
				return false;
			}
	}
	
	function add_feedback($submitter, $feedback)
	{
		$this->link = $this->connectDB();
		
		$query = "INSERT INTO feedback (submitter, bug, comment, date_submitted) VALUES ('$submitter', '".mysql_real_escape_string($feedback)."', '', '".time()."')";
		$result = $this->doQuery($query, $this->link);		
	}
	
	function update_feedback($submitter, $feedback, $id, $bug_fixed, $comment)
	{
		$this->link = $this->connectDB();
		$query = "UPDATE feedback SET bug='".mysql_real_escape_string($feedback)."', bug_fixed='".mysql_real_escape_string($bug_fixed)."', comment='".mysql_real_escape_string($comment)."' WHERE id='$id'";
	//	print_r($query);
		$result = $this->doQuery($query, $this->link);		
	}
	
	function delete_feedback($submitter, $id)
	{
		$this->link = $this->connectDB();
		$query = "DELETE FROM feedback WHERE id='$id'";
		$result = $this->doQuery($query, $this->link);		
	}
	
	function check_admin_user($submitter)
	{
		$this->link = $this->connectDB();
		
		$query = "SELECT * FROM $this->table WHERE username='".mysql_real_escape_string($submitter)."' AND (admin='1'OR cogs='1')"; //abhinav
		$result = $this->doQuery($query, $this->link);
		if (mysql_num_rows($result) == 1) 		// User is admin or faculty member
		{
			return true;
		}
		else return false;
	}
	

    // PJC 8.22 on creation of users, collection My Representative Publications
    // is created and assigned to new user.

	function create_user($account, $username, $firstname, $lastname, $submitter)
	{
		$this->link = $this->connectDB();
		
		// Note: Conflict with user creation during proxy creation
		//if(!$this->check_admin_user($submitter))
		//{
		//	$this->error .= 2; 
		//	return array(false, "no_permission");
		//}
		//else
		//{
			if(!$this->verify_ads_user($account, $username))
			{
				$this->error .= 2; 
				return array(false, "no_ads");
			}
			else{
				// Get email as username for guest account.
				$username = $this->get_real_username($account, $username);
				
				// Query username to check if user exist
				$query = "SELECT * FROM $this->table WHERE username='".mysql_real_escape_string($username)."'";
				$result = $this->doQuery($query, $this->link);
				if (mysql_num_rows($result) <= 0) 		// User doesn't exist - Add / Insert
				{
					$value_str = "'".mysql_real_escape_string($username)."','".mysql_real_escape_string($firstname)."','".mysql_real_escape_string($lastname)."'";
                    $query = "INSERT INTO $this->table (username, firstname, lastname) VALUES (".$value_str.")";
				
                    // Collection created if user needs to be added. Script on
                    // ~cogs pulls from "My Representative Publications", so the
                    // first arg should never change.
                    $collections = new Collections();
                    $collections_result = $collections->createCollection("My Representative Publications", $username, $username);
                    if (!$collections_result){
                        return false;
                    }
                    
                    $result = $this->doQuery($query, $this->link);
					if (!$result) { 
						$this->error .= 2; 
						return array(false, "query_error");	
					}
					else { 
						$new_user_id = (int)mysql_insert_id();
						//$this->error .= "New user added successfully [".$new_user_id."].<br />"; 				
						return array(true, $new_user_id);
					}
				}
				else if(mysql_num_rows($result) == 1)	// Found one user - return user
				{
					$row = mysql_fetch_assoc($result);
					$this->error .= 2;
					return array(false, "user_exist", $row['id']); //$row['id'];	Special return used by createproxy();
				}
				else									// More than one user - Error
				{
					$this->error .= 2;
					return array(false, "multiple_users");
				}
			}
		//}
	}
	
	function create_proxy($account, $username, $firstname, $lastname, $submitter)
	{
		// Check if username == submitter
		
		// Note: Currently should allow self as a proxy.
		//if($username == $submitter)
		//{
		//	$this->error .= 2;
		//	return array(false, "proxy_equal_submitter");
		//}
		if(false){}
		else
		{
			// Create user and setup proxy
			$result=$this->create_user($account, $username, $firstname, $lastname, $submitter);
			if($result[0] == true || $result[1] == "user_exist")
			{
				if($result[1] == "user_exist") { $id = $result[2]; }  // Special return value. $result[2] returns user id when a user exists.
				else { $id = $result[1]; } 
	
				$this->link = $this->connectDB();
				
				// Check submitter id
				$query = "SELECT * FROM $this->table WHERE username='".mysql_real_escape_string($submitter)."'";
				$result = $this->doQuery($query, $this->link);
				if(mysql_num_rows($result) == 1)
				{
					$row = mysql_fetch_array($result, MYSQL_ASSOC);
					$submitter_id = $row['id'];
					
					// Check existing proxy
					$query = "SELECT * FROM proxy_of WHERE proxyid='".mysql_real_escape_string($id)."' AND authorid='".mysql_real_escape_string($submitter_id)."'";
					$result = $this->doQuery($query, $this->link);
					if(mysql_num_rows($result) > 0) // User exists.
					{
						$this->error .= 2;
						return array(false, "proxy_exist");//$id;
					}
					else
					{					
						// Insert proxy
						$value_str = "'".mysql_real_escape_string($id)."','".mysql_real_escape_string($submitter_id)."'";
						$query = "INSERT INTO $this->proxy (proxyid, authorid) VALUES (".$value_str.")";
						$result = $this->doQuery($query, $this->link);
						//$this->error .= "Proxy added succesfully.<br />";
						return array(true, $id);
					}
				}
				else
				{
					$this->error .= 2; //2
					return $result;	// Return user error results.
				}	
			}
			else
			{
				$this->error .= 2;
				return $result;  // User creation error.
			}
		}
	}
	
	function remove_user($user_id, $submitter)
	{
		$this->link = $this->connectDB();	
		// Check if submitter allowed to delete users
		if($this->check_admin_user($submitter) == false)
		{
			$this->error .= 2; 
			return false; //array(false, "no_permission");
		}
		else
		{
			// Remove proxy - User can be both proxy and author.
			$query = "DELETE FROM $this->proxy WHERE proxyid = '".$user_id."' OR authorid ='".$user_id."'";
			$result = $this->doQuery($query, $this->link);
			if($result){
				// Remove user
				$query = "DELETE FROM $this->table WHERE id = '".$user_id."'";
				$result = $this->doQuery($query, $this->link);
				if($result){
					//$this->error .= "User deleted succesfully.<br />";
					return $user_id;
				}
				else{
					$this->error .= 2;
					return false;
				}
			}
			else{
				$this->error .= 2;
				return false;
			}
		}
	}
	
	function remove_proxy($user_id, $submitter)
	{
		$this->link = $this->connectDB();
		
		// Check submitter id
		$query = "SELECT * FROM $this->table WHERE username='".mysql_real_escape_string($submitter)."'";
		$result = $this->doQuery($query, $this->link);
		if(mysql_num_rows($result) == 1)
		{
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$submitter_id = $row['id'];
			
			// Remove proxy
			$query = "DELETE FROM $this->proxy WHERE proxyid = '".$user_id."' AND authorid ='".$submitter_id."'";
			$result = $this->doQuery($query, $this->link);
			//$this->error .= "Proxy deleted succesfully.<br />";
			return $user_id;
		}
		else
		{
			$this->error .= 2;
			return false;
		}
	}
		
	function update_user($id, $username, $firstname, $lastname, $submitter)
	{
		$this->link = $this->connectDB();
		
		// Check if submitter allowed to update users
		if($this->check_admin_user($submitter) == false)
		{
			$this->error .= 2; 
			return false; //array(false, "no_permission");
		}
		else
		{
			// Query username to check if user exist
			$query = "SELECT * FROM $this->table WHERE id='".mysql_real_escape_string($id)."'";
			$result = $this->doQuery($query, $this->link);
			if (mysql_num_rows($result) == 1) 		// Found exactly one user
			{
				$value_str = "username='".mysql_real_escape_string($username)."', firstname='".mysql_real_escape_string($firstname)."', lastname='".mysql_real_escape_string($lastname)."'";
				$query = "UPDATE $this->table SET $value_str WHERE id='".mysql_real_escape_string($id)."'";
				$result = $this->doQuery($query, $this->link);
				if (!$result) { 
					$this->error .= 2; 
					return false;	
				}
				else { 
					//$this->error .= "User updated successfully [".$id."].<br />"; 				
					return $id;
				}
			}
			else									// More than one user - Error
			{
				$this->error .= 2;
				return false;
			}
		}
	}
	
	function get_user($user_id, $submitter)
	{
		$this->link = $this->connectDB();
		
		// TO-DO: Check if submitter allowed to view users
		$query = "SELECT * FROM $this->table WHERE id='".$user_id."'"; 
		$result = $this->doQuery($query, $this->link);
		if(mysql_num_rows($result) > 0)
		{
			$result_arr = array();
			while(($result_arr[] = mysql_fetch_assoc($result)) || array_pop($result_arr));  // Copy result into an array
			mysql_free_result($result);
			return $result_arr;
		}
		else
		{
			$this->error .= 2;
			return false;
		}		
	}
	
	function get_all_users($submitter)
	{
		$this->link = $this->connectDB();
		
		// Check if submitter allowed to view users
		if($this->check_admin_user($submitter) == false)
		{
			$this->error .= 2; 
			return false; //array(false, "no_permission");
		}
		else 
		{
			$query = "SELECT * FROM $this->table ";
			$result = $this->doQuery($query, $this->link);
			if(mysql_num_rows($result) > 0)
			{
				$result_arr = array();
				while(($result_arr[] = mysql_fetch_assoc($result)) || array_pop($result_arr));  // Copy result into an array
				mysql_free_result($result);
				return $result_arr;
			}
			else
			{
				$this->error .= 2;
				return false;
			}		
		}
	}
	
	function get_faculty()
	{
		$this->link = $this->connectDB();
		
		
		$query = "SELECT u.*, c.collection_id FROM $this->table u, collections c where u.cogs=1 AND u.username=c.owner AND c.collection_name='My Representative Publications'  ORDER BY u.lastname, u.firstname";
		$result = $this->doQuery($query, $this->link);
		if(mysql_num_rows($result) > 0)
		{
			$result_arr = array();
			while(($result_arr[] = mysql_fetch_assoc($result)) || array_pop($result_arr));  // Copy result into an array
			mysql_free_result($result);
			return $result_arr;
		}
		else
		{
			$this->error .= 2;
			return false;
		}		
		
	}
	
	function get_all_proxies($submitter)
	{
		$this->link = $this->connectDB();
				
		// Check submitter id
		$query = "SELECT * FROM $this->table WHERE username='".mysql_real_escape_string($submitter)."'";
		$result = $this->doQuery($query, $this->link);
		if(mysql_num_rows($result) == 1)
		{
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$submitter_id = $row['id'];
			
			if ($row['admin'] == 1)
			{
				$query = "SELECT u.* FROM $this->table u";
			}
			else
			{
			// Get all proxies
				$query = "SELECT u.* FROM $this->proxy p, $this->table u WHERE authorid='".mysql_real_escape_string($submitter_id)."' AND p.proxyid = u.id";
			}
			$result = $this->doQuery($query, $this->link);
			if(mysql_num_rows($result) > 0)
			{
				$result_arr = array();
				while(($result_arr[] = mysql_fetch_assoc($result)) || array_pop($result_arr));  // Copy result into an array
				mysql_free_result($result);
				return $result_arr;
			}
			else
			{
				$this->error .= 2;
				return false;
			}
		}
		else
		{
			$this->error .= 2;
			return false;
		}
	}
	
	//Abhinav
	
	function get_all_sortproxies($submitter)
	{
		$this->link = $this->connectDB();
				
		// Check submitter id
		$query = "SELECT * FROM $this->table WHERE username='".mysql_real_escape_string($submitter)."'";
		$result = $this->doQuery($query, $this->link);
		if(mysql_num_rows($result) == 1)
		{
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$submitter_id = $row['id'];
			
			// Get all proxies
			$query = "SELECT u.* FROM $this->proxy p, $this->table u WHERE authorid='".mysql_real_escape_string($submitter_id)."' AND p.proxyid = u.id ORDER BY username";
			$result = $this->doQuery($query, $this->link);
			if(mysql_num_rows($result) > 0)
			{
				$result_arr = array();
				while(($result_arr[] = mysql_fetch_assoc($result)) || array_pop($result_arr));  // Copy result into an array
				mysql_free_result($result);
				return $result_arr;
			}
			else
			{
				$this->error .= 2;
				return false;
			}
		}
		else
		{
			$this->error .= 2;
			return false;
		}
	}
	
	function get_all_owners($submitter)
	{
		$this->link = $this->connectDB();
				
		// Check submitter id
		$query = "SELECT * FROM users WHERE username='".mysql_real_escape_string($submitter)."'";
		$result = $this->doQuery($query, $this->link);
		if(mysql_num_rows($result) == 1)
		{
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$submitter_id = $row['id'];
			
			if ($row['admin'] == 1)
			{
				$query = "SELECT u.* FROM $this->table u ORDER BY lastname";
			}
			else
			{
			// Get all owners
				$query = "SELECT u.* FROM proxy_of p, users u WHERE proxyid='".mysql_real_escape_string($submitter_id)."' AND p.authorid = u.id ORDER BY lastname";
			}
			$result = $this->doQuery($query, $this->link);
			if(mysql_num_rows($result) > 0)
			{
				$result_arr = array();
				while(($result_arr[] = mysql_fetch_assoc($result)) || array_pop($result_arr));  // Copy result into an array
				mysql_free_result($result);
				return $result_arr;
			}
			else
			{
				$this->error .= 2;
				return false;
			}
		}
		else
		{
			$this->error .= 2;
			return false;
		}
	}
	
	function verify_ads_user($account, $username)
	{
		$ldap = $this->create_ldap_obj($account);

		if($ldap)
		{
			if($account == "ads") 
			{
				$filter = "(&(objectClass=user) (samaccountname=".$username."))";
			}
			else // ($account == "guest")  
			{ 
				// Find exact User object (note no * wildcard) that have "mail" or "samaccountname" match $value (value could be 8-digits ID or email address).
				$filter = "(&(objectClass=user) (|(mail=".$username.") (samaccountname=".$username.")))";  
			}
			
			$result=$ldap->search_user($filter,array("givenname","sn","samaccountname","mail","department","displayname","telephonenumber","primarygroupid"));
			
			if($result['count'] == 1)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			$this->error .= 4;
			return false;		
		}
	}
	
	function get_real_username($account, $username)  // Return email username for guest account instead of CAS 8 digits.
	{
		$ldap = $this->create_ldap_obj($account);
		
		if($ldap)
		{
			if($account == "guest") 
			{
				// Find exact User object (note no * wildcard) that have "mail" or "samaccountname" match $value (value could be 8-digits ID or email address).
				$filter = "(&(objectClass=user) (|(mail=".$username.") (samaccountname=".$username.")))"; 
			}
			else // ($account == "ads")  
			{ 
				$filter = "(&(objectClass=user) (samaccountname=".$username."))"; 
			}
			
			$result=$ldap->search_user($filter,array("givenname","sn","samaccountname","mail","department","displayname","telephonenumber","primarygroupid"));
			
			if($account == "ads")				
			{
				return $result[0]['samaccountname'][0];
			}
			else if($account == "guest")
			{
				return $result[0]['mail'][0];  // Use email instead for guest account.
			}
			else 
			{
				return $username;			   // Simply return original username.
			}
		}
		else
		{
			$this->error .= 4;
			return $username;		
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
	
	// Create connection to database.
	function connectDB()
	{
		$link;
		if (!$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)) {
    		$this->error .= 2;
		}
			
		if (!mysql_select_db(DB_NAME, $link)) {
			$this->error .= 2;
		}
		return $link;
	}
	
	function doQuery($query, $link) 
	{	
		if (!$result = mysql_query($query, $link)) {
			$this->$error = 1;
		}
		return $result;	
	}
	
/*	function mysql_error_check($result)
	{
		if (!$result) {
			$this->error .= "DB Error, could not query the database<br />";
			$this->error .= 'MySQL Error: ' . mysql_error() . '<br />';
			//exit;
		}
	}
	
	function mysql_error_check_q($result, $query)
	{
		if (!$result) {
			$this->error .= "DB Error, could not query the database<br />";
			$this->error .= 'MySQL Error: ' . mysql_error() . '<br />';
			$this->error .= 'MySQL Query: <strong>' . $query . '</strong><br />';
			//exit;
		}
	}*/	
}

?>

