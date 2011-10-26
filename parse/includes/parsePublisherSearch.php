<?php 

function parsePublisherSearch($str)		// DB Search of publisher and location
{
	// Look in Journal DB first. Book and Inbook DB does not exist yet.
	$found = false;
	
	$Publisher = "";
	$PublisherLoc = "";
	$after_str1 = "";
	$after_str2 = "";
	$after_str = "";
	
	// Journal Name DB
	$col_pub = "publisher";
	$col_loc = "location";
	$table_pub = "publishers";
	$table_loc = "locations";
	
	$query_pub = "select ".$col_pub." from ".$table_pub." ORDER BY $col_pub DESC";  // Order descending so that longer string match first.
	$query_loc = "select ".$col_loc." from ".$table_loc." ORDER BY $col_loc DESC";

	$result_pub = mysql_query($query_pub);		
	$result_loc = mysql_query($query_loc);
	
	while($loc = mysql_fetch_assoc($result_loc))
	{
		if(($after_str1 = mb_strstr($str,$loc['location'])) == true)
		{
			$PublisherLoc = $loc['location'];
			$found = true;
			break;
		}			
	}

	while($pub = mysql_fetch_assoc($result_pub))
	{
		if(($after_str2 = mb_strstr($str,$pub['publisher'])) == true)
		{
			$Publisher = $pub['publisher'];
			$found = true;
			break;
		}
	}
	
	$after_str = (mb_strlen($after_str1) > mb_strlen($after_str2)) ? $after_str1 : $after_str2;
	
	$returnArray = array('found' => $found, 'Publisher' => $Publisher, 'PublisherLoc' => $PublisherLoc, 'AfterStr' => $after_str);
		
	return $returnArray;
}


?>
