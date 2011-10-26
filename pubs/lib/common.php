<?php 

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

?>