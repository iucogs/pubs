<?php 

	function book_db_search_first($str)
	{
		$BookTitle = "unknown";
		$Publisher = "unknown";
		$PublisherLoc = "unknown";
		//$Editor = "unknown";
		$found = false;
	
		$parsePubSearch = parsePublisherSearch($str);
		
		if($parsePubSearch['found'])								// Do DB Search first
		{	
			$found = true;
			$Publisher = $parsePubSearch['Publisher'];
			$PublisherLoc = $parsePubSearch['PublisherLoc'];
			
			if(!empty($PublisherLoc))
			{
				$pos = mb_strpos($str,$PublisherLoc); 
				if($pos === false){}
				else
				{
					$BookTitle = clean(mb_substr($str, 0, $pos));
					// Parse Editors here.
/*					if(preg_match('/(.*)\b(edited\sby|ed\.|eds\.|eds|ed)\b(.*)/i', $temp, $match) == 1)
					{
						$BookTitle = clean($match[1]);
						//$Editor = parseAuthor($match[3]);
						//$Editor = $match[3];
						//echo "Editor: $Editor<br />";
					}
					else
					{
						//$BookTitle = clean(substr($str, 0, $pos));
					}*/
				}
			}
			else  // Publisher Location not found --> everything before the Publisher is the BookTitle
			{
				$BookTitle = clean(str_replace($Publisher,'',$str));
			}
		}
		else 
		{
			$found = false;
		}
		
		$returnArray = array('found' => $found, 'BookTitle' => $BookTitle, 'Publisher' => $Publisher, 'PublisherLoc' => $PublisherLoc);
		
		return $returnArray;
	}

	function parseBook($str)
	{
		$result = book_db_search_first($str);
		if($result['found'])
		{
			return $result;
		}
		
		$found = false;
		$charlist = REGEX_ESCAPE_CHAR_LIST; //"/)('\"[]?";  // For addcslashes()
		
		$BookTitle = "unknown";
		$Publisher = "unknown";
		$PublisherLoc = "unknown";
		$Editor = "unknown";
	
		$colon = explode(':',$str);
		$colon_num = sizeof($colon);
		
		if($colon_num == 2) 	// Location:Publisher OR Title (Depends on the location on the string)
		{
			$found = 1;
			
			$pattern2 = '/[.]([^.]*)$/';
			$pattern = '/[,]([^,]*)$/';
			
			// Look for Publicher's Location
			if(preg_match($pattern, $colon[0], $match))		// Look for [,] LOCATION [:]
			{		
				if(str_word_count_utf8($match[1],0) == 1)				// Check if LOCATION == "MA, IN, ... " Abbreviated state etc..
				{
					// Check all Capital in Location
					$capital_pattern = '/^[A-Z]*$/';
					
					$trimmed = trim($match[1]);
												
					if(preg_match($capital_pattern, $trimmed)) 	// One word and capital
					{							
						// Take another part up to a comma
						$comma_pattern = '/([,]|[.])([^,.]*[,][^,]*)$/';
						if(preg_match($comma_pattern, $colon[0], $match2))
						{
							$PublisherLoc = locationLengthCheck($match2[2]);
							$BookTitle = preg_replace('/'.addcslashes($PublisherLoc, $charlist).'.*$/', '', $colon[0]);
						}
					}
					else										// One word but not capital
					{
						//  Check if the previous word followed by [.]
						$dot_pattern = '/[.]([^.]*'.addcslashes($trimmed, $charlist).'.*)$/';
						if(preg_match($dot_pattern, $colon[0], $match3))
						{
							$PublisherLoc = locationLengthCheck($match3[1]);
							$BookTitle = preg_replace('/'.addcslashes($PublisherLoc, $charlist).'.*$/', '', $colon[0]);
						}
						else
						{
							$PublisherLoc = locationLengthCheck($trimmed);
							$BookTitle = preg_replace('/'.addcslashes($PublisherLoc, $charlist).'.*$/', '', $colon[0]);
						}
					}
				}
				else											// More than one word
				{
					$PublisherLoc = locationLengthCheck($match[1]);
					$BookTitle = preg_replace('/'.addcslashes($PublisherLoc, $charlist).'.*$/', '', $colon[0]);
				}
			}
			else if(preg_match($pattern2, $colon[0], $match))	// Look for [.] LOCATION [:]
			{		
				if(str_word_count_utf8($match[1],0) == 0)				// If no word then its an abbrev location (e.g. Cambridge, Mass.)
				{
					$match_temp = preg_replace($pattern2,'',$colon[0]);		// Replace [.]
					if(preg_match($pattern2, $match_temp, $match)){			// Redo pattern match
						$PublisherLoc = locationLengthCheck($match[1]);
						$BookTitle = preg_replace('/'.addcslashes($PublisherLoc, $charlist).'.*$/', '', $colon[0]);
					}
				}
				else
				{
					$PublisherLoc = locationLengthCheck($match[1]);
					$BookTitle = preg_replace('/'.addcslashes($PublisherLoc, $charlist).'.*$/', '', $colon[0]);
				}
			}
			else{}
			
			// Look for Publisher
			$Publisher = $colon[1];		// Assign the rest of the string as publisher.
		}
		else if($colon_num == 3) // Two colons, take second colon
		{
			$found = 1;
			
			$pattern2 = '/[.]([^.]*)$/';
			$pattern = '/[,]([^,]*)$/';
			
			// Look for Publicher's Location
			if(preg_match($pattern, $colon[1], $match))		// Look for [,] LOCATION [:]
			{	
				if(str_word_count_utf8($match[1],0) == 1)				// Check if LOCATION == "MA, IN, ... " Abbreviated state etc..
				{
					// Check all Capital in Location
					$capital_pattern = '/^[A-Z]*$/';
					
					$trimmed = trim($match[1]);
					
					if(preg_match($capital_pattern, $trimmed)) 	// One word and capital
					{							
						// Take another part up to a comma
						$comma_pattern = '/([,]|[.])([^,.]*[,][^,]*)$/';
						if(preg_match($comma_pattern, $colon[1], $match2))
						{
							$PublisherLoc = locationLengthCheck($match2[2]);
							$BookTitle = preg_replace('/'.addcslashes($PublisherLoc, $charlist).'.*$/', '', $colon[0].$colon[1]);
						}
					}
					else										// One word but not capital
					{
						//  Check if the previous word followed by [.]
						$dot_pattern = '/[.]([^.]*'.$trimmed.'.*)$/';
						if(preg_match($dot_pattern, $colon[1], $match3))
						{
							$PublisherLoc = locationLengthCheck($match3[1]);
							$BookTitle = preg_replace('/'.addcslashes($PublisherLoc, $charlist).'.*$/', '', $colon[0].$colon[1]);
						}
						else
						{
							$PublisherLoc = locationLengthCheck($trimmed);
							$BookTitle = preg_replace('/'.addcslashes($PublisherLoc, $charlist).'.*$/', '', $colon[0].$colon[1]);
						}
					}
				}
				else											// More than one word
				{
					$PublisherLoc = locationLengthCheck($match[1]);
					$BookTitle = preg_replace('/'.addcslashes($PublisherLoc, $charlist).'.*$/', '', $colon[0].$colon[1]);
				}
			}
			else if(preg_match($pattern2, $colon[1], $match))	// Look for [.] LOCATION [:]
			{		
				if(str_word_count_utf8($match[1],0) == 0)				// If no word then its an abbrev location (e.g. Cambridge, Mass.)
				{
					$match_temp = preg_replace($pattern2,'',$colon[1]);		// Replace [.]
					if(preg_match($pattern2, $match_temp, $match)){			// Redo pattern match
						$PublisherLoc = locationLengthCheck($match[1]);
						$BookTitle = preg_replace('/'.addcslashes($PublisherLoc, $charlist).'.*$/', '', $colon[0].$colon[1]);
					}
				}
				else
				{
					$PublisherLoc = locationLengthCheck($match[1]);			
					$BookTitle = preg_replace('/'.addcslashes($PublisherLoc, $charlist).'.*$/', '', $colon[0].$colon[1]);
				}
			}
			else{}
						
			// Look for Publisher
			$Publisher = $colon[2];  // Assign the rest of the string as publisher.
			
		}
		else if($colon_num > 3){}	// Think about this (Make a function with variable colon num and always use the last colon?)
		else{} 						// No colon. Do nothing
		
		
		// Result clean up
		$BookTitle = trim($BookTitle);
		$Publisher = trim($Publisher);
		$PublisherLoc = trim($PublisherLoc);

		// Clean junk at the beginning and end. [function clean() is in parseInbook.php]
		$BookTitle = clean($BookTitle);
		$Publisher = clean($Publisher);
		$PublisherLoc = clean($PublisherLoc);
		
		$returnArray = array('found' => $found, 'BookTitle' => $BookTitle, 'Publisher' => $Publisher, 'PublisherLoc' => $PublisherLoc, 'Editor' => $Editor);
		
		return $returnArray;
	}
	
	function locationLengthCheck($PublisherLoc)
	{
		// Check for how many words in a location.
		if(str_word_count_utf8($PublisherLoc, 0) > 5)
		{
			$pattern = '/([.]|[,])([^.,]*)$/';
			if(preg_match($pattern, $PublisherLoc, $match))
			{
				return $match[2];
			}
			else return "";
		}
		else return $PublisherLoc;
	}

?>
