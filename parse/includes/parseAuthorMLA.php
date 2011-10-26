<?php

function parseAuthorMLA($str)
{
	//echo "STR : ".$str."<br />";

	// Try and grab the author part.
	// 1. Use the double quote -> anything before double quote are names
	// 2. Starting of line up to a (.) are names
	// 3. Special MLA names condition where first name in the list starts with Lastname, Firstname, (the rest use First Middle Last)
	//$case = 0;
	$author = "unknown";
	$authorStr = "";
	$title = "";
	$afterAuthorStr = "";
	
	$titlePattern = '/^(.*)(["][^"]*["]|[\'][^\']*[\'])(.*)$/';  // (Names) ("Title")
	$editorsPattern = '/^(.*)(\bEd\b|\bEds\b)(.*)$/';  		 	 // (Names) (EDS.) Note: Capital E in pattern since ed. could be edition.
	$dotPattern = '/^([^.]*)[.](.*)$/';							 // (Names) up to first dot
	
	if(preg_match($titlePattern, $str, $match) == 1){			// Title
		$authorStr = processAuthNames($match[1]); 
		$afterAuthorStr = $match[2].$match[3];
		//$case = 1;
	}
	else if(preg_match($editorsPattern, $str, $match) == 1)		// EDS.
	{
		$authorStr = processAuthNames($match[1]); //$match[1];
		$afterAuthorStr = $match[3];
		//$case = 2;
	}	
	else if(preg_match($dotPattern, $str, $match) == 1)			// Dot
	{
		$authorStr = processAuthNames($match[1]); //$match[1];
		$afterAuthorStr = $match[2];
		//$case = 3;
	}
	else 
	{
		$authorStr = "unknown";
		$afterAuthorStr = $str;
		//$case = 4;
	}
	
	/*echo "AUTHOR_STR : ";
	print_r($authorStr);
	echo "<br />";*/
	
	// Parse Author - Handle special case.
	if($authorStr != "unknown")
	{
		$author = array();
		$author = parseAuthor($authorStr);
	}
	else
	{
		$author = array("unknown");
	}
	
	$returnArray = array('author' => $author, 'afterAuth' => $afterAuthorStr);
	
	/*echo "AUTHOR_R : ";
	print_r($author);
	echo "<br />";
	echo "AFTER_AUTH_STR : ";
	echo $afterAuthorStr."<br />";
	echo "Case : ".$case."<br />";*/

	
	return $returnArray;
}

