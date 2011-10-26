<?php
    // Parse type of entry.
	function parseTypeAPA($str, $afterYearStr, $beforeYearStr)
	{
		$type = "unknown";  					// Entry type.
		$returnArray = array('type' => $type);	// Initialize return array
		
		// Set previous author str for ---,
		global $previous_author_str; global $same_author_str;
		if($same_author_str == false)   // Set previous author only when same_author_str is false OR (---) is not found
		{
			$beforeYearStr = preg_replace('@[(]|[)]|[/]|[:]|[\[]|[\]]|[<]|[>]@',' ', $beforeYearStr); 		// Remove unusual characters
			$previous_author_str = $beforeYearStr;  // Authors string
		}
		
		// Proceeding
		$ProceedingTitle = "unknown";
		$ProceedingName = "unknown";
		$ProceedingPublisher = "unknown";
		$ProceedingPublisherLoc = "unknown";
		 
		// Journal
		$JournalName = "unknown";				// Entry name.
		$JournalTitle = "unknown";				// Entry title.
		
		// Inbook
		$InbookTitle = "unknown";
		$InbookBookTitle = "unknown";
		$InbookEditors = "unknown";
		$InbookPublisher = "unknown";
		$InbookPublisherLoc = "unknown";
		
		// Book
		$BookTitle = "unknown";
		$BookPublisher = "unknown";
		$BookPublisherLoc = "unknown";
		
		// Edited Book (use some of the book variables above)
		$BookEditors = "unknown";
		
		// Check for journal entry.
		// 1. Patterns
		//    - volume:page-page
		//    - volume(number)
		//    - without "eds"
		// 2. Search Journal Name from DB
		
		$editedbook = parseEditedBook($beforeYearStr, $afterYearStr);
		if($editedbook['found'] == 1)  // Pattern matching.
		{
			$type = "edited_book";
			$BookTitle = $editedbook['BookTitle'];
			$BookPublisher = $editedbook['Publisher'];
			$BookPublisherLoc = $editedbook['PublisherLoc'];
			$returnArray = array('type' => $type, 'title' => $BookTitle, 'publisher' => $BookPublisher, 'location' => $BookPublisherLoc);
			return $returnArray;
		}
		
		$proceeding = parseProceedingAPA($afterYearStr);
		if($proceeding['found'])
		{
			$type = "proceedings";
			$ProceedingName = $proceeding['ProceedingName'];
			$ProceedingTitle = $proceeding['ProceedingTitle'];
			$ProceedingPublisher = $proceeding['Publisher'];
			$ProceedingPublisherLoc = $proceeding['PublisherLoc'];
			$returnArray = array('type' => $type, 'title' => $ProceedingTitle, 'name' => $ProceedingName, 
				'publisher' => $ProceedingPublisher, 'location' => $ProceedingPublisherLoc);
			return $returnArray;
		}

		$journal = parseJournal($afterYearStr);
		if($journal['found'] == 1){  // Pattern matching.
			$type = "article";
			$JournalName = $journal['Jname']; 		
			$JournalTitle = $journal['Jtitle'];
			$returnArray = array('type' => $type, 'title' => $JournalTitle, 'name' => $JournalName);
			return $returnArray;
		}
	
		// Check for Inbook / article / chapter
		$inbook = parseInbook($afterYearStr);
		if($inbook['found'] == 1){  // Pattern matching.
			$type = "inbook";
			$InbookTitle = $inbook['InbookTitle'];
			$InbookBookTitle = $inbook['BookTitle'];
			$InbookEditors = $inbook['Editors'];
			$InbookPublisher = $inbook['Publisher'];
			$InbookPublisherLoc = $inbook['PublisherLoc'];
			$returnArray = array('type' => $type, 'title' => $InbookTitle, 'booktitle' => $InbookBookTitle, 
				'editor' => $InbookEditors, 'publisher' => $InbookPublisher, 'location' => $InbookPublisherLoc);
			return $returnArray;
		}
				
		// Check for Book
		$book = parseBook($afterYearStr);
		if($book['found'] == 1){  // Pattern matching.
			$type = "book";
			$BookTitle = $book['BookTitle'];
			$BookPublisher = $book['Publisher'];
			$BookPublisherLoc = $book['PublisherLoc'];
			//$BookEditor = $book['Editor'];
			$returnArray = array('type' => $type, 'title' => $BookTitle, 'publisher' => $BookPublisher, 'location' => $BookPublisherLoc);
			return $returnArray;
		}
		
		// TO-DO: Search for other entry type as well. Currently searching for Journal Name only.
		$dbsearch = dbSearch($afterYearStr);
		if($dbsearch['found'] == 1)
		{
			$returnArray = array('type' => $dbsearch['type'], 'title' => $dbsearch['title'], 'name' => $dbsearch['name']);
			return $returnArray;
		}
		
		return $returnArray;	
	}
	
	function dbSearch($afterYearStr)
	{
		// Look in Journal DB first. Book and Inbook DB does not exist yet.
		$found = 0;
		
		// Characters to include as words
		//$word_chars = 'àáãçéóñéüíÉŽž?[]';
		
		// Journal Name DB
		$column = "name";
		$table = "journals";
		
		$query = "select ".$column.", id from ".$table."";		
		$result = mysql_query($query);
		
		// Max percentage
		$max_percent = 0;
		$best_db_entry = "";
		$best_db_id;
		$best_str_entry = "";
		$best_index;	


		// Do this outside of loop. Just need to do it once.		
		//$str_word_arr = str_word_count($afterYearStr, 1, $word_chars);    // Str array that have been splitted into words.
		$str_word_arr = str_word_count_utf8($afterYearStr, 1);				// Str array that have been splitted into words, UTF8 compatible.
		$str_word_size = sizeof($str_word_arr);
		
			
		// Loop through one DB entry at a time
		while($row = mysql_fetch_array($result))
		{		
			//$entry_word_arr = str_word_count($row['name'], 1, $word_chars);   // DB Entry array that have been splitted into words
			$entry_word_arr = str_word_count_utf8($row['name'], 1);   			// DB Entry array that have been splitted into words
			$entry_word_size = sizeof($entry_word_arr);						  	// Entry word array size
				
			$db_entry = words2string($entry_word_arr, 0, $entry_word_size, ''); // Words put together for comparison.
						
			for($i = 0; $i < sizeof($str_word_arr); $i++)
			{
				$percent = 0;
			
				// Compare the two entries [$db_entry moved outside for efficiency].
				$str_entry = words2string($str_word_arr, $i, $i + $entry_word_size);
				
				similar_text($str_entry, $db_entry, $percent);			// Find similarity in percentage
				if($percent >= 90 && $percent >= $max_percent)
				{
					if($percent == $max_percent)   						// Same percentage. Check for length
					{
						if(strlen($db_entry) > strlen($best_db_entry)) 	// New entry length is longer than best entry length
						{
							$max_percent = $percent;
							$best_db_entry = $db_entry;
							$best_db_id = $row['id'];
							$best_db_name = $row['name'];
							$best_str_entry = $str_entry;
							$best_index = $i;
						}
						else
						{
							// Do nothing. Keep the original best entry
						}
					}
					else
					{
						$max_percent = $percent;
						$best_db_entry = $db_entry;
						$best_db_id = $row['id'];
						$best_db_name = $row['name'];
						$best_str_entry = $str_entry;
						$best_index = $i;
					}
					/*echo "Db entry: ".$db_entry." | ID: ".$best_db_id."<br />";
					echo "Str entry: ".$str_entry."<br />";
					echo "Percent: ".$percent."<br />";
					echo "Best Index: ".$best_index."<br /><br />";*/
				}
			}
			
		}
		
		if($max_percent == 0)
		{
			$returnArray = array('found' => 0);
			return $returnArray;
		}
		else
		{
			$JournalTitle = "";
			if(!is_null($best_index))
			{
				$JournalTitle = words2string($str_word_arr, 0, $best_index, " ");
			}
			else
			{
				$JournalTitle = "unknown";											  // Save journal title.			  
			}
			$JournalName = $best_db_name;   										  // Save journal name.
			$type = "article";
			$returnArray = array('found' => 1, 'type' => $type, 'title' => $JournalTitle, 'name' => $JournalName);
			return $returnArray;
		}		
	
	}
	
	function words2string($arr, $index1, $index2, $padding = '')          // Combine array strings into one string from index1 to index2
	{
		$str = "";
		$upper_limit = 0;
		
		if(sizeof($arr) <= $index2)
		{
			$upper_limit = sizeof($arr);
		}
		else
		{
			$upper_limit = $index2;
		}
		for($i = $index1; $i < $upper_limit; $i++)			// Loop string array
		{
			$str = $str.$padding.$arr[$i];					// Put words together
		}
	
		return $str;
	}
	
?>