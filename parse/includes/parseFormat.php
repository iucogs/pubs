<?php
	
	function parseFormat($rawStr, $year)
	{
		$format = "APA";  	// Default format, else MLA
		
		if(empty($year)){   // Couldn't find year
					 		// Default format APA
		}
		else if(is_numeric($year[0])){  // Check if first year is a number
			
			$strCount = mb_strlen($rawStr);

			$yearPos = strpos($rawStr,$year[0]);
			
			//echo "Count: ".$strCount." Pos: ".$yearPos." Eq: ".(($strCount - $yearPos) / $strCount)."<br>";
			
			//if(($strCount - $yearPos) / $strCount < 0.70){  // Firsr year position is located after 70% of the whole line
			//	$format = "MLA";
			//}
			if($yearPos / $strCount > 0.70){  // Firsr year position is located after 70% of the whole line
				$format = "MLA";
			}
		}
		else{}
		
		return $format;	
	}
?>