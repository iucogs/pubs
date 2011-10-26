<?php

	// parseJournal
	function parseJournalMLA($str)
	{	
		$journalTitle = "unknown";
		$journalName = "unknown";
	
		// MLA Journal Article
		//$patterns[0] = '{(\d+)[/](\d+)\s*(:|,)\s*(\d+)\s*(-|n)\s*(\d+)}';			//$replace[0] = 'VOLUME/NUMBER:PAGE(-|n)PAGE';
		//$patterns[1] = '/(\d+)\s*\(\s*(\d+)\s*\)\s*(:|,)\s*(\d+)\s*(-|n)\s*(\d+)/';	//$replace[1] = 'VOLUME(NUMBER):PAGE(-|n)PAGE';
		//$patterns[2] = '/(\d+)\s*(:|,)\s*(\d+)\s*(-|n)\s*(\d+)/';      				//$replace[2] = 'VOLUME:PAGE(-|n)PAGE';
		//$patterns[3] = '/(\d+)\s*:/';                           	   				//$replace[5] = 'VOLUME:';
		//$patterns[4] = '/(\d+)\s*\(\s*(\d+)\s*\)\s*:/';           					//$replace[6] = 'VOLUME(number):';
		//$patterns[5] = '/vol\.\s*(\d+)/i';                   		   				//$replace[8] = 'VOLUME';
		//$patterns[6] = '/(\d+)\s*(:|,)\s*(\d+)\s*/';   			    				//$replace[9] = 'VOLUME:PAGE';
		//$patterns[7] = '/vol\.\s*([A-Z]+)/i';										//$replace[11] = 'VOLUME. ROMAN';
		//$patterns[8] = '/no\.\s*(\d+)/i';											//$replace[10] = 'NUMBER';	
		
		// MLA Journal Article - UTF8
		$patterns[0] = '{(\p{Nd}+)[/](\p{Nd}+)\p{Zs}*(:|,)\p{Zs}*(\p{Nd}+)\p{Zs}*(\p{Pd}|n)\p{Zs}*(\p{Nd}+)}';			//$replace[0] = 'VOLUME/NUMBER:PAGE(-|n)PAGE';
		$patterns[1] = '/(\p{Nd}+)\p{Zs}*\(\p{Zs}*(\p{Nd}+)\p{Zs}*\)\p{Zs}*(:|,)\p{Zs}*(\p{Nd}+)\p{Zs}*(\p{Pd}|n)\p{Zs}*(\p{Nd}+)/';	//$replace[1] = 'VOLUME(NUMBER):PAGE(-|n)PAGE';
		$patterns[2] = '/(\p{Nd}+)\p{Zs}*(:|,)\p{Zs}*(\p{Nd}+)\p{Zs}*(\p{Pd}|n)\p{Zs}*(\p{Nd}+)/';      				//$replace[2] = 'VOLUME:PAGE(-|n)PAGE';
		$patterns[3] = '/(\p{Nd}+)\p{Zs}*:/';                           	   				//$replace[5] = 'VOLUME:';
		$patterns[4] = '/(\p{Nd}+)\p{Zs}*\(\p{Zs}*(\p{Nd}+)\p{Zs}*\)\p{Zs}*:/';           					//$replace[6] = 'VOLUME(number):';
		$patterns[5] = '/vol\.\p{Zs}*(\p{Nd}+)/i';                   		   				//$replace[8] = 'VOLUME';
		$patterns[6] = '/(\p{Nd}+)\p{Zs}*(:|,)\p{Zs}*(\p{Nd}+)\p{Zs}*/';   			    				//$replace[9] = 'VOLUME:PAGE';
		$patterns[7] = '/vol\.\p{Zs}*([A-Z]+)/i';										//$replace[11] = 'VOLUME. ROMAN';
		$patterns[8] = '/no\.\p{Zs}*(\p{Nd}+)/i';											//$replace[10] = 'NUMBER';
				
		// Look for article only
		$found = 0;
		for($i = 0; $i < sizeof($patterns); $i++)
		{
			if(preg_match($patterns[$i], $str, $match) == 1)
			{					
				$tmp = preg_replace($patterns[$i], 'PATTERNMARKER', $str);
				$tmp = preg_replace('/PATTERNMARKER.*/', '', $tmp);        				   			// Remove everything after pattern
				//$tmp = preg_replace('/^([a-z]?[)]?\.*\s*|\s+|[a-z]\.\s*|[a-z]\s*)/', '', $tmp);	// Remove junk at the begining of sentences
				$tmp = preg_replace('/^((\p{L})?[)]?\.*\p{Zs}*|\p{Zs}+|(\p{L})\.\p{Zs}*|(\p{L})\p{Zs}*)/u', '', $tmp);		// Remove junk at the begining of sentences
				$tmp = preg_replace('/(\.|\.\p{Zs}*)$/u', '', $tmp);	   						   			// Remove junk at the end of sentences
						
				// DONE : Find the obvious ones first
				//        1. Check for obvious (?) Don't matter how many (.) there are.
				//        2. Check if string has more than one (.)
				//        3. Then, check for (.)
				//        4. Else, later. (undecided)
				
				// Find journal title and name.
				$tmpExplode = explode(".", $tmp);
				$explodeArray = explode("?",$tmp);
				if(sizeof($explodeArray) == 2 && sizeof($tmpExplode) == 1){  // There's one ? in the string and no . in the string
					$journalTitle = $explodeArray[0]."?";       // Add a question mark since explode took it off.
					$journalName = trim($explodeArray[1]);      // Journal name part
				}
				else if(sizeof($explodeArray) > 2){				// There's more than one ? in the string.
					// SUGGESTION: Search DB?
					$journalTitle = "undecided";
					$journalName = "undecided"; 
				}
				else{											// There's no (?) in the string.
					$explodeArray = $tmpExplode; 						
					if(sizeof($explodeArray) == 2){				// There's one (.) in the string.
						$journalTitle = $explodeArray[0]."."; 	// Add a question mark since explode took it off.
						$journalName = trim($explodeArray[1]);  // Journal name part
					}
					else if(sizeof($explodeArray) > 2){			// There's more than one (.) in the string.
						// SUGGESTION: Search DB?
						$journalTitle = "undecided";
						$journalName = "undecided";				
					}
					else{										// There's no (?) or (.) in the string.
						// Check for title in quotation marks
						if(preg_match('/\"(.*)\"(.*)/', $tmp, $quote_match)){   // Check for 2 double quotation marks
							$journalTitle = $quote_match[1];	// Inside of double quotations.
							$journalName = $quote_match[2];		// After of last quotation mark.				
						}
						else if(preg_match('/\'(.*)\'(.*)/', $tmp, $quote_match)){  // Check for 2 single quotation marks
							$journalTitle = $quote_match[1];	// Inside of single quotations.
							$journalName = $quote_match[2];		// After of last quotation mark.
						}
						else{
							// SUGGESTION: Search DB?
							$journalTitle = "undecided";
							$journalName = "undecided";
						}
					}				
				}		
				
				// CLEAN-UP:
				// - Remove comma and/or spaces at the start of journal name
				$journalName = preg_replace('/^\p{Zs}*[,]*/u', "", $journalName);
				
				// - Remove comma and/or spaces at the end of journal name
				$journalName = preg_replace('/\p{Zs}*[,]*\p{Zs}*$/u', "", $journalName);
				
				// -  Remowe double quotation mark at the beginning
				$journalName = preg_replace('/^\p{Zs}*(\p{Pi}|\"|\')*\p{Zs}*/u', "", $journalName);
				$journalTitle = preg_replace('/^\p{Zs}*(\p{Pi}|\"|\')*\p{Zs}*/u', "", $journalTitle);
				
				
				// Check for in-Journal
				if(preg_match('/^\p{Zs}*in\p{Zs}+/iu',$journalName, $match)){   				  // JournalName starts with 'in'
					$journalName = preg_replace('/^\p{Zs}*in\p{Zs}+/iu', '',$journalName);	  // Trim 'in'
					$found = 1; 									  				  // in-book
				}
				else{
					$found = 1;
				}
				
				break;
			}
		}		
		
		$returnArray = array('found' => $found, 'Jtitle' => $journalTitle, 'Jname' => $journalName);
		
		return $returnArray;
	}
?>