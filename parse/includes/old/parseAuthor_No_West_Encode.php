<?php 

	// Analyze possible author
	function parseAuthor($str)
	{
		$matches;                					// Split matches array
		$authorsName = array();  					// Verified author's name array


		// Prepare text string for processing			
		$str = preg_replace('/\s{2}/',' ', $str); 						   	        // Remove multiple spaces
 		$str = preg_replace('/\s*(\beds\b\.|\bet al\b\.|ed\.)\s*/i', '', $str);     // Remove Edition: eds. | et al. | ed.
		$str = preg_replace('/[,][ ]and[ ]|[,][ ]&[ ]/i', ', ', $str);			    // Remove ,& / ,and
		$str = preg_replace('/([ ]and[ ]|[ ]&[ ])/i', ', ', $str);					// Remove & / and
		$str = preg_replace('@[(]|[)]|[/]|[:]|[\[]|[\]]|[<]|[>]@',' ', $str); 		// Remove unusual characters 
		
		$genericPattern[0] = '/^[A-Z][a-z][^,]*[,][ ]*[A-Z][^,]*[,]/';				// Generic form "Name, X. X.,"
		$genericPattern[1] = '/^[A-Z][.][^,]*[ ][A-Z][a-z][^,]*[,]/';				// Generic form "X. X. Name,"
		$genericPattern[2] = '/^[A-Z][a-z][^,]*[,][ ]*[A-Z][^,]*/';			    	// Generic form "Name, X. X."
		$genericPattern[3] = '/^[A-Z][.][^,]*[ ][A-Z][a-z][^,]*/';			    	// Generic form "X. X. Name"
		$genericPattern[4] = '/^[A-Z][a-z][^,]*[,][ ]*[A-Z][a-z][^,]*[,]/';			// Generic form "Name, Name,"
		$genericPattern[5] = '/^[A-Z][a-z][^,]*[,][ ]*[A-Z][a-z]+[.]/';				// Generic form "Name, Name."
	
		// loop until list is empty
		while(!empty($str)){   // Loop until $value string is empty

			// Catch normal form "Name, Name,"
			if(preg_match($genericPattern[4], $str, $match) == 1)
			{
				$authorsName[] = $match[0];
				$str = preg_replace($genericPattern[4], '', $str); 				// Remove pattern from list
				$str = preg_replace('/[.][,]$/', ',', $str);					// Remove [.] at the end of first name
			}

			// Catch normal form "Name, X. X.,"
			else if (preg_match($genericPattern[0], $str, $match) == 1) {

				$value = $match[0];
				
				$lnamePattern[0] = '/^[A-Z][a-z]+[,]/';							// Regular last name "Name,"
				$lnamePattern[1] = '/^[A-Z][a-z]+[-][A-Z][a-z]+[,]/';			// Compound last name "Name-Name,"
				$lnamePattern[2] = '/^[A-Z][a-z]+[ ][A-Z][a-z]+[,]/';			// Split name "Name Name,"
				$lnamePattern[3] = '/^[A-Z][a-z]+[A-Z][a-z]+[,]/';				// Mc's &  Mac's
				$lnamePattern[4] = '/^[A-Z][a-z]+[ ]([A-Z][.]?[ ]?)+[,]/';		// Missing comma after last name

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

				$value = $match[0];
				$name = flipName($value);				
                $str = preg_replace($genericPattern[1], $name, $str);
		
			}
			
			// Catch end of list "Name, Name. and remove punctuation at the end
			else if(preg_match($genericPattern[5], $str, $match) == 1) {
				$value = $match[0];
				$value = preg_replace('/[.]$/', ',', $value);   		// Replace [.] at the end with [,]
				$str = preg_replace($genericPattern[5], $value, $str);  // Reinsert in $str for reprocessing
			}
			
			// Catch end of list "Name, X. X." and add comma to end
			else if (preg_match($genericPattern[2], $str, $match) == 1) {
				$value = $match[0];
				$value = trim($value).',';
				$str = preg_replace($genericPattern[2], $value, $str);
			}

		
			// Catch end of list "X. X. Name" and add comma to end
			else if (preg_match($genericPattern[3], $str, $match) == 1) {
				$value = $match[0];
				$value = trim($value).',';
				$str = preg_replace($genericPattern[3], $value, $str);
			}

			// No regular patterns are found - look for unusual patterns or remove first character and loop
			else {

				$altPattern = '/^(Jr[.]*|Sr[.]*|III[.]*)/';		// Pattern for Jr, Sr, III
				$noCapsPattern = '/^([A-Z][.][ ]*)+[a-z]+/';    // Pattern for no caps "X. X. name"
				$lastPattern = '/[a-z]+/';						// Pattern for no caps last name "name"		
	
				// check for leftover Jr, Sr, or III and add it to previous name
				if (preg_match($altPattern, $str, $matches) == 1) {
					$alt = $matches[0];
					
					if(($loc = count($authorsName) - 1) >= 0)   // Check for negative value
					{		
						$authorsName[$loc] = $authorsName[$loc].$alt;
					}
					$str = trim(preg_replace($altPattern, '', $str));
				}

				// Check for no caps last name
				else if (preg_match($noCapsPattern, $str, $matches) == 1) {
			
					$value = $matches[0];

					// Find last name and capitalize first letter
					if (preg_match($lastPattern, $value, $lname) == 1) {
						$lastname = $lname[0];
						$lastname = strtoupper($lastname{0}).substr($lastname, 1);
						$value = preg_replace($lastPattern, $lastname, $value);	
					}
			
					// This should never happen
					else {
						$value = '';
					}

					$str = preg_replace($noCapsPattern, $value, $str);
				}					

				// remove first character and loop
				else {
					$str = preg_replace('/^./','',$str);	
				}
			
			}
		
		// remove any extra commas, periods, and spaces before looping back
		$str = trim(preg_replace('/^[,]*[.]*/', '', $str));
		
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
		
		$initPattern = '/^[A-Z][^a-z]/';					// Any leading capital letter
		$removePattern = '/^[A-Z][.]*[ ]*[,]*/';			// Full initial with period, spaces, and commas
		$altPattern = '/(Jr[.]*|Sr[.]*|III[.]*)/';			// Check for Jr, Sr, and III

		$initials = '';										// a place to store initials
		$alt = '';											// a place to store Jr, Sr, III

		// remove Jr, Sr, and III to place at end of string
		if (preg_match($altPattern, $inits, $matches) == 1) {
			$alt = $matches[0];
			$inits = trim(preg_replace($altPattern, '', $inits));
		}

		// build string of initials
		while(preg_match($initPattern, $inits, $match) == 1) {
			$match[0] = preg_replace('/[.]|[,]/', '', $match[0]);	// Remove periods and commas from initial
			$initials = $initials.$match[0].'. ';					// Add correct format to initial
			$inits = preg_replace($removePattern, '', $inits);		// Remove pattern from string

		}

		return $initials.$alt;
	}


	// Move Last name to front of intitials
	function flipName($name){

        $initPattern = '/^[A-Z][.]/';								// Pattern for initial
		$altPattern = '/(Jr[.]*|Sr[.]*|III[.]*)/';					// Pattern for Jr, Sr, III
			
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

		$lastname = trim(preg_replace('/[,]|[.]/', '', $name));  	// remove comma, period, and space from last name

		return $lastname.', '.$initials.$alt.', ';
	}
?>
