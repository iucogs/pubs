<?php 

	function parseEditedBook($beforeYearStr, $afterYearStr)
	{
		$found = false;
		$BookTitle = "unknown";
		$BookPublisher = "unknown";
		$BookPublisherLoc = "unknown";
		
		//echo "Before YR: $beforeYearStr<br />";
		//print_r($beforeYearStr);
	
		if(preg_match('/\s*(\beds\b\.|ed\.)\s*/i', $beforeYearStr, $match) == 1)
		{
			$found = true;
			
			$book = parseBook($afterYearStr);
			
			if($book['found'] == 1){  // Pattern matching.
				$BookTitle = $book['BookTitle'];
				$BookPublisher = $book['Publisher'];
				$BookPublisherLoc = $book['PublisherLoc'];
			}
		}
		
		$returnArray = array('found' => $found, 'BookTitle' => $BookTitle, 'Publisher' => $BookPublisher, 'PublisherLoc' => $BookPublisherLoc);
		return $returnArray;
	}
?>