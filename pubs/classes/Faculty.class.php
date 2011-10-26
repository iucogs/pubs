<?php 

class Faculty
{
	var $link;
	var $table;
	var $notify;
	var $msg;

	function Faculty()
	{
		require_once('../lib/mysql_connect.php');
		//$this->table = 'citation';
		$this->notify = "";
		$this->msg = "";
	}
	
	function query_user($username)
	{
		$this->link = $this->connectDB();
		$query = "SELECT id FROM users WHERE username='".mysql_real_escape_string($username)."'";
		$result = mysql_query($query, $this->link);
		$this->mysql_error_check($result);
		if (mysql_num_rows($result) == 1) 		// One user
		{
			$row = mysql_fetch_row($result);
			return $row[0];
		}
		else
		{
			return false;
		}
	}
	
	function saveFacultyInfo($faculty, $arr)
	{
		if(($id = $this->query_user($faculty)) != false)
		{
			$this->link = $this->connectDB();
			
			$value_str = "";
			foreach($arr as $key => $value)
			{
				$value_str .= $key."='".mysql_real_escape_string($value)."', ";
			} 
			
			$value_str = substr($value_str,0,-2);
			$value_str .= " ";
						
			$query = "UPDATE facprofile SET ".$value_str." WHERE user_id='".$id."'";
			$result = mysql_query($query, $this->link);
			$this->mysql_error_check_q($result, $query);
			if (!$result) { 
				$this->notify .= "Error updating.<br />"; 
				$result_arr = false;	
			}
			else { 
				$this->notify .= "Updated successfully.<br />"; 				
				$result_arr = true;
			}
			return $result_arr;
		}
		else
		{
			return false;
		}
	}
	
	function getFacultyInfo($faculty)
	{
		if(($id = $this->query_user($faculty)) != false)
		{
			$this->link = $this->connectDB();
			$query = "SELECT * FROM facprofile WHERE user_id='".$id."'";
			$result = mysql_query($query, $this->link);
			$this->mysql_error_check($result);
			if (mysql_num_rows($result) > 0) 
			{
				$result_arr = array();
				while(($result_arr[] = mysql_fetch_assoc($result)) || array_pop($result_arr));  // Copy result into an array
			}
			else
			{	
				$result_arr = array();
			}
			mysql_free_result($result);
			return $result_arr;
		}
		else
		{
			return false;
		}
	}
	
	function getRepresentativePublications($faculty)
	{
		if(($id = $this->query_user($faculty)) != false)
		{
			$this->link = $this->connectDB();
			$query = "SELECT DISTINCT c.raw FROM users u, citation c, represent_pubs_of rp WHERE rp.citation_id=c.citation_id AND rp.user_id='".$id."'";
			$result = mysql_query($query, $this->link);
			$this->mysql_error_check($result);
			if (mysql_num_rows($result) > 0) 
			{
				$result_arr = array();
				while(($result_arr[] = mysql_fetch_assoc($result)) || array_pop($result_arr));  // Copy result into an array
			}
			else
			{	
				$result_arr = array();
			}
			mysql_free_result($result);
			return $result_arr;
		}
		else
		{
			return false;
		}
	}
	
	// Create connection to database.
	function connectDB()
	{
		$link;
		if (!$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)) {
    		$this->msg .= 'Could not connect to mysql<br />';
			//exit;
		}
			
		if (!mysql_select_db(DB_NAME, $link)) {
			$this->msg .= 'Could not select database<br />';
			//exit;
		}
		return $link;
	}
	
	function mysql_error_check($result)
	{
		if (!$result) {
			$this->msg .= "DB Error, could not query the database<br />";
			$this->msg .= 'MySQL Error: ' . mysql_error() . '<br />';
			//exit;
		}
	}
	
	function mysql_error_check_q($result, $query)
	{
		if (!$result) {
			$this->msg .= "DB Error, could not query the database<br />";
			$this->msg .= 'MySQL Error: ' . mysql_error() . '<br />';
			$this->msg .= 'MySQL Query: <strong>' . $query . '</strong><br />';
			//exit;
		}
	}
}

?>
