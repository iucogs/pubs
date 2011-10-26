<?php
	// Analyze possible year
	function parseDate($str)
  	{
		$matches;  // Hold all matching expressions
		$validYear = array(); // Hold valid year.
		
//		// Remove patterns with numbers that are not year
//		$patterns[0] = '{\d+[/]\d+\s*(:|,)\s*\d+\s*(-|n)\s*\d+}'; 	$replace[0] = 'VOLUME/NUMBER:PAGE(-|n)PAGE';
//		$patterns[1] = '/\d+\s*(:|,)\s*\d+\s*(-|n)\s*\d+/';      	$replace[1] = 'VOLUME:PAGE(-|n)PAGE';
//		$patterns[2] = '/\(*\s*pp\.\s*\d+\s*(-|n)\s*\d+\s*\)*/i';	$replace[2] = 'PP. PAGE(-|n)PAGE';
//		$patterns[3] = '/\d+\s*(-|n)\s*\d+/';                     	$replace[3] = 'PAGE(-|n)PAGE';
//		$patterns[4] = '/\d+\s*:/';                           	   	$replace[4] = 'VOLUME:';
//		$patterns[5] = '/\d+\s*\(\s*\d+\s*\)\s*:/';           		$replace[5] = 'VOLUME(number):';
//		$patterns[6] = '/ch\.\s*\d+/i';                     		$replace[6] = 'CHAPTER';
//		$patterns[7] = '/vol\.\s*\d+/i';                   		    $replace[7] = 'VOLUME';
//		$patterns[8] = '/\d+\s*(:|,)\s*\d+\s*/';       			    $replace[8] = 'VOLUME:PAGE'; 
//		
//		for($i = 0; $i < sizeof($patterns); $i++){
//			$str = preg_replace($patterns[$i], $replace[$i], $str);
//		}

		$temp = parseVolume($str);
		
		$str = $temp['str'];
		
		preg_match_all('/\p{Nd}{4}|in press|forthcoming|submitted/iu', $str, $matches);  // 4 digits year | in press
		
		// Validate matches
		foreach ($matches as $v1) {
 		   	foreach ($v1 as $v2) {
				if($v2 <= (date('Y') + 1) && $v2 >= 1400){  // Check for year between 1400 (printing press 1440) - current year plus one
        			$validYear[] = $v2;					
				}
				if(strcasecmp($v2, "in press") == 0){  		// Check for "in press"
					$validYear[] = strtoupper($v2);	
				}
				if(strcasecmp($v2, "forthcoming") == 0){  	// Check for "forthcoming"
					$validYear[] = strtoupper($v2);	
				}
				if(strcasecmp($v2, "submitted") == 0){  	// Check for "submitted"
					$validYear[] = strtoupper($v2);	
				}
    		}
		}
		
		// Clean up $entry before saving to $data
		if(empty($validYear)){
			$validYear = array('unknown'); 			// Initialize empty author array as unknown
		}		
		
		$returnArray = array('year' => $validYear, 'info' => $temp['info']);
		
		return $returnArray;
  	}
?>