<?php

require_once dirname(__FILE__).'/constants.php'; // Definition for DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_FLAGS

// Make the connnection and then select the database.
$dbc = @mysql_connect (DB_HOST, DB_USER, DB_PASSWORD) OR die ('Could not connect to MySQL: ' . mysql_error() );
mysql_select_db (DB_NAME) OR die ('Could not select the database: ' . mysql_error() );

// Telling MySQL that we're expecting and sending data as UTF8 and collation as utf8_general_ci
// MySQL charset is probably utf8 by default but we are setting it anyways.
// Collation in MySQL Database is a set of rules used in comparisons.
// For comparisons between 'utf8_general_ci' vs 'utf8_unicode_ci' visit: [http://forums.mysql.com/read.php?103,187048,188748#msg-188748]
$query = "SET NAMES 'utf8' COLLATE 'utf8_general_ci'";

$link;
if (!$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)) {
	echo "Error in mysql_connect.php";
}
	
if (!mysql_select_db(DB_NAME, $link)) {
	echo "Error in mysql_connect.php";
}

if (!$result = mysql_query($query, $link)) {
	echo "Error in mysql_connect.php";
}
		
?>