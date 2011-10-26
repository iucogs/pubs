<?php 

	function parseEntryToDB($entry)
	{
		if ($citation_id = compareEntryAndHandleDuplicateRaw($entry))
		{
		}
		else
		{	
			$citation_id = insertOneCitation($entry);
			
			//==============================================
			// Prep array for fuzzy match argument.
			//----------------------------------------------
			
			// Split / Prep first author lastname
			
			/******* MOVED TO PUBS (parser.php) *******
			$first_auth_name = nameSplitter($entry['author'][0]);
			$lastname = $first_auth_name['lastname'];
			
			$fuzzy_args = array("lastname" => $lastname, "year" => $entry['year'][0], "title" => $entry['title']);
			
			doFuzzyMatch($fuzzy_args, $citation_id);  // Does fuzzy match and updates similarTo table
			/******************************************/
		}
		return $citation_id;
	}
	
	function compareEntryAndHandleDuplicateRaw($entry)
	{
			// Query entries with same raw.
		$duplicate_raw_query = "SELECT c.citation_id, c.raw FROM citations c WHERE raw='".mysql_real_escape_string($entry['raw'])."'";
		$duplicate_raw_result = mysql_query($duplicate_raw_query);
		query_result($duplicate_raw_result, $duplicate_raw_query);
		
		if(mysql_num_rows($duplicate_raw_result) > 0)  // Duplicate raw exists
		{
			// Change the existing timestamp			
			$row = mysql_fetch_assoc($duplicate_raw_result);  // Get first row			
			if(changeTimestamp($row['citation_id'],$entry['entryTime']))
			{
				// Then return citation id.
				return $row['citation_id'];
			}
		}
		else
		{
			return false;	
		}
	}
	
	function insertOneCitation($entry) 
	{		
		$query = "INSERT INTO citations (pubtype, journal, title, raw, year, volume, chapter, number, pages, format, booktitle, editor, publisher, location, url, submitter, owner, entryTime) VALUES ('".mysql_real_escape_string($entry['type'])."', '".mysql_real_escape_string($entry['name'])."', '".mysql_real_escape_string($entry['title'])."', '".mysql_real_escape_string($entry['raw'])."', '".mysql_real_escape_string($entry['year'][0])."', '".mysql_real_escape_string($entry['volume'])."', '".mysql_real_escape_string($entry['chapter'])."', '".mysql_real_escape_string($entry['number'])."', '".mysql_real_escape_string($entry['pages'])."', '".mysql_real_escape_string($entry['format'])."', '".mysql_real_escape_string($entry['booktitle'])."', '".mysql_real_escape_string($entry['editor'])."', '".mysql_real_escape_string($entry['publisher'])."', '".mysql_real_escape_string($entry['location'])."', '".mysql_real_escape_string($entry['url'])."', '".mysql_real_escape_string($entry['submitter'])."', '".mysql_real_escape_string($entry['owner'])."','".mysql_real_escape_string($entry['entryTime'])."')"; 
				
		$result = mysql_query($query);
		query_result($result, $query);
		
		$author_id = -1;	
		$citation_id = (int)mysql_insert_id();
		
		$queryArray = array();
		
		if(empty($citation_id) || ($citation_id == 0))
		{
			$msg = "<br />Author ID: $author_id <br /> Query: $query <br /> ";
			$msg .= print_r($entry);
			die($msg);
		}
		
		// Write authors
		$position_num = 1;
		foreach($entry['author'] as $author){

			$name = nameSplitter($author);  // Split firstname and lastname
			$firstname = $name['firstname'];
			$lastname = $name['lastname'];
			
			// TO-DO: Remove fn.
			//$firstname = preg_replace('/[A-Z][a-z][^.]*[.]?$/', "", $firstname); 
			
			// Add slashes for db entry
			$slash_lastname = mysql_real_escape_string($lastname);
			$slash_firstname = mysql_real_escape_string($firstname);
			
			// Check for existing first name and last name in author table
			$query = "SELECT * FROM authors WHERE lastname='".$slash_lastname."' AND firstname='".$slash_firstname."'";
			$result = mysql_query($query);
			query_result($result, $query);
			
			$num_rows = mysql_num_rows($result);  		// Number of rows that database output
			
			// Handle duplicate entries
			
			// Haven't yet handled case where there is more than one exact duplicate author
			
			if($num_rows >= 1){  						// Exactly one copy of entry? exist
				$row = mysql_fetch_array($result);			
				$author_id = $row['author_id'];  		// Use existing author_id
								
				// Insert author_of
				$query = "INSERT INTO author_of (author_id, citation_id, position_num) VALUES ('$author_id', '$citation_id', '$position_num')";
				$result = mysql_query($query);
				query_result($result, $query);
			}
			else{  	
					// Put unverified authors into an array
					$queryArray[$position_num-1] = array($slash_lastname, $slash_firstname);
			}
						
			$position_num++;
		}
		
		if (!empty($queryArray))
		{
			$query_keys = "";
			$query_values = "";
			foreach ($queryArray as $key => $val)
			{
				$query_keys .= ", author".($key)."ln, author".($key)."fn ";
				$query_values .= ", '$val[0]', '$val[1]' ";
			}
			$query = "INSERT INTO authors_unverified (citation_id".$query_keys.") VALUES ('".$citation_id."'".$query_values.")";
			$result = mysql_query($query);
		}
			
		// Citation ID
		return $citation_id;
	}
	
	function changeTimestamp($citation_id, $timestamp)
	{
		$query = "UPDATE citations SET entryTime = '$timestamp' WHERE citation_id = $citation_id ";
		$result = mysql_query($query);
		query_result($result, $query);
		if(!$result) {
			return false;
		}
		else {
			return true;
		}
	}

	function nameSplitter($author)
	{	
		$nameSplit = explode(',', $author, 2);  // Split first name and last name
		$lastname = $nameSplit[0];
		if(sizeof($nameSplit) < 2){   			// Name without comma or comma at the end     
			$firstname = "";
		}
		else{
			$firstname = $nameSplit[1];
		}
		
		// Remove comma and/or spaces at the end of firstname
		$firstname = preg_replace('/\s*[,]*$/', "", $firstname);
		
		// Remove extra spaces
		$firstname = trim($firstname);
		$lastname = trim($lastname);
		
		$name = array('firstname' => $firstname, 'lastname' => $lastname);
		
		return $name;
	}
	
	function print_str_array($arr)
	{	
		$str = "";
		
		foreach($arr as $key => $a)
		{
			if($key == sizeof($arr)-1) {
				$str .= $a."";		// Don't print semi-colon after last item.
			}
			else {
				$str .= $a."; ";	// Separate each item with a semi-colon.
			}
		}
		
		return $str;
	}
	
	function query_result($result, $query)
	{
		if (!$result) {
			$message  = '<b>Invalid query:</b> ' . mysql_error() . "<br />";
			$message .= '<b>Whole query:</b> ' . $query . "<br />";
			die($message);
		}
	}
	
	function doFuzzyMatch($fuzzy_args, $citation_id)
	{
		define("FUZZY_MATCH_RATIO", 0.5);
	
		$query = "SELECT c.citation_id, a.lastname, c.year, c.title FROM citations c, authors a, author_of ao WHERE a.author_id = ao.author_id and c.citation_id = ao.citation_id AND ao.position_num = 1 UNION SELECT c.citation_id, au.author0ln, c.year, c.title FROM citations c, authors_unverified au WHERE c.citation_id = au.citation_id";
		
		$result = mysql_query($query);
		query_result($result, $query);
		$ratios_array = array();
		$return_value = true;
				
		if (mysql_num_rows($result) > 0){  
			while ($row = mysql_fetch_assoc($result))
			{	
				if ($citation_id != $row['citation_id'])
				{	
					// Do Fuzzy Match
					if (($ratio = fuzzy_match($fuzzy_args['lastname'], $row['lastname'], 2)) > FUZZY_MATCH_RATIO)
					{
						$lastname_ratio = $ratio;
						if (($ratio = fuzzy_match($fuzzy_args['year'], $row['year'], 1)) > FUZZY_MATCH_RATIO)
						{
							$year_ratio = $ratio;
							if (($ratio = fuzzy_match($fuzzy_args['title'], $row['title'], 1)) > FUZZY_MATCH_RATIO)
							{
								$title_ratio = $ratio;
								
								// Assign citation_id2 since all 3 criterias match.
								$citation_id2 = $row['citation_id'];  
								
								// Save all ratios in array. To be written into DB later.
								$ratios_array[] = array("citation_id1" => $citation_id, "citation_id2" => $citation_id2, "lastname_ratio" => $lastname_ratio, "year_ratio" => $year_ratio, "title_ratio" => $title_ratio);
							}
						}
					}		
				}
				else {} // Same citation_id. Do not fuzzy match.
			}
		}	
		
		if(!empty($ratios_array))
		{
			$return_value = insertIntoSimilarToTable($ratios_array);
		}
		
		return $return_value;
	}
	
	function insertIntoSimilarToTable($ratios_array)
	{
	
		// Insert or Delete duplicates in similar_to
		foreach($ratios_array as $ratios)
		{
			$citation_id1 = $ratios['citation_id1'];
			$citation_id2 = $ratios['citation_id2'];
		
			// Check if citation_id1 and citation_id2 pair exists.
			$query = "SELECT * FROM similar_to WHERE (citation_id1 = '$citation_id1' AND citation_id2 = '$citation_id2') OR (citation_id1 = '$citation_id2' AND citation_id2 = '$citation_id1'); ";
			$result = mysql_query($query);
			query_result($result, $query);
			
			// Should only have one value since similar_to's citation_id1 and citation_id2 are reflexive.
			if (mysql_num_rows($result) > 1)
			{  
				$count = 0;
				
				// Should delete all other pairs.
				while ($row = mysql_fetch_assoc($result))
				{	
					if($count > 0) // Skip first pair.
					{
						//$del_id1 = $citation_id1;
						//$del_id2 = $citation_id2;
						//
						//if($row['citation_id1'] != $citation_id1) // Swap values
						//{
						//	$del_id1 = $citation_id2;
						//	$del_id2 = $citation_id1;
						//}
						//$query = "DELETE FROM similar_to WHERE citation_id1=$del_id1 AND citation_id2=$del_id2";
						$query = "DELETE FROM similar_to WHERE id = '".$row['id']."'; ";
						$result = mysql_query($query);
						query_result($result, $query); 
					}
					$count++;
				}
			}
			else if(mysql_num_rows($result) == 1)   // Skip since only one value exist
			{
				
			}
			else // No match. mysql_num_rows = 0 or less.
			{
				$query = "INSERT INTO similar_to (citation_id1, citation_id2, lastname_ratio, year_ratio, title_ratio) VALUES ('".$ratios['citation_id1']."','".$ratios['citation_id2']."','".$ratios['lastname_ratio']."','".$ratios['year_ratio']."','".$ratios['title_ratio']."')";
				$result = mysql_query($query);
				query_result($result, $query);
			}
		}
		
		
		// No longer similar?
		// Get all similar_to rows with either citation_id1 (Primary citation_id that is being saved/edit/update).
		// All reflexive rows with citation_id1 in it.
		foreach($ratios_array as $ratios)
		{	
			$citation_id1 = $ratios['citation_id1'];
			$citation_id2 = $ratios['citation_id2'];
		
			$query = "SELECT * FROM similar_to WHERE citation_id1 = '$citation_id1' OR citation_id2 = '$citation_id1'; ";
			$result = mysql_query($query);
			query_result($result, $query);
			
			// Compare with $ratios_array for missing values in queried table (means that the missing reflexive row(s) are no longer similar to citationd_id1)
			if (mysql_num_rows($result) > 0)
			{  
				// Should delete missing row with citation_id2 that does not exist in $ratios_array 
				while ($row = mysql_fetch_assoc($result))
				{	
					if($citation_id2 == $row['citation_id2'])
					{
						// Still similar
					}
					else if($citation_id2 == $row['citation_id1'])
					{
						// Still similar
					}
					else
					{
						// No longer similar since citation_id2 does not exist in ratios_array. (Delete row).
						$query = "DELETE FROM similar_to WHERE id = '".$row['id']."'; ";
						$result = mysql_query($query);
						query_result($result, $query);
					}
				}
			}
		}
		
		return true;
	}
	
	
	/******************************* OLD FUZZY MATCH *********************************
	
		function doFuzzyMatch($entry, $citation_id)
	{
		define("FUZZY_MATCH_RATIO", 0.5);
	
		$query = "SELECT c.citation_id, a.lastname, c.year, c.title FROM citations c, authors a, author_of ao WHERE a.author_id = ao.author_id and c.citation_id = ao.citation_id AND ao.position_num = 1 UNION SELECT c.citation_id, au.author0ln, c.year, c.title FROM citations c, authors_unverified au WHERE c.citation_id = au.citation_id";
		
		$result = mysql_query($query);
		query_result($result, $query);
		$ratios_array = array();
		$return_value = true;
				
		if (mysql_num_rows($result) > 0){  
			while ($row = mysql_fetch_assoc($result))
			{	
				if ($citation_id != $row['citation_id'])
				{
					// Split / Prep first author lastname
					$first_auth_name = nameSplitter($entry['author'][0]);
					$ln = $first_auth_name['lastname'];
					
					// Do Fuzzy Match
					if (($ratio = fuzzy_match($ln,$row['lastname'],2)) > FUZZY_MATCH_RATIO)
					{
						$citation_id2 = $row['citation_id'];
						$lastname_ratio = $ratio;
						if (($ratio = fuzzy_match($entry['year'][0],$row['year'],1)) > FUZZY_MATCH_RATIO)
						{
							$year_ratio = $ratio;
							if (($ratio = fuzzy_match($entry['title'],$row['title'],1)) > FUZZY_MATCH_RATIO)
							{
								$title_ratio = $ratio;
								$ratios_array[] = array("citation_id1" => $citation_id, "citation_id2" => $citation_id2, "lastname_ratio" => $lastname_ratio, "year_ratio" => $year_ratio, "title_ratio" => $title_ratio);
							}
						}
					}
				}
			}
		}	
		
		if(!empty($ratios_array))
		{
			$return_value = insertIntoSimilarToTable($ratios_array);
		}
		
		return $return_value;
	}
	
	function insertIntoSimilarToTable($ratios_array)
	{
		foreach($ratios_array as $ratios)
		{
			$query = "INSERT INTO similar_to (citation_id1, citation_id2, lastname_ratio, year_ratio, title_ratio) VALUES ('".$ratios['citation_id1']."','".$ratios['citation_id2']."','".$ratios['lastname_ratio']."','".$ratios['year_ratio']."','".$ratios['title_ratio']."')";
			
			$result = mysql_query($query);
			query_result($result, $query);
		}
		
		return true;
	}
	
	****************************** OLD FUZZY MATCH ***********************************/
	
	function findExactMatchesOnFirstAuthorTitleYear()  // not used currently  -- but SAVE
	{
			/*else	// Check for title, year and author of verified entries.
		{
				
			// Query entries with same title, same year and return first author.
			$duplicate_query = "SELECT c.citation_id, c.title, c.year, a.* FROM citations c, authors a, author_of ao WHERE title='".mysql_real_escape_string($entry['title'])."' AND year='".mysql_real_escape_string($entry['year'][0])."' AND a.author_id = ao.author_id and c.citation_id = ao.citation_id AND ao.position_num = 1 AND c.verified = 1";
			
			$duplicate_result = mysql_query($duplicate_query);
			query_result($duplicate_result, $duplicate_query);
			
			if(mysql_num_rows($duplicate_result) > 0)
			{
				// Check for same first Author
				while($row = mysql_fetch_assoc($duplicate_result))
				{
					$first_auth_name = nameSplitter($entry['author'][0]);
					$fn = $first_auth_name['firstname'];
					$ln = $first_auth_name['lastname'];
								
					if($row['firstname'] == $fn && $row['lastname'] == $ln)  // First author name are the same
					{
						// Change timestamp first then return citation id.
						if(changeTimestamp($row['citation_id'],$entry['entryTime']))
						{
							// Then return citation id.
							return $row['citation_id'];
						}
					}
				}			
			}
		}*/	
	}
?>