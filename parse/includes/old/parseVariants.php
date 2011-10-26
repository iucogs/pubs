<?php
	function createVariants($firstname, $lastname)
	{
		
		$returnArray = array();
		$returnFirstname = array();
		
		$firstname = " Andrew Delano Mutu";                 // 
		$lastname = "Abbott";
		
		$firstname = trim($firstname);
		$lastname = trim($lastname);
		
		// Count how many first name
		$f_array = explode(' ',$firstname);					// Anderson, Junior, Dunkin, ...
		
		echo "f_array: ";
		print_r($f_array);
		
		$var_f_array = array();								// A, A J, A J D, ...
		$var_f_dot_array = array();							// A., A. J., A. J. D., ...
		$var_f_full_array = array();
		$var_f_second_array = array();
		$var_f_second_dot_array = array();
		
		// Initialize arrays
		for($k = 0; $k < sizeof($f_array); $k++){
			$var_f_array[$k] = "";
			$var_f_dot_array[$k] = "";
			$var_f_full_array[$k] = "";
			$var_f_second_array[$k] = "";
			$var_f_second_dot_array[$k] = "";
		}
	
		// Get firstname initial
		for($i = 0; $i < sizeof($f_array); $i++)
		{
			$f_initial_array[] = $f_array[$i][0]; 			// A, J, D, ...
			$f_initial_dot_array[] = $f_array[$i][0]."."; 	// A., J., D., ...
			
			for($j = 0; $j <= $i; $j++)
			{
				$var_f_array[$i] = $var_f_array[$i]." ".$f_initial_array[$j];
				$var_f_dot_array[$i] = $var_f_dot_array[$i]." ".$f_initial_array[$j].".";
				$var_f_full_array[$i] = $var_f_full_array[$i]." ".$f_array[$j];
			}
		}
		
		for($z = 0; $z < sizeof($f_initial_array); $z++)
		{
			for($x = 1; $x <= $z; $x++)
			{
				$var_f_second_array[$z] = $var_f_second_array[$z]." ".$f_initial_array[$x];
				$var_f_second_dot_array[$z] = $var_f_second_dot_array[$z]." ".$f_initial_dot_array[$x];
			}
		}
		
		/*echo "<br /> f_initial_array: ";
		print_r($f_initial_array);
		
		echo "<br /> var_f_array: ";
		print_r($var_f_array);
		
		echo "<br /> var_f_second_array: ";
		print_r($var_f_second_array);*/
		
		// First Variant: Firstname initials then Lastname.
		foreach($var_f_array as $f_init){
			//$returnArray[] = $f_init." ".$lastname;
			$returnFirstname[] = $f_init;
		}
		
		foreach($var_f_dot_array as $f_init){
			//$returnArray[] = $f_init." ".$lastname;
		}
		
		// Second Variant: Lastname, Firstname initials.
		foreach($var_f_array as $f_init){
			//$returnArray[] = $lastname.", ".$f_init;
		}
		
		foreach($var_f_dot_array as $f_init){
			//$returnArray[] = $lastname.", ".$f_init;
		}
		
		// Third Variant: Lastname, variations of full Firstname.
		foreach($var_f_full_array as $f_init){
			//$returnArray[] = $lastname.", ".$f_init;
		}
		
		// Fourth Variant:
		for($i = 0; $i < sizeof($f_initial_array); $i++){
			//$returnArray[] = $f_array[0]." ".$var_f_second_array[$i]." ".$lastname;
		}
		
		for($i = 0; $i < sizeof($f_initial_array); $i++){
			//$returnArray[] = $f_array[0]." ".$var_f_second_dot_array[$i]." ".$lastname;
		}

		return $returnArray;		
	}
?>
