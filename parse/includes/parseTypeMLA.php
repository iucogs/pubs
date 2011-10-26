<?php
	
	function parseTypeMLA($str)
	{
		$type = "unknown";  					// Entry type.
		$returnArray = array('type' => $type);	// Initialize return array
		 
		// Journal
		$JournalName = "unknown";				// Entry name.
		$JournalTitle = "unknown";				// Entry title.
		
		// Inbook
		$InbookTitle = "unknown";
		$InbookBookTitle = "unknown";
		$InbookEditors = "unknown";
		$InbookPublisher = "unknown";
		$InbookPublisherLoc = "unknown";
		
		//Book
		$BookTitle = "unknown";
		$BookPublisher = "unknown";
		$BookPublisherLoc = "unknown";
		
		// TO-DO: Remove year at the end.
		
		
		
		// Grab Authors
		$returnArray = parseAuthorMLA($str);
		
		$author = $returnArray['author'];
		$afterAuthStr = $returnArray['afterAuth'];
		
		
		// Set previous author str for ---,
		global $previous_author_str; global $same_author_str;
		if($same_author_str == false)   // Set previous author only when same_author_str is false OR (---) is not found
		{
			$str = str_replace($afterAuthStr,"",$str);
			$previous_author_str = preg_replace('@[(]|[)]|[/]|[:]|[\[]|[\]]|[<]|[>]@',' ', $str); 		// Remove unusual characters
		}
		
		if($author != "unknown")  // Name found
		{
			$editedbook = parseEditedBook("", $afterAuthStr);
			if($editedbook['found'] == 1)  // Pattern matching.
			{
				$type = "edited_book";
				$BookTitle = $editedbook['BookTitle'];
				$BookPublisher = $editedbook['Publisher'];
				$BookPublisherLoc = $editedbook['PublisherLoc'];
				$returnArray = array('type' => $type, 'title' => $BookTitle, 'publisher' => $BookPublisher, 'location' => $BookPublisherLoc);
				return $returnArray;
			}
		
			$journal = parseJournalMLA($afterAuthStr);
			if($journal['found'] == 1){  // Pattern matching.
				$type = "article";
				$JournalName = $journal['Jname']; 		
				$JournalTitle = $journal['Jtitle'];
				$returnArray = array('author' => $author, 'type' => $type, 'title' => $JournalTitle, 'name' => $JournalName);
				return $returnArray;
			}
			else{}
			
			// Check for Inbook / article / chapter
			$inbook = parseInbook($afterAuthStr);
			if($inbook['found'] == 1){  // Pattern matching.
				$type = "inbook";
				$InbookTitle = $inbook['InbookTitle'];
				$InbookBookTitle = $inbook['BookTitle'];
				$InbookEditors = $inbook['Editors'];
				$InbookPublisher = $inbook['Publisher'];
				$InbookPublisherLoc = $inbook['PublisherLoc'];
				$returnArray = array('author' => $author, 'type' => $type, 'title' => $InbookTitle, 'booktitle' => $InbookBookTitle, 
					'editor' => $InbookEditors, 'publisher' => $InbookPublisher, 'location' => $InbookPublisherLoc);
				return $returnArray;
			}
			else{   // DB Searching 
			}
			
			// Check for Book
			$book = parseBook($afterAuthStr);
			if($book['found'] == 1){  // Pattern matching.
				$type = "book";
				$BookTitle = $book['BookTitle'];
				$BookPublisher = $book['Publisher'];
				$BookPublisherLoc = $book['PublisherLoc'];
				$returnArray = array('author' => $author, 'type' => $type, 'title' => $BookTitle, 'publisher' => $BookPublisher, 'location' => $BookPublisherLoc);
				return $returnArray;
			}
			else{   // DB Searching 
			}
		}
		else{} // Couldn't find name
		
		
		$returnArray = array('type' => $type, 'author' => $author);
		
		return $returnArray;
	}
	
?>
