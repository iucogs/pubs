<?php

function parseAuthorAPA($str)
{
	// Prepare text string for processing	
	$str = preg_replace('/\s{2}/',' ', $str); 						   	        // Remove multiple spaces
	$str = preg_replace('/\s*(\beds\b\.|\bet al\b\.|ed\.)\s*/i', '', $str);     // Remove Edition: eds. | et al. | ed.
	$str = preg_replace('@[(]|[)]|[/]|[:]|[\[]|[\]]|[<]|[>]@',' ', $str); 		// Remove unusual characters
	
	// Separate string into chunks separated by "and" or "&"
	$split_array = preg_split('/[\s,]+(and|&)[\s,]+/i', $str, -1, PREG_SPLIT_NO_EMPTY);
	
	$result = array();
	
	// Run each chunk through parseAuthor and save author's name found into $result
	foreach($split_array as $key => $chunk)
	{
		$chunk_result = parseAuthor($chunk);
		foreach($chunk_result as $name)
		{
			if($name == "unknown")
			{
				// DB Search for possible remaining authors
				if(($searchedAuthorsName = parseAuthorSearch($chunk)) != false)
				{
					// Reconstruct author's name into [lastname, firstname,] and save in $result
					$result[] = $searchedAuthorsName['lastname'].", ".$searchedAuthorsName['firstname'].",";
				}
				else $result[] = $name;
			}
			else
			{
				$result[] = $name;
			}
		}
	}	
	
	return $result;
}

?>