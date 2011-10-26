<?php
	// Sort the contents of a file alphabetically.
	function fileSort($filename)
	{
	  	if (!is_readable($filename)) {
			echo "Error: File ($filename) is not readable";
			exit;
		}
		
		$lines = file($filename);  // Read a file by line and load it into an array	  	
	  	natsort($lines);           // Natural Order Sort
		
		$file = fopen($filename, 'w') or exit("Unable to open file!"); // Check if file exist and open it
		
		foreach($lines as $value){ // Write array into a file		
			fwrite($file, $value); 	
		}
		
		fclose($file);		
	}
?>