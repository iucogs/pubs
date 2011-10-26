<?php 

function parseProceedingAPA($str)
{
	// clean the string for possible year with alphabets. (1999a)
	$str = removeYearAlphabetAtStart($str);	
	
	$found = false;
	$ProcTitle = "";
	$ProcName = "";
	$Publisher = "";
	$PublisherLoc = "";
	$publisher_str = "";
	
	$patterns[0] = '/(.*)(\.|\?|.)\s*(The\s?\bProceedings\b.*)(\,|\.)(.*)$/';
	$patterns[1] = '/(.*)(\.|\?|.)\s*(\bProceedings\b.*)(\,|\.)(.*)$/';
	$patterns[2] = '/(.*)((\.|\?).*\bTransactions\b.*)/';
	$patterns[3] = '/(.*)((\.|\?).*\bConference\b.*)/';
	
	for($i = 0; $i < sizeof($patterns); $i++)
	{
		if(preg_match($patterns[$i], $str, $match) == 1)	// Pattern match is true
		{
			$found = true;
			if($i == 0 || $i == 1)
			{
				$ProcTitle = clean($match[1]);				// First part of match
				$ProcName = clean($match[3]);				// Second part of match				
				$publisher_str = $match[5];
				$parsePubSearch = parsePublisherSearch($publisher_str);
				if($parsePubSearch['found'])								// Do DB Search first
				{	
					$Publisher = $parsePubSearch['Publisher'];
					$PublisherLoc = $parsePubSearch['PublisherLoc'];
				}
				else														// Pattern match if fails
				{
					$parsePublisher = parsePublisher($publisher_str);
					$Publisher = $parsePublisher['Publisher'];
					$PublisherLoc = $parsePublisher['PublisherLoc'];
				}
			}
			else
			{
				$ProcTitle = clean($match[1]);				// First part of match
				$ProcName = clean($match[2]);				// Second part of match
							
				$parsePublisher = parsePublisher($match[2]);
				$Publisher = $parsePublisher['Publisher'];
				$PublisherLoc = $parsePublisher['PublisherLoc'];
			}
				
			break;
		}
	}

	$returnArray = array('found' => $found, 'ProceedingTitle' => $ProcTitle, 'ProceedingName' => $ProcName, 'Publisher' => $Publisher, 'PublisherLoc' => $PublisherLoc);
		
	return $returnArray;
}

?>
