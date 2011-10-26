<?php

	// parseJournal
	function parseJournal($str)
	{	
		$journalTitle = "unknown";
		$journalName = "unknown";
	
		// APA Journal Article
		//$patterns[0] = '{(\d+)[/](\d+)\s*(:|,)\s*(\d+)\s*(-|n)\s*(\d+)}';			//$replace[0] = 'VOLUME/NUMBER:PAGE(-|n)PAGE';
		//$patterns[1] = '/(\d+)\s*\(\s*(\d+)\s*\)\s*(:|,)\s*(\d+)\s*(-|n)\s*(\d+)/';	//$replace[1] = 'VOLUME(NUMBER):PAGE(-|n)PAGE';
		//$patterns[2] = '/(\d+)\s*\(*\s*'.
		//				'(January|February|March|April|May|June|July|April|September|October|November|December)'.
		//				'\s*\)*\s*(:|,)\s*(\d+)\s*(-|n)\s*(\d+)/i';					//$replace[2] = 'VOLUME(MONTH):PAGE(-|n)PAGE';													
		//$patterns[3] = '/(\d+)\s*(:|,)\s*(\d+)\s*(-|n)\s*(\d+)/';      				//$replace[3] = 'VOLUME:PAGE(-|n)PAGE';
		//$patterns[4] = '/(\d+)\s*:/';                           	   				//$replace[4] = 'VOLUME:';
		//$patterns[5] = '/(\d+)\s*\(\s*(\d+)\s*\)\s*:/';           					//$replace[5] = 'VOLUME(number):';
		//$patterns[6] = '/vol\.\s*(\d+)/i';                   		   				//$replace[6] = 'VOLUME';
		//$patterns[7] = '/(\d+)\s*(:|,)\s*(\d+)\s*/';   			    				//$replace[7] = 'VOLUME:PAGE';
		//$patterns[8] = '/vol\.\s*([A-Z]+)/i';										//$replace[8] = 'VOLUME. ROMAN';
		//$patterns[9] = '/no\.\s*(\d+)/i';											//$replace[9] = 'NUMBER';

		// APA Journal Article - UTF8		
		$patterns[0] = '{(\p{Nd}+)[/](\p{Nd}+)\p{Zs}*(:|,)\p{Zs}*(\p{Nd}+)\p{Zs}*(\p{Pd}|n)\p{Zs}*(\p{Nd}+)}u';			//$replace[0] = 'VOLUME/NUMBER:PAGE(-|n)PAGE';
		$patterns[1] = '/(\p{Nd}+)\p{Zs}*\(\p{Zs}*(\p{Nd}+)\p{Zs}*\)\p{Zs}*(:|,)\p{Zs}*(\p{Nd}+)\p{Zs}*(\p{Pd}|n)\p{Zs}*(\p{Nd}+)/u';	//$replace[1] = 'VOLUME(NUMBER):PAGE(-|n)PAGE';
		$patterns[2] = '/(\p{Nd}+)\p{Zs}*\(*\p{Zs}*'.
						'(January|February|March|April|May|June|July|April|September|October|November|December)'.
						'\p{Zs}*\)*\p{Zs}*(:|,)\p{Zs}*(\p{Nd}+)\p{Zs}*(\p{Pd}|n)\p{Zs}*(\p{Nd}+)/iu';					//$replace[2] = 'VOLUME(MONTH):PAGE(-|n)PAGE';													
		$patterns[3] = '/(\p{Nd}+)\p{Zs}*(:|,)\p{Zs}*(\p{Nd}+)\p{Zs}*(\p{Pd}|n)\p{Zs}*(\p{Nd}+)/u';      				//$replace[3] = 'VOLUME:PAGE(-|n)PAGE';
		$patterns[4] = '/(\p{Nd}+)\p{Zs}*:/u';                           	   				//$replace[4] = 'VOLUME:';
		$patterns[5] = '/(\p{Nd}+)\p{Zs}*\(\p{Zs}*(\p{Nd}+)\p{Zs}*\)\p{Zs}*:/u';           					//$replace[5] = 'VOLUME(number):';
		$patterns[6] = '/vol\.\p{Zs}*(\p{Nd}+)/iu';                   		   				//$replace[6] = 'VOLUME';
		$patterns[7] = '/(\p{Nd}+)\p{Zs}*(:|,)\p{Zs}*(\p{Nd}+)\p{Zs}*/u';   			    				//$replace[7] = 'VOLUME:PAGE';
		$patterns[8] = '/vol\.\p{Zs}*([A-Z]+)/iu';										//$replace[8] = 'VOLUME. ROMAN';
		$patterns[9] = '/no\.\p{Zs}*(\p{Nd}+)/iu';											//$replace[9] = 'NUMBER';	
				
		// Look for article only
		$found = 0;
		for($i = 0; $i < sizeof($patterns); $i++)
		{
			if(preg_match($patterns[$i], $str, $match) == 1)
			{							
				$tmp = preg_replace($patterns[$i], 'PATTERNMARKER', $str);
				$tmp = preg_replace('/PATTERNMARKER.*/', '', $tmp);        				   									// Remove everything after pattern
				//$tmp = preg_replace('/^((\p{L}\p{M}*)?[)]?\.*\s*|\s+|(\p{L}\p{M}*)\.\s*|(\p{L}\p{M}*)\s*)/', '', $tmp);	// Remove junk at the begining of sentences
				//$tmp = preg_replace('/^((\p{L})?[)]?\.*\s*|\s+|(\p{L})\.\s*|(\p{L})\s*)/u', '', $tmp);						// Remove junk at the begining of sentences
				$tmp = preg_replace('/^((\p{L})?[)]?\.*\p{Zs}*|\p{Zs}+|(\p{L})\.\p{Zs}*|(\p{L})\p{Zs}*)/u', '', $tmp);		// Remove junk at the begining of sentences
				$tmp = preg_replace('/(\.|\.\p{Zs}*)$/u', '', $tmp);	   						   									// Remove junk at the end of sentences
						
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
					$jtitle_temp = "";
					for($i = 0; $i < sizeof($explodeArray)-1; $i++)			// Put all together except the last item in array.
					{
						$jtitle_temp .= $explodeArray[$i]."?";	
					}										
					$journalTitle = $jtitle_temp;							// UNDECIDED: Take the last ?
					$journalName = $explodeArray[sizeof($explodeArray)-1]; 	// UNDECIDED: Take the part after last ?
				}
				else{											// There's no (?) in the string.
					$explodeArray = $tmpExplode;
					// Check for title in quotation marks
					if(preg_match('/\p{Pi}(.*)\p{Pf}(.*)/u', $tmp, $quote_match)){   // Check for other type of quotation marks --> (preg_match('/\"(.*)\'(.*)/", $tmp, $quote_match))
						$journalTitle = $quote_match[1];	// Inside of other type quotations.
						$journalName = $quote_match[2];		// After of last quotation mark.
					}
					else if(preg_match('/\"(.*)\"(.*)/u', $tmp, $quote_match)){  // Check for 2 double quotation marks
						$journalTitle = $quote_match[1];	// Inside of double quotations.
						$journalName = $quote_match[2];		// After of last quotation mark.
					}
					else if(preg_match('/\'(.*)\'(.*)/u', $tmp, $quote_match)){  // Check for 2 single quotation marks
						$journalTitle = $quote_match[1];	// Inside of single quotations.
						$journalName = $quote_match[2];		// After of last quotation mark.
					} 						
					else if(sizeof($explodeArray) == 2){		// There's one (.) in the string.
						$journalTitle = $explodeArray[0]."."; 	// Add a punctuation mark since explode took it off.
						$journalName = trim($explodeArray[1]);  // Journal name part
					}
					else if(sizeof($explodeArray) > 2){			// There's more than one (.) in the string.
						// SUGGESTION: Search DB?
						$dbsearch = dbSearch($tmp);
						if($dbsearch['found'] == 1)
						{
							$journalTitle = $dbsearch['title'];
							$journalName = $dbsearch['name'];
						}
						else{
							//$pattern = '/^[^(\p{L&}\p{M}*)]*/i';
							$pattern = '/^[^(\p{L&})]*/iu';
							$tmp = preg_replace($pattern, '', $tmp);
							if(empty($tmp))
							{
								$tmp = "unknown";
							}
							$journalTitle = $tmp;		// UNDECIDED: $tmp
							$journalName = "unknown";	// UNDECIDED: unknown
						}
					}
					else{										// There's no (?) or (.) in the string.
						// SUGGESTION: Search DB?
						$dbsearch = dbSearch($tmp);
						if($dbsearch['found'] == 1)
						{
							$journalTitle = $dbsearch['title'];
							$journalName = $dbsearch['name'];
						}
						else{
							//$pattern = '/^[^(\p{L&}\p{M}*)]*/i';
							$pattern = '/^[^(\p{L&})]*/iu';
							$tmp = preg_replace($pattern, '', $tmp);
							if(empty($tmp))
							{
								$tmp = "unknown";
							}
							$journalTitle = $tmp;		// UNDECIDED: $tmp
							$journalName = "unknown";	// UNDECIDED: unknown
						}
					}				
				}		
				
				// CLEAN-UP:
				// - Remove comma and/or spaces at the start of journal name
				$journalName = clean($journalName);				
				
				// - Remove comma and/or spaces at the end of journal name [clean has already taken care of this]
				//$journalName = preg_replace('/\p{Zs}*[,]*\p{Zs}*$/u', "", $journalName);
				
				// -  Remove double quotation mark at the beginning
				//$journalTitle = preg_replace('/^\s*(\"|\')*\s*/', "", $journalTitle);
				$journalTitle = clean($journalTitle);
				
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