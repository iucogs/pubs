<?php
	function parseVolume($str)
	{
		$volume = ""; 
		$chapter = ""; 
		$pages = ""; 
		$number = "";
		
		$roman_pattern = '/^M{0,4}(CM|CD|D?C{0,3})(XC|XL|L?X{0,3})(IX|IV|V?I{0,3})$/';
		//		^                   # beginning of string
		//		M{0,4}              # thousands - 0 to 4 M's
		//		(CM|CD|D?C{0,3})    # hundreds - 900 (CM), 400 (CD), 0-300 (0 to 3 C's),
		//							#            or 500-800 (D, followed by 0 to 3 C's)
		//		(XC|XL|L?X{0,3})    # tens - 90 (XC), 40 (XL), 0-30 (0 to 3 X's),
		//							#        or 50-80 (L, followed by 0 to 3 X's)
		//		(IX|IV|V?I{0,3})    # ones - 9 (IX), 4 (IV), 0-3 (0 to 3 I's),
		//							#        or 5-8 (V, followed by 0 to 3 I's)
		//		$            		# end of string

	
		// Volume and page patterns
		//$patterns[0] = '{(\d+)[/](\d+)\s*(:|,)\s*(\d+)\s*(-|n)\s*(\d+)}';			//$replace[0] = 'VOLUME/NUMBER:PAGE(-|n)PAGE';
		//$patterns[1] = '/(\d+)\s*\(\s*(\d+)\s*\)\s*(:|,)\s*(\d+)\s*(-|n)\s*(\d+)/';	//$replace[1] = 'VOLUME(NUMBER):PAGE(-|n)PAGE';
		//$patterns[2] = '/(\d+)\s*\(*\s*'.
		//				'(January|February|March|April|May|June|July|April|September|October|November|December)'.
		//				'\s*\)*\s*(:|,)\s*(\d+)\s*(-|n)\s*(\d+)/i';					//$replace[2] = 'VOLUME(MONTH):PAGE(-|n)PAGE';
		//$patterns[3] = '/(\d+)\s*(:|,)\s*(\d+)\s*(-|n)\s*(\d+)/';      				//$replace[3] = 'VOLUME:PAGE(-|n)PAGE';
		//$patterns[4] = '/\(*\s*pp\.\s*(\d+)\s*(-|n)\s*(\d+)\s*\)*/i';				//$replace[4] = 'PP. PAGE(-|n)PAGE';
		//$patterns[5] = '/(\d+)\s*(-|n)\s*(\d+)/';                     				//$replace[5] = 'PAGE(-|n)PAGE';
		//$patterns[6] = '/(\d+)\s*:/';                           	   				//$replace[6] = 'VOLUME:';
		//$patterns[7] = '/(\d+)\s*\(\s*(\d+)\s*\)\s*:/';           					//$replace[7] = 'VOLUME(number):';
		//$patterns[8] = '/ch\.\s*(\d+)/i';                     						//$replace[8] = 'CHAPTER';
		//$patterns[9] = '/vol\.\s*(\d+)/i';                   		   				//$replace[9] = 'VOLUME';
		//$patterns[10] = '/(\d+)\s*(:|,)\s*(\d+)\s*/';   			    			//$replace[10] = 'VOLUME:PAGE';
		//$patterns[11] = '/no\.\s*(\d+)/i';											//$replace[11] = 'NUMBER';
		//$patterns[12] = '/vol\.\s*([A-Z]+)/i';										//$replace[12] = 'VOLUME. ROMAN';
		//$patterns[13] = '/volume\s*(\d+)/i';										//$replace[13] = 'VOLUME (number)';
		
		// Volume and page patterns
		$patterns[0] = '{(\p{Nd}+)[/](\p{Nd}+)\p{Zs}*(:|,)\p{Zs}*(\p{Nd}+)\p{Zs}*(\p{Pd}|n)\p{Zs}*(\p{Nd}+)}u';			//$replace[0] = 'VOLUME/NUMBER:PAGE(-|n)PAGE';
		$patterns[1] = '/(\p{Nd}+)\p{Zs}*\(\p{Zs}*(\p{Nd}+)\p{Zs}*\)\p{Zs}*(:|,)\p{Zs}*(\p{Nd}+)\p{Zs}*(\p{Pd}|n)\p{Zs}*(\p{Nd}+)/u';	//$replace[1] = 'VOLUME(NUMBER):PAGE(-|n)PAGE';
		$patterns[2] = '/(\p{Nd}+)\p{Zs}*\(*\p{Zs}*'.
						'(January|February|March|April|May|June|July|April|September|October|November|December)'.
						'\p{Zs}*\)*\p{Zs}*(:|,)\p{Zs}*(\p{Nd}+)\p{Zs}*(\p{Pd}|n)\p{Zs}*(\p{Nd}+)/iu';					//$replace[2] = 'VOLUME(MONTH):PAGE(-|n)PAGE';
		$patterns[3] = '/(\p{Nd}+)\p{Zs}*(:|,)\p{Zs}*(\p{Nd}+)\p{Zs}*(\p{Pd}|n)\p{Zs}*(\p{Nd}+)/u';      				//$replace[3] = 'VOLUME:PAGE(-|n)PAGE';
		$patterns[4] = '/\(*\p{Zs}*pp\.\p{Zs}*(\p{Nd}+)\p{Zs}*(\p{Pd}|n)\p{Zs}*(\p{Nd}+)\p{Zs}*\)*/iu';				//$replace[4] = 'PP. PAGE(-|n)PAGE';
		$patterns[5] = '/(\p{Nd}+)\p{Zs}*(\p{Pd}|n)\p{Zs}*(\p{Nd}+)/u';                     				//$replace[5] = 'PAGE(-|n)PAGE';
		$patterns[6] = '/(\p{Nd}+)\p{Zs}*:/u';                           	   				//$replace[6] = 'VOLUME:';
		$patterns[7] = '/(\p{Nd}+)\p{Zs}*\(\p{Zs}*(\p{Nd}+)\p{Zs}*\)\p{Zs}*:/u';           					//$replace[7] = 'VOLUME(number):';
		$patterns[8] = '/ch\.\p{Zs}*(\p{Nd}+)/iu';                     						//$replace[8] = 'CHAPTER';
		$patterns[9] = '/vol\.\p{Zs}*(\p{Nd}+)/iu';                   		   				//$replace[9] = 'VOLUME';
		$patterns[10] = '/(\p{Nd}+)\p{Zs}*(:|,)\p{Zs}*(\p{Nd}+)\p{Zs}*/u';   			    			//$replace[10] = 'VOLUME:PAGE';
		$patterns[11] = '/no\.\p{Zs}*(\p{Nd}+)/iu';											//$replace[11] = 'NUMBER';
		$patterns[12] = '/vol\.\p{Zs}*([A-Z]+)/iu';										//$replace[12] = 'VOLUME. ROMAN';
		$patterns[13] = '/volume\p{Zs}*(\p{Nd}+)/iu';										//$replace[13] = 'VOLUME (number)';
		
		for($i = 0; $i < sizeof($patterns); $i++){
				
			if(preg_match($patterns[$i], $str, $match))	 // Lazy match - use first same pattern
			{
				// Save match info
				switch($i)
				{
					case 0:                                  // VOLUME/NUMBER:PAGE(-|n)PAGE
						$volume = $match[1];
						$number = $match[2];
						$pages = $match[4]." - ".$match[6];
						break;
					case 1:                                  // VOLUME(NUMBER):PAGE(-|n)PAGE
						$volume = $match[1];
						$number = $match[2];
						$pages = $match[4]." - ".$match[6];
						break;
					case 2:                                  // VOLUME(MONTH):PAGE(-|n)PAGE
						$volume = $match[1];
						//$number = $match[2];	// Month			 
						$pages = $match[4]." - ".$match[6];
						break;
					case 3:                                  // VOLUME:PAGE(-|n)PAGE
						$volume = $match[1];
						$pages = $match[3]." - ".$match[5];
						break;
					case 4:                                  // PP. PAGE(-|n)PAGE
						$pages = $match[1]." - ".$match[3];
						break;
					case 5:                                  // PAGE(-|n)PAGE
						$pages = $match[1]." - ".$match[3];
						break;
					case 6:	                                 // VOLUME:
						$volume = $match[1];
						break;
					case 7:                                  // VOLUME(NUMBER):
						$volume = $match[1];
						$number = $match[2];
						break;
					case 8:                                  // CHAPTER
						$chapter = $match[1];
						break;
					case 9:                                  // VOLUME
						$volume = $match[1];
						break;
					case 10:                                  // VOLUME:PAGE
						$volume = $match[1];
						$pages = $match[3];
						break;
					case 11:                                  // NUMBER
						$number = $match[1];
						break;
					case 12:                                  // VOLUME. ROMAN
						$roman = $match[1];
						if(preg_match($roman_pattern, $roman))
						{
							$volume = roman2dec($roman);
						}
						break;
					case 13:
						$volume = $match[1];
						break;
					default:
						break;
				}
				
				$str = preg_replace($patterns[$i], '', $str);   // Replace the rest of same pattern.
			}
		}
		
		// Remove whitespace
		$pages = preg_replace('/\p{Zs}*/u', '', $pages);
		
		$info = array('volume' => $volume, 'chapter' => $chapter, 'pages' => $pages, 'number' => $number);
		
		$returnArray = array('str' => $str, 'info' => $info);
		
		return $returnArray;
	}
	
	function roman2dec ($linje) 
	{
		// Fixing variable so it follows my convention
		$linje = strtoupper($linje);
		
		// Removing all non-roman letters
		$linje = preg_replace("[^IVXLCDM]", "", $linje);
	
		//print("$linje    = $linje<br>");
		
		// Defining variables
		$romanLettersToNumbers = array("M" => 1000, "D" => 500, "C" => 100, "L" => 50, "X" => 10, "V" => 5, "I" => 1);
	
		$oldChunk = 1001;
		$count = 0;
	
		// Looping through line
		for($start = 0; $start < strlen($linje); $start++) {
			$chunk = substr($linje, $start, 1);
			
			$chunk = $romanLettersToNumbers[$chunk];
			
			if($chunk <= $oldChunk) {
				$count += $chunk;
				//$calculation .= " + $chunk";
			} else {
				$count += ($chunk - (2 * $oldChunk));
				//$calculation .= " + " . ($chunk - (2 * $oldChunk));
			}
			
		
			$oldChunk = $chunk;
		}

		// Summing it up
		//eval("$calculation = $calculation;");
		return $count;
	}
?>
