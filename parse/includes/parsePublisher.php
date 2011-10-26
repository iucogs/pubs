<?php 

function parsePublisher($str)
{
	//$charlist = "/)('\"[]?"; //REGEX_ESCAPE_CHAR_LIST

	$Publisher = "";
	$PublisherLoc = "";

	$colon = explode(':',$str);
	$colon_num = sizeof($colon);

	$pattern2 = '/[.]([^.]*)$/';
	$pattern = '/[,]([^,]*)$/';
	
	if($colon_num == 2)
	{
		// Look for Publicher's Location
		if(preg_match($pattern, $colon[0], $match))		// Look for [,] LOCATION [:]
		{
			if(str_word_count($match[1]) == 1)				// Check if LOCATION == "MA, IN, ... " Abbreviated state etc..
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
						//$BookTitle = preg_replace('/'.addcslashes($PublisherLoc, $charlist).'.*$/', '', $colon[0]);
					}
				}
				else										// One word but not capital
				{
					//  Check if the previous word followed by [.]
					$dot_pattern = '/[.]([^.]*'.$trimmed.'.*)$/';
					if(preg_match($dot_pattern, $colon[0], $match3))
					{
						$PublisherLoc = locationLengthCheck($match3[1]);
						//$BookTitle = preg_replace('/'.addcslashes($PublisherLoc, $charlist).'.*$/', '', $colon[0]);
					}
					else
					{
						$PublisherLoc = locationLengthCheck($trimmed);
						//$BookTitle = preg_replace('/'.addcslashes($PublisherLoc, $charlist).'.*$/', '', $colon[0]);
					}
				}
			}
			else											// More than one word
			{
				$PublisherLoc = locationLengthCheck($match[1]);
				//$BookTitle = preg_replace('/'.addcslashes($PublisherLoc, $charlist).'.*$/', '', $colon[0]);
			}
		}
		else if(preg_match($pattern2, $colon[0], $match))	// Look for [.] LOCATION [:]
		{		
			if(str_word_count($match[1]) == 0)				// If no word then its an abbrev location (e.g. Cambridge, Mass.)
			{
				$match_temp = preg_replace($pattern2,'',$colon[0]);		// Replace [.]
				if(preg_match($pattern2, $match_temp, $match)){			// Redo pattern match
					$PublisherLoc = locationLengthCheck($match[1]);
					//$BookTitle = preg_replace('/'.addcslashes($PublisherLoc, $charlist).'.*$/', '', $colon[0]);
				}
			}
			else
			{
				$PublisherLoc = locationLengthCheck($match[1]);
				//$BookTitle = preg_replace('/'.addcslashes($PublisherLoc, $charlist).'.*$/', '', $colon[0]);
			}
		}
		else{}
		
		// Look for Publisher
		$Publisher = $colon[1];		// Assign the rest of the string as publisher.
		
		$returnArray = array('Publisher' => $Publisher, 'PublisherLoc' => $PublisherLoc);
		
		return $returnArray;
	}
}

?>
