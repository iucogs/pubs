<?php
	// Accept an array and return a string in bibtex style.
	function printEntry($entry, $entrySplit)
	{
		$str = "";
		
		if(!empty($entry)){         // Check for empty entry array.
			$str = $str.$entry[0];  // First entry.
		}
		
		// Second entry to N.
		for($i = 1; $i < sizeof($entry); $i++){
			$str = $str." ".$entrySplit." ".$entry[$i];
		}
		
		return $str;
	} 
	
	// Write output file in bibtex.
	function bibtexWriter($data)
	{
		$filename = "output.txt"; // Filename or path. Change to output.bib later
		$file = fopen($filename, 'w') or exit("Unable to open file!"); // Check if file exist and open it
		
		$id = 1;
		foreach($data as $entry)	
		{		
			fwrite($file, "@".$entry['type']."{ $id,\n"); 	// Start an entry with its type.
			fwrite($file, "\tauthor\t= \"".printEntry($entry['author'],"and")."\"\n");
			fwrite($file, "\ttitle\t= \"".$entry['title']."\"\n");
			fwrite($file, "\tyear\t= \"".printEntry($entry['year'],",")."\"\n");
			fwrite($file, "\tjournal\t= \"".$entry['name']."\"\n");
			fwrite($file, "\traw\t= \"".$entry['raw']."\"\n");
			fwrite($file, "}\n\n"); 					   // Close an entry.
			$id++;
		}
		
		fclose($file);
	}	
?>