function processAuthNames($str)   // Process names for TITLE, EDS and DOT as well.
{
	// Grab first name, first part
	// Check if it looks like a name
	// Grab first name, second part
	// Check if it looks like a name
	
	// Grab others
	// Send to parseAuthor()
	
	$firstAuthor = "";
	$otherAuthors = array();
	$remainingAuthor = "";
	
	$firstComma = '/^([^,]*)[,]/';   		// All first name, first part ends with a comma
	if(preg_match($firstComma, $str, $match) == 1)
	{
		// Check for names with more than 3 words
		if(str_word_count($match[1], 0) <= 3)
		{
			// Check for name abbreviation

			// ==============------
			// Check for second part (because if second part fails, first part fails) - put in a function?
			// ==============------
			
			// Grab remaining string to process second part
			$remainingStr = preg_replace($firstComma, '', $str);

			// Second part could end in a comma or a dot. Which ever comes first
			$commaOrDot = '/^([^.,]*)(\.|\,|$)/';
			if(preg_match($commaOrDot, $remainingStr, $matchSecond) == 1)
			{				
				// Check for names with more than 3 words
				if(str_word_count($matchSecond[1], 0) <= 3)
				{
					// Check for name abbreviation
					
					
					// Save first author
					$firstAuthor = $match[1].", ".$matchSecond[1];
					$remainingAuthor = preg_replace($commaOrDot, '', $remainingStr);  // Remaining string to look for more authors
				}
				else
				{
					$firstAuthor = "unknown";
				}
			} 
			else
			{
				$firstAuthor = "unknown";
			}
		}
		else
		{
			$firstAuthor = "unknown";
		}
	}
	else
	{
		$firstAuthor = "unknown";
	}
	
	//echo "REMAINING AUTHOR: ".$remainingAuthor."<br />";

	
	// Saved authors
	$savedAuthor = "";
	
	// Check for more names
	if(str_word_count($remainingAuthor, 0) > 2)  // Consists of at least firstname and lastname (two words)
	{
		$found = false;
		$loop_count = 0;
		while(str_word_count($remainingAuthor, 0) != 0)
		{
			// Infinite loop control
			if($loop_count > 50)
			{
				$savedAuthor = "";
				break;			
			}
		
			// Check for AND, &
			$andPattern = '/^[,]*\s*(\band\b|[&])/';
			if(preg_match($andPattern, $remainingAuthor, $match) == 1)  // There's only one more name
			{
				if(checkNameFormat($remainingAuthor) == "firstname")	// -- Check for which name format
				{
					// Check for $found
					if(startsWithAnd($remainingAuthor, 'firstname') == true) 	// [firstname lastname]
					{		
						// Remove starting and
						$remainingAuthor = removeStartingAnd($remainingAuthor);
						$savedAuthor = $savedAuthor." ".flipLastAndFirst($remainingAuthor).", ";   // Flip name for parseAuthor()
						$found = true;
					}
					else
					{
						$found = false;
					}
				}
				else   // Lastname format
				{
					// Check for $found
					if(startsWithAnd($remainingAuthor, 'lastname') == true)  	// [lastname, firstname]
					{
						// Remove starting and
						$remainingAuthor = removeStartingAnd($remainingAuthor);
						$savedAuth = $savedAuthor." ".$remainingAuthor.", ";
						$found = true;
					}
					else
					{
						$found = false;
					}
				}
				
				$remainingAuthor = "";
				break;
			}
			else   // There's more than one name
			{			
				if(checkNameFormat($remainingAuthor) == "firstname")	// -- Check for which name format
				{		
					$oneName = "";
					
					// Grab first name and remove from remainingAuthor
					$firstNamePattern = '/^([^,]*[,])/';  						// Same as $firstcomma pattern in checkNameFormat() function
					if(preg_match($firstNamePattern, $remainingAuthor, $matchFirstName) == 1)
					{
						$oneName = $matchFirstName[1];
						$remainingAuthor = preg_replace($firstNamePattern, "", $remainingAuthor);
					}
					$savedAuthor = $savedAuthor." ".flipLastAndFirst($oneName).", ";	// Flip name for parseAuthor()
					$found = true;
				}
				else	// Lastname format
				{					
					$oneName = "";
					
					// Grab first two comma and remove from remainingAuthor
					$lastNamePattern = '/^([^,]*[,][^,]*[,])/';
					if(preg_match($lastNamePattern, $remainingAuthor, $matchFirstName) == 1)
					{
						$oneName = $matchFirstName[1];
						$remainingAuthor = preg_replace($lastNamePattern, "", $remainingAuthor);
					}
					$savedAuthor = $savedAuthor." ".$oneName." ";	// Don't have to flip name for parseAuthor()
					$found = true;
				}
			}
			
			// Replace the name found OR simply quit?
			if($found)
			{
				// Do nothing, continue the loop
			}
			else
			{
				break;   // Break out the loop / Quit
			}
			
			$loop_count++;
		}	
	}
	
	return $firstAuthor.", ".$savedAuthor;
}

function checkNameFormat($str)					// Check if name format [firstname lastname] or [lastname, firstname]
{
	$firstcomma = '/^([^,]*[,])/';

	if(preg_match($firstcomma, $str, $match) == 1)
	{
		if(str_word_count($match[1], 0) == 1)	// One lastname  [lastname, firstname]
		{
			return "lastname";
		}
		else return "firstname"; 				// [firstname lastname]
	}
	else return "firstname";
}

function removeStartingAnd($str)
{
	$startsWithAndPattern = '/^[,]*\s*(\band\b|[&])/';
	if(preg_match($startsWithAndPattern, $str, $match) == 1)
	{
		// Remove "and" / "&"
		$str = preg_replace($startsWithAndPattern, "", $str);
		return $str;
	}
}

function startsWithAnd($str, $nameFormat)		// Process one name
{
	$startsWithAndPattern = '/^[,]*\s*(\band\b|[&])/';
	if(preg_match($startsWithAndPattern, $str, $match) == 1)
	{
		return true;
	}
	else return false;	
}

function flipLastAndFirst($str)					//Flip the rest of the name into lastname, firstname format to sent to parseAuthor()		
{	
	$returnStr = "";
	$arr = str_word_count($str, 1);
	$arr_size = sizeof($arr);
	if($arr_size > 1)
	{
		$returnStr = "".$arr[$arr_size - 1].", ";  // Lastname, ...
		for($i = 0; $i < $arr_size - 1; $i++)	   // Firstnames
		{
			$returnStr = $returnStr." ".$arr[$i];
		}
	}

	return $returnStr;
}

?>
