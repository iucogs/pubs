<?php
	/*	Development Note:
	 *		1. "Immortality and Resurrection: Death in the Western World: Two Conflicting Currents of Thought. New York: Macmillan." has incomprehensible pmatch error
	 *			before clean() -> parsePublisherAndCity. FIXED
	 *		2. parsePublisherAndCity simply return the whole string as Publisher. Nothing is being done yet to separate City and Publisher.
	 *		3. Editors and BookTitle are being written to DB as strings/text and not being point as a separate data entry.
	 */

	function inbook_db_search_first($str)
	{
		$InbookTitle = "unknown";
		$BookTitle = "unknown";
		$Editors = "unknown";
		$Publisher = "unknown";
		$PublisherLoc = "unknown";
		$found = false;
	
		$patterns[0] = '/(.*)\bin\b(?=!.*\bin\b.*)(\beditors\b|\beditor\b|\(eds\.\)|\(eds\)|\(ed\.\)|\(ed\))(.*)/i';  	// 'IN ... EDS [check if statement]
		$patterns[1] = '/(.*)\bin\b(.*)\b(edited\sby|ed\.|eds\.|eds|ed)\b(.*)/i';							            // 'IN ... EDS
	
		$parsePubSearch = parsePublisherSearch($str);
		
		if($parsePubSearch['found'])								// Do DB Search first
		{	
			//$found = true;
			$Publisher = $parsePubSearch['Publisher'];
			$PublisherLoc = $parsePubSearch['PublisherLoc'];
						
			for($i = 0; $i < sizeof($patterns); $i++)
			{
				if(preg_match($patterns[$i], $str, $match) == 1)	// Look for inbook
				{
					// Title
					$InbookTitle = clean($match[1]);				// Contains the title

					// Find Editor
					$tmp_result = parseEditor($match[0]);			// String between "in .. eds"
					if($tmp_result['flag'] != "not inbook")
					{
						$Editors = $tmp_result['editor'];		 			// Editor
						$BookTitle = clean($tmp_result['booktitle']);		// BookTitle
					}
					$found = true;
					break;
				}			
			}
		}
		else 
		{
			//$found = false;
		}
		
		$returnArray = array('found' => $found, 'InbookTitle' => $InbookTitle, 'BookTitle' => $BookTitle, 'Editors' => $Editors, 
				'Publisher' => $Publisher, 'PublisherLoc' => $PublisherLoc);	
		
		return $returnArray;
	}


	function parseInbook($str)
	{
		// Do city and publisher DB search first
		$result = inbook_db_search_first($str);
		if($result['found'])
		{
			return $result;
		}
		
		$InbookTitle = "unknown";
		$BookTitle = "unknown";
		$Editors = "unknown";
		$Publisher = "unknown";
		$PublisherLoc = "unknown";
	
		// APA Inbook Article / Chapter
		//$patterns[0] = '/(.*)\bin\b(?=!.*\bin\b.*)(\beditors\b|\beditor\b|\(eds\.\)|\(eds\)|\(ed\.\)|\(ed\))(.*)/i';  	// 'IN ... EDS [check if statement]
		//$patterns[1] = '/(.*)\bin\b(.*)\b(edited\sby|ed\.|eds\.|eds|ed)\b(.*)/i';							// 'IN ... EDS
		//$patterns[2] = '/\(*\s*pp\.\s*(\d+)\s*(-|n)\s*(\d+)\s*\)*/i';				//$replace[3] = 'PP. PAGE(-|n)PAGE';
		//$patterns[3] = '/(\d+)\s*(-|n)\s*(\d+)/';                     				//$replace[4] = 'PAGE(-|n)PAGE';
		//$patterns[4] = '/ch\.\s*(\d+)/i';                     						//$replace[7] = 'CHAPTER';
		
		// APA Inbook Article / Chapter - UTF8
		$patterns[0] = '/(.*)\bin\b(?=!.*\bin\b.*)(\beditors\b|\beditor\b|\(eds\.\)|\(eds\)|\(ed\.\)|\(ed\))(.*)/i';  	// 'IN ... EDS [check if statement]
		$patterns[1] = '/(.*)\bin\b(.*)\b(edited\sby|ed\.|eds\.|eds|ed)\b(.*)/i';							// 'IN ... EDS
    	$patterns[2] = '/\(*\p{Zs}*pp\.\p{Zs}*(\p{Nd}+)\p{Zs}*(\p{Pd}|n)\p{Zs}*(\p{Nd}+)\p{Zs}*\)*/iu';				//$replace[3] = 'PP. PAGE(-|n)PAGE';
		$patterns[3] = '/(\p{Nd}+)\p{Zs}*(\p{Pd}|n)\p{Zs}*(\p{Nd}+)/u';                     				//$replace[4] = 'PAGE(-|n)PAGE';
		$patterns[4] = '/ch\.\p{Zs}*(\p{Nd}+)/iu';                     						//$replace[7] = 'CHAPTER';

		//$starts_with_in_pattern = '/^\s*\bin\b(.*)/';
		
		$found = 0;
		for($i = 0; $i < sizeof($patterns); $i++)
		{
			if(preg_match($patterns[$i], $str, $match) == 1)	// Look for inbook
			{	
				$tmp; 
				
				if(preg_match('/\p{Pi}(.*)\p{Pf}(.*)/u', $str, $tmp))   	// There are 2 double quotes (UTF8)
				{		
					// Title
					$InbookTitle = $tmp[1];
					
					// Find Editor
					$tmp_result = parseEditor($tmp[2]);			// Pass after title string
					if($tmp_result['flag'] != "not inbook")
					{
						$Editors = $tmp_result['editor']; 			// Editor
						$BookTitle = $tmp_result['booktitle'];		// BookTitle
						$Publisher = $tmp_result['publisher'];		// Publisher
						$PublisherLoc = $tmp_result['publisherLoc'];// PublisherLoc
					}
				}		
				else if(sizeof($tmp = explode('"', $str)) == 3)   	// There are 2 double quotes
				{
					// Title
					$InbookTitle = $tmp[1];
					
					// Find Editor
					$tmp_result = parseEditor($tmp[2]);			// Pass after title string
					if($tmp_result['flag'] != "not inbook")
					{
						$Editors = $tmp_result['editor']; 			// Editor
						$BookTitle = $tmp_result['booktitle'];		// BookTitle
						$Publisher = $tmp_result['publisher'];		// Publisher
						$PublisherLoc = $tmp_result['publisherLoc'];// PublisherLoc
					}
				}
				else if(sizeof($tmp = explode("'", $str)) == 3) // There are 2 single quotes
				{
					// Title 
					$InbookTitle = $tmp[1];
					
					// Find Editor
					$tmp_result = parseEditor($tmp[2]);			// Pass after title string
					if($tmp_result['flag'] != "not inbook")
					{
						$Editors = $tmp_result['editor']; 			// Editor
						$BookTitle = $tmp_result['booktitle'];		// BookTitle
						$Publisher = $tmp_result['publisher'];		// Publisher
						$PublisherLoc = $tmp_result['publisherLoc'];// PublisherLoc
					}
				}
				else if($i == 0 || $i == 1)						// Matches pattern 0 or 1
				{
					// Title
					$InbookTitle = clean($match[1]);						// Contains the title
					
					// Find Editor
					$tmp_result = parseEditor($match[0]);			// String between "in .. eds"
					if($tmp_result['flag'] != "not inbook")
					{
						$Editors = $tmp_result['editor']; 			// Editor
						$BookTitle = $tmp_result['booktitle'];		// BookTitle
						$Publisher = $tmp_result['publisher'];		// Publisher
						$PublisherLoc = $tmp_result['publisherLoc'];// PublisherLoc
					}
				}
				else 
				{		
					//echo "TO-DO: Else Part of parseInbook()<br />";
					break;   // Not an inbook
				}
			
				$found = 1;
				break;
			}
			else
			{
			}
		}
		
		$returnArray = array('found' => $found, 'InbookTitle' => $InbookTitle, 'BookTitle' => $BookTitle, 'Editors' => $Editors, 
							'Publisher' => $Publisher, 'PublisherLoc' => $PublisherLoc);
				
		return $returnArray;
	}
	
	function parseEditorHelper1($between, $the_rest)
	{
		$match[1] = $between;
		$match[3] = $the_rest;
		$editor = parseAuthor($match[1]);							// (between)
		$remaining_str = removePageNumbers($match[3]);				// Remove page numbers $match[3] == (the rest)
		// Using parseBook() first
		$cleaned = clean($remaining_str);
		$book_result = parseBook($cleaned);
		if($book_result['found'] == 1)
		{
			$booktitle = $book_result['BookTitle'];
			$publisher = $book_result['Publisher'];
			$publisherLoc = $book_result['PublisherLoc'];
		}
		else
		{
			$splitArray = splitStr(clean($remaining_str));				// Cleaned and Splitted (after year)
			$booktitle = $splitArray['before'];							// after year but before publisher
			$pub_result = parsePublisherAndCity(clean($splitArray['after']));	// Sent remaining to publisher.	
			$publisher = $pub_result['publisher'];						// Publisher
			$publisherLoc = $pub_result['publisherLoc'];				// Location
		}	
		
		return array('booktitle' => $booktitle, 'editor' => $editor, 'publisher' => $publisher, 'publisherLoc' => $publisherLoc);			
	}
	
	function parseEditorHelper2($after_last_comma, $before_comma, $the_rest)
	{
		$comma_match[2] = $after_last_comma;
		$comma_match[1] = $before_comma;
		$match[3] = $the_rest;
		$editor = parseAuthor($comma_match[2]);						// $comma_match[2] == (after last comma)
		$booktitle = clean($comma_match[1]);						// $comma_match[1] == (before comma)
		$remaining_str = removePageNumbers($match[3]);				// Remove page numbers $match[3] == (the rest)
		// Using parseBook() first
		$book_result = parseBook($remaining_str);
		if($book_result['found'] == 1)
		{
			$tmp_booktitle = $book_result['BookTitle'];
			if( ($tmp_booktitle != "unknown") && !empty($tmp_booktitle) ) {
				$booktitle = $book_result['BookTitle'];
			}
			$publisher = $book_result['Publisher'];
			$publisherLoc = $book_result['PublisherLoc'];
		}
		else
		{
			$pub_result = parsePublisherAndCity(clean($remaining_str));	// Sent remaining to publisher.		
			$publisher = $pub_result['publisher'];						// Publisher
			$publisherLoc = $pub_result['publisherLoc'];				// Location
		}
		return array('booktitle' => $booktitle, 'editor' => $editor, 'publisher' => $publisher, 'publisherLoc' => $publisherLoc);
	}
	
	function parseEditor($afterQuoteStr)
	{
		$editor = "";
		$booktitle = "";
		$publisher = "";
		$publisherLoc = "";
		$flag = "";
				
		$bracket_pattern = '/\bin\b(.*)(\beditors\b|\beditor\b|\(eds\.\)|\(eds\)|\(ed\.\)|\(ed\))(.*)/i';
		$unbracket_pattern = '/\bin\b(.*)\b(edited\sby|ed\.|eds\.|eds|ed)\b(.*)/i';
		$starts_with_in_pattern = '/^\s*\bin\b(.*)/';				
	
		// 1. Check for bracket pattern "in (between) (eds) (the rest) 
		//	  - if (between) has "multiple name pattern"
		//			- if (after [,] has no word)
		//				- parseHelper1()
		//			- else
		//				- parseHelper2()
		//    - else if (between) has [,]
		// 			- if (after [,] has no word)
		//				- (between) == authors		} parseHelper1()
		//				- (the rest) == booktitle	} parseHelper1()
		//			- else
		//				- (before comma) == booktitle  } parseHelper2()
		//				- (after comma) == authors	   } parseHelper2()
		//	  - else
		//			- (between) == authors
		//			- (the rest) == booktitle
		
		// 2. Check for unbracket pattern "in (between) eds (the rest)
		//	  - if (after eds) starts with (year)
		//			- (between) == authors
		//			- (after year) == booktitle
		//	  - else 
		//			- (between) == booktitle
		//			- (the rest) == authors
		
		// 3. Else if starts with "in ..."   [No Editors]
		//	 - Editor = none
		// 	 - If parseBook() == true
		//     - Set booktitle, publisher and location
		//   - Else search DB
	
		if(preg_match($bracket_pattern, $afterQuoteStr, $match))			// 1.
		{
			$comma_pattern = '/(.*)[,]([^,]*)$/u';							// (before comma) [,] (after last comma)

			if(preg_match($comma_pattern, $match[1], $comma_match))			// $match[1] == (between)
			{
				$multiple_name_pattern = '/(.*\,\p{Zs}?\&[^,]*)(.*)/u';					// $multiple_name_pattern checks for ", &" pattern
				if(preg_match($multiple_name_pattern, $match[1], $name_match)) 		// 
				{
																					// (before comma) == $name_match[1] 
					if(str_word_count($name_match[2]) == 0)							// (after last comma) == $name_match[2]
					{
						$result = parseEditorHelper1($match[1], $match[3]);			// 	$match[1] == $between; $match[3] = $the_rest;
						$editor = $result['editor'];
						$booktitle = $result['booktitle'];
						$publisher = $result['publisher'];
						$publisherLoc = $result['publisherLoc'];
					}
					else
					{
						// $name_match[2] = $after_last_comma;	$name_match[1] = $before_comma; $match[3] = $the_rest;
						$result = parseEditorHelper2($name_match[2], $name_match[1], $match[3]);  
						$editor = $result['editor'];
						$booktitle = $result['booktitle'];
						$publisher = $result['publisher'];
						$publisherLoc = $result['publisherLoc'];
					}
				}
				else if(str_word_count($comma_match[2]) == 0)					// $comma_match[2] == (after last comma)
				{
					$result = parseEditorHelper1($match[1], $match[3]);
					$editor = $result['editor'];
					$booktitle = $result['booktitle'];
					$publisher = $result['publisher'];
					$publisherLoc = $result['publisherLoc'];
//******************MOVED TO parseEditorHelper1****************************************************************
//					$editor = parseAuthor($match[1]);							// (between)
//					$remaining_str = removePageNumbers($match[3]);				// Remove page numbers $match[3] == (the rest)
//					// Using parseBook() first
//					$cleaned = clean($remaining_str);
//					$book_result = parseBook($cleaned);
//					if($book_result['found'] == 1)
//					{
//						$booktitle = $book_result['BookTitle'];
//						$publisher = $book_result['Publisher'];
//						$publisherLoc = $book_result['PublisherLoc'];
//					}
//					else
//					{
//						$splitArray = splitStr(clean($remaining_str));				// Cleaned and Splitted (after year)
//						$booktitle = $splitArray['before'];							// after year but before publisher
//						$pub_result = parsePublisherAndCity(clean($splitArray['after']));	// Sent remaining to publisher.	
//						$publisher = $pub_result['publisher'];						// Publisher
//						$publisherLoc = $pub_result['publisherLoc'];				// Location
//					}	
//**************************************************************************************************************			
					/*$splitArray = splitStr(clean($remaining_str));				// Cleaned and Splitted (the rest)
					$booktitle = $splitArray['before'];							// after year but before publisher
					$pub_result = parsePublisherAndCity(clean($splitArray['after']));	// Sent remaining to publisher.	
					$publisher = $pub_result['publisher'];						// Publisher
					$publisherLoc = $pub_result['publisherLoc'];				// Location*/
				}
				else
				{
					$result = parseEditorHelper2($comma_match[2], $comma_match[1], $match[3]);
					$editor = $result['editor'];
					$booktitle = $result['booktitle'];
					$publisher = $result['publisher'];
					$publisherLoc = $result['publisherLoc'];
//******************MOVED TO parseEditorHelper2****************************************************************
//					$editor = parseAuthor($comma_match[2]);						// $comma_match[2] == (after last comma)
//					$booktitle = clean($comma_match[1]);						// $comma_match[1] == (before comma)
//					$remaining_str = removePageNumbers($match[3]);				// Remove page numbers $match[3] == (the rest)
//					// Using parseBook() first
//					$book_result = parseBook($remaining_str);
//					if($book_result['found'] == 1)
//					{
//						$booktitle = $book_result['BookTitle'];
//						$publisher = $book_result['Publisher'];
//						$publisherLoc = $book_result['PublisherLoc'];
//					}
//					else
//					{
//						$pub_result = parsePublisherAndCity(clean($remaining_str));	// Sent remaining to publisher.		
//						$publisher = $pub_result['publisher'];						// Publisher
//						$publisherLoc = $pub_result['publisherLoc'];				// Location
//					}
//**************************************************************************************************************
					/*$pub_result = parsePublisherAndCity(clean($remaining_str));	// Sent remaining to publisher.
					$publisher = $pub_result['publisher'];						// Publisher
					$publisherLoc = $pub_result['publisherLoc'];				// Location*/
				}
			}
			else
			{	
				$editor = parseAuthor($match[1]);							// (between)
				$remaining_str = removePageNumbers($match[3]);				// Remove page numbers $match[3] == (the rest)
				// Using parseBook() first
				$cleaned = clean($remaining_str);
				$book_result = parseBook($cleaned);
				if($book_result['found'] == 1)
				{
					$booktitle = $book_result['BookTitle'];
					$publisher = $book_result['Publisher'];
					$publisherLoc = $book_result['PublisherLoc'];
				}
				else
				{
					$splitArray = splitStr(clean($remaining_str));				// Cleaned and Splitted (after year)
					$booktitle = $splitArray['before'];							// after year but before publisher
					$pub_result = parsePublisherAndCity(clean($splitArray['after']));	// Sent remaining to publisher.	
					$publisher = $pub_result['publisher'];						// Publisher
					$publisherLoc = $pub_result['publisherLoc'];				// Location
				}	
				/*$splitArray = splitStr(clean($remaining_str));				// Cleaned and Splitted (the rest)
				$booktitle = $splitArray['before'];							// after year but before publisher
				$pub_result = parsePublisherAndCity(clean($splitArray['after']));	// Sent remaining to publisher.	
				$publisher = $pub_result['publisher'];						// Publisher
				$publisherLoc = $pub_result['publisherLoc'];				// Location*/
			}
		}
		else if(preg_match($unbracket_pattern, $afterQuoteStr, $match))  	// 2. 
		{					
			//$year_pattern = '/^[^a-z]*\(\d{4}\)(.*)/i';					// Starts with no alphabet followed by 4 digits
			$year_pattern = '/^[^\p{L}]*\(\p{Nd}{4}\)(.*)/u';				// Starts with no alphabet followed by 4 digits
			if(preg_match($year_pattern, $match[3], $year_match))			// $match[3] == (after eds)
			{	
				$editor = parseAuthor($match[1]);							// (between)
				$remaining_str = removePageNumbers($year_match[1]);			// Remove page numbers (after year)

				// Using parseBook() first
				$cleaned = clean($remaining_str);
				$book_result = parseBook($cleaned);
				if($book_result['found'] == 1)
				{
					$booktitle = $book_result['BookTitle'];
					$publisher = $book_result['Publisher'];
					$publisherLoc = $book_result['PublisherLoc'];
				}
				else
				{
					$splitArray = splitStr(clean($remaining_str));				// Cleaned and Splitted (after year)
					$booktitle = $splitArray['before'];							// after year but before publisher
					$pub_result = parsePublisherAndCity(clean($splitArray['after']));	// Sent remaining to publisher.	
					$publisher = $pub_result['publisher'];						// Publisher
					$publisherLoc = $pub_result['publisherLoc'];				// Location
				}				
			}
			else
			{	
				$booktitle = clean($match[1]); 								// (between)
				$clean_match = clean($match[3]);							// (the rest)
				$editor = parseAuthor($clean_match);						// editor		
				$remaining_str = removePageNumbers($clean_match);			// Remove page numbers
				$remaining_str = removeEditorFromStr($remaining_str);		// Remove editor found from string
				// Using parseBook() first
				$book_result = parseBook($remaining_str);
				if($book_result['found'] == 1)
				{	
					$tmp_booktitle = $book_result['BookTitle'];
					if( ($tmp_booktitle != "unknown") && !empty($tmp_booktitle) ) {
						$booktitle = $book_result['BookTitle'];
					}
					$publisher = $book_result['Publisher'];
					$publisherLoc = $book_result['PublisherLoc'];
				}
				else
				{
					$pub_result = parsePublisherAndCity(clean($remaining_str));		// Sent remaining to publisher.		
					$publisher = $pub_result['publisher'];						// Publisher
					$publisherLoc = $pub_result['publisherLoc'];				// Location
				}
			}
		}
		else if(preg_match($starts_with_in_pattern, $afterQuoteStr, $match))
		{		
			$editor = "";
			$cleaned = clean($match[1]);
			$book_result = parseBook($cleaned);
			if($book_result['found'] == 1)
			{
				$booktitle = $book_result['BookTitle'];
				$publisher = $book_result['Publisher'];
				$publisherLoc = $book_result['PublisherLoc'];
			}
			else
			{
				$pub_result = parsePublisherAndCity($cleaned);		// Sent remaining to publisher.		
				$publisher = $pub_result['publisher'];						// Publisher
				$publisherLoc = $pub_result['publisherLoc'];				// Location
			}
		}
		else
		{
			$flag = "not inbook";
			//echo "Else Part of parseEditor()<br />";
		}
		
		$returnArray = array('editor' => $editor, 'booktitle' => $booktitle, 'publisher' => $publisher, 'publisherLoc' => $publisherLoc, 'flag' => $flag);
		
		return $returnArray;
	}
	
	function parsePublisherAndCity($str)						// TO-DO: Search DB for Publisher and City
	{
		$parsePubSearch = parsePublisherSearch($str);
		if($parsePubSearch['found'])								// Do DB Search first
		{	
			$publisher = $parsePubSearch['Publisher'];
			$publisherLoc = $parsePubSearch['PublisherLoc'];
		}
		else {
			$publisher = $str;
			$publisherLoc = "";
		}
		
		$returnArray = array('publisher' => $publisher, 'publisherLoc' => $publisherLoc);
		return $returnArray;
	}
	
	function removePageNumbers($str)							// Remove page numbers 
	{
		$patterns[0] = '/\(*\p{Zs}*pp\.\p{Zs}*(\p{Nd}+)\p{Zs}*(\p{Pd}|n)\p{Zs}*(\p{Nd}+)\p{Zs}*\)*/iu';	//$replace[3] = 'PP. PAGE(-|n)PAGE';
		$patterns[1] = '/(\p{Nd}+)\p{Zs}*(\p{Pd}|n)\p{Zs}*(\p{Nd}+)/u';                     	//$replace[4] = 'PAGE(-|n)PAGE';
		
		for($i = 0; $i < sizeof($patterns); $i++)
		{
			$str = preg_replace($patterns[$i], '', $str);
		}
		
		return $str;
	}
	
	function removeEditorFromStr($str)							// Remove editors by using comma
	{
		$return_str = "";
		
		$pattern[0] = '/^[^,]*[,]/u';
		$pattern[1] = '/^[^(]*[(]/u';
		
		$return_str = preg_replace($pattern[0], '', $str);
		
		if(str_word_count($return_str) == 0)					// Comma doesn't work
		{
			$return_str = preg_replace($pattern[1], '', $str);	// Use bracket instead as separator
		}
		
		return $return_str;
	}
	
	function splitStr($str)										// To split at the publisher's part of string
	{
		$before = "";
		$after = "";
		$pattern = '/^(.*)([(]|[.])(.*)$/u';						// Split at [(] or [.]
		
		if(preg_match($pattern, $str, $match))
		{
			$before = $match[1];
			$after = $match[2].$match[3];
		}
		else
		{
			$before = $str;
			$after = "";	
		}
		
		$returnArray = array('before' => $before, 'after' => $after);
		
		return $returnArray;
	}
	
?>
