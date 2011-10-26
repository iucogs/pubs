<?php 

	// Analyze possible author
	function parseAuthor($str)
	{
//		echo "Entry Str: $str<br />";
		$matches;                					// Split matches array
		$authorsName = array();  					// Verified author's name array
		$loop_control = 0;							// Counter to prevent infinite loop
		$old_str_size = 0;							// Keep track of previous $str size
		
		// Prepare text string for processing			
		$str = preg_replace('/\s{2}/',' ', $str); 						   	        // Remove multiple spaces
 		$str = preg_replace('/\s*(\beds\b\.|\bet al\b\.|ed\.)\s*/iu', '', $str);     // Remove Edition: eds. | et al. | ed.
		$str = preg_replace('/[,][ ]and[ ]|[,][ ]&[ ]/iu', ', ', $str);			    // Remove ,& / ,and
		$str = preg_replace('/([ ]and[ ]|[ ]&[ ])/iu', ', ', $str);					// Remove & / and
		$str = preg_replace('@[(]|[)]|[/]|[:]|[\[]|[\]]|[<]|[>]@u',' ', $str); 		// Remove unusual characters 
		
//		$genericPattern[0] = '/^[A-Z][a-z]+[^,]*[,][ ]*[A-Z][^,]*[,]/';				// Generic form "Name, X. X.,"
//		$genericPattern[1] = '/^[A-Z][.][^,]*[ ][A-Z][a-z][^,]*[,]/';				// Generic form "X. X. Name,"
//		$genericPattern[2] = '/^[A-Z][a-z][^,]*[,][ ]*[A-Z][^,]*/';			    	// Generic form "Name, X. X."
//		$genericPattern[3] = '/^[A-Z][.][^,]*[ ][A-Z][a-z][^,]*/';			    	// Generic form "X. X. Name"
//		$genericPattern[4] = '/^[A-Z][a-z][^,]*[,][ ]*[A-Z][a-z][^,]*[,]/';			// Generic form "Name, Name,"
//		$genericPattern[5] = '/^[A-Z][a-z][^,]*[,][ ]*[A-Z][a-z]+[.]/';				// Generic form "Name, Name."
		
		$genericPattern[0] = '/^(\p{Lu}\p{M}*)(\p{L}\p{M}*)+[^,]*[,][ ]*(\p{Lu}\p{M}*)[^,]*[,]/u';				// Generic form "Name, X. X.,"
		$genericPattern[1] = '/^(\p{Lu}\p{M}*)[.][^,]*[ ](\p{Lu}\p{M}*)(\p{L}\p{M}*)[^,]*[,]/u';					// Generic form "X. X. Name,"
		$genericPattern[2] = '/^(\p{Lu}\p{M}*)(\p{L}\p{M}*)[^,]*[,][ ]*(\p{Lu}\p{M}*)[^,]*/u';			    	// Generic form "Name, X. X."
		$genericPattern[3] = '/^(\p{Lu}\p{M}*)[.][^,]*[ ](\p{Lu}\p{M}*)(\p{L}\p{M}*)[^,]*/u';			    	// Generic form "X. X. Name"
		$genericPattern[4] = '/^(\p{Lu}\p{M}*)(\p{L}\p{M}*)[^,]*[,][ ]*(\p{Lu}\p{M}*)(\p{L}\p{M}*)[^,]*[,]/u';	// Generic form "Name, Name,"
		$genericPattern[5] = '/^(\p{Lu}\p{M}*)(\p{L}\p{M}*)[^,]*[,][ ]*(\p{Lu}\p{M}*)(\p{L}\p{M}*)+[.]/u';		// Generic form "Name, Name."
	
		//$count = 1; // For Debugging
		
		// loop until list is empty
		while(!empty($str)){   // Loop until $value string is empty
			
			// Loop control to prevent infinite loop
			$old_str_size = sizeof($str);
			if(sizeof($str) >= $old_str_size)
			{
				$loop_control++;
			}
			
			// Remove a charactrer and continue if loop on a character goes more than 50
			if($loop_control > 50) {$str = preg_replace('/^./u','',$str); continue;}
			
			// For Debugging
			/* echo "$count : $str<br />"; 
			$count++;
			if($count == 50)
			{
				$str = preg_replace('/^./','',$str);
			}*/
			
			// Catch normal form "Name, Name,"
			if(preg_match($genericPattern[4], $str, $match) == 1)
			{
				// echo "<b>Match Generic 4</b><br />";
				$authorsName[] = $match[0];
				$str = preg_replace($genericPattern[4], '', $str); 				// Remove pattern from list
				$str = preg_replace('/[.][,]$/u', ',', $str);					// Remove [.] at the end of first name
			}

			// Catch normal form "Name, X. X.,"
			else if (preg_match($genericPattern[0], $str, $match) == 1) {
				// echo "<b>Match Generic 0</b>"; print_r($match); echo "<br />";
				$value = $match[0];
//				$lnamePattern[0] = '/^[A-Z][a-z]+[,]/';							// Regular last name "Name,"
//				$lnamePattern[1] = '/^[A-Z][a-z]+[-][A-Z][a-z]+[,]/';			// Compound last name "Name-Name,"
//				$lnamePattern[2] = '/^[A-Z][a-z]+[ ][A-Z][a-z]+[,]/';			// Split name "Name Name,"
//				$lnamePattern[3] = '/^[A-Z][a-z]+[A-Z][a-z]+[,]/';				// Mc's &  Mac's
//				$lnamePattern[4] = '/^[A-Z][a-z]+[ ]([A-Z][.]?[ ]?)+[,]/';		// Missing comma after last name

				$lnamePattern[0] = '/^(\p{Lu}\p{M}*)(\p{L}\p{M}*)+[,]/';		// Regular last name "Name,"
				$lnamePattern[1] = '/^(\p{Lu}\p{M}*)(\p{L}\p{M}*)+[-](\p{Lu}\p{M}*)(\p{L}\p{M}*)+[,]/u';		// Compound last name "Name-Name,"
				$lnamePattern[2] = '/^(\p{Lu}\p{M}*)(\p{L}\p{M}*)+[ ](\p{Lu}\p{M}*)(\p{L}\p{M}*)+[,]/u';		// Split name "Name Name,"
				$lnamePattern[3] = '/^(\p{Lu}\p{M}*)(\p{L}\p{M}*)+(\p{Lu}\p{M}*)(\p{L}\p{M}*)+[,]/u';		// Mc's &  Mac's
				$lnamePattern[4] = '/^(\p{Lu}\p{M}*)(\p{L}\p{M}*)+[ ]((\p{Lu}\p{M}*)[.]?[ ]?)+[,]/u';		// Missing comma after last name

				// Catch regular last name "Name,"
				if (preg_match($lnamePattern[0], $value, $match) == 1) { 
					$lastname = $match[0];
					$value = preg_replace($lnamePattern[0], '', $value);
					$initials = fixInitials(trim($value));
					$name = $lastname.' '.$initials;
					$authorsName[] = $name;
				}
		
				// Catch compound last name "Name-Name"
				else if (preg_match($lnamePattern[1], $value, $match) == 1) {

                                        $lastname = $match[0];
                                        $value = preg_replace($lnamePattern[1], '', $value);
                                        $initials = fixInitials(trim($value));
                                        $name = $lastname.' '.$initials;
                                        $authorsName[] = $name;
				
                }
				
				// Catch split last name "Name Name"
				else if (preg_match($lnamePattern[2], $value, $match) == 1) {

                                        $lastname = $match[0];
                                        $value = preg_replace($lnamePattern[2], '', $value);
                                        $initials = fixInitials(trim($value));
                                        $name = $lastname.' '.$initials;
                                        $authorsName[] = $name;
				
                }

				// Catch Mc's, Mac's and other names with two capital letters
				else if (preg_match($lnamePattern[3], $value, $match) == 1) {

                                        $lastname = $match[0];
                                        $value = preg_replace($lnamePattern[3], '', $value);
                                        $initials = fixInitials(trim($value));
                                        $name = $lastname.' '.$initials;
                                        $authorsName[] = $name;
				
                }

				// Throw the name away
				else {
					
			
				}
				
				// remove pattern from list
				$str = preg_replace($genericPattern[0], '', $str);
			}
			
			// Catch "X. X. Name," and convert to "Name, X. X.,"
			else if (preg_match($genericPattern[1], $str, $match) == 1) {
				// echo "<b>Match Generic 1</b><br />";
				$value = $match[0];
				$name = flipName($value);				
                $str = preg_replace($genericPattern[1], $name, $str);
		
			}
			
			// Catch end of list "Name, Name. and remove punctuation at the end
			else if(preg_match($genericPattern[5], $str, $match) == 1) {
				// echo "<b>Match Generic 5</b><br />";
				$value = $match[0];
				$value = preg_replace('/[.]$/', ',', $value);   		// Replace [.] at the end with [,]
				$str = preg_replace($genericPattern[5], $value, $str);  // Reinsert in $str for reprocessing
			}
			
			// Catch end of list "Name, X. X." and add comma to end
			else if (preg_match($genericPattern[2], $str, $match) == 1) {
				// echo "<b>Match Generic 2</b><br />";
				$value = $match[0];
				$value = trim($value).',';
				$str = preg_replace($genericPattern[2], $value, $str);
			}

		
			// Catch end of list "X. X. Name" and add comma to end
			else if (preg_match($genericPattern[3], $str, $match) == 1) {
				// echo "<b>Match Generic 3</b><br />";
				$value = $match[0];
				$value = trim($value).',';
				$str = preg_replace($genericPattern[3], $value, $str);
			}

			// No regular patterns are found - look for unusual patterns or remove first character and loop
			else {
				// echo "<b>Do Not Match Any Generic</b><br />";
				$altPattern = '/^(Jr[.]*|Sr[.]*|III[.]*)/u';						// Pattern for Jr, Sr, III
				$noCapsPattern = '/^(((\p{Lu}\p{M}*)[.][ ]*)+)((\p{L}\p{M}*)+)/u'; // Pattern for no caps "X. X. name"
				$lastPattern = '/(\p{L}\p{M}*)+/';								// Pattern for no caps last name "name"		
	
				// check for leftover Jr, Sr, or III and add it to previous name
				if (preg_match($altPattern, $str, $matches) == 1) {	echo "Jr, Sr or III<br />";
					$alt = $matches[0];
					
					if(($loc = count($authorsName) - 1) >= 0)   // Check for negative value
					{		
						$authorsName[$loc] = $authorsName[$loc].$alt;
					}
					$str = trim(preg_replace($altPattern, '', $str));
				}

				// NOTE: Caused too many errors. Taking this out will only ignore names with mistakes. 
//				// Check for no caps last name
//				else if (preg_match($noCapsPattern, $str, $matches) == 1) { echo "No caps lastname<br />";
//			
//					$value = $matches[0];
//					
//					// Find last name and capitalize first letter
//					if (preg_match($lastPattern, $value, $lname) == 1) {
//						$lastname = $lname[0];
//						$lastname = strtoupper($lastname{0}).substr($lastname, 1);
//						$value = preg_replace($lastPattern, $lastname, $value);	
//					}
//			
//					// This should never happen
//					else {
//						$value = '';
//					}
//
//					$str = preg_replace($noCapsPattern, $tmp, $str);
//				}					

				// remove first character and loop
				else {	
					$str = preg_replace('/^./u','',$str);	
				}
			
			}
		
		// remove any extra commas, periods, and spaces before looping back
		$str = trim(preg_replace('/^[,]*[.]*/u', '', $str));
		
		}
 		
		// Initialize empty author array as unknown
		if(empty($authorsName))
		{
			$authorsName[] = "unknown";
		}				
		
		return $authorsName;
	}


	// formats initials with correct periods and spacing
	function fixInitials($inits) {
		
		$initPattern = '/^(\p{Lu}\p{M}*)[^(\p{L}\p{M}*)]/u';					// Any leading capital letter
		$removePattern = '/^(\p{Lu}\p{M}*)[.]*[ ]*[,]*/u';			// Full initial with period, spaces, and commas
		$altPattern = '/(Jr[.]*|Sr[.]*|III[.]*)/u';			// Check for Jr, Sr, and III

		$initials = '';										// a place to store initials
		$alt = '';											// a place to store Jr, Sr, III

		// remove Jr, Sr, and III to place at end of string
		if (preg_match($altPattern, $inits, $matches) == 1) {
			$alt = $matches[0];
			$inits = trim(preg_replace($altPattern, '', $inits));
		}

		// build string of initials
		while(preg_match($initPattern, $inits, $match) == 1) {
			$match[0] = preg_replace('/[.]|[,]/u', '', $match[0]);	// Remove periods and commas from initial
			$initials = $initials.$match[0].'. ';					// Add correct format to initial
			$inits = preg_replace($removePattern, '', $inits);		// Remove pattern from string

		}

		return $initials.$alt;
	}


	// Move Last name to front of intitials
	function flipName($name){

        $initPattern = '/^(\p{Lu}\p{M}*)[.]/u';								// Pattern for initial
		$altPattern = '/(Jr[.]*|Sr[.]*|III[.]*)/u';					// Pattern for Jr, Sr, III
			
		$initials = '';
		$lastname = '';
		$alt = '';

		// remove Jr, Sr, and III to place at end of name
		if (preg_match($altPattern, $name, $matches) == 1) {
			$alt = $matches[0];
			$name = trim(preg_replace($altPattern, '', $name));
		}
		
		// build string of initials
		while (preg_match($initPattern, $name, $matches) == 1) {
			$initials = $initials.$matches[0].' ';
			$name = trim(preg_replace($initPattern, '', $name));
		}

		$lastname = trim(preg_replace('/[,]|[.]/u', '', $name));  	// remove comma, period, and space from last name

		return $lastname.', '.$initials.$alt.', ';
	}
?>
