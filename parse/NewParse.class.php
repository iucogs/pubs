<?php
/***************************
 IU COGS PUBS Parser v2.0.0
 Written by: Patrick Craig/pjcraig
 Maintained by:
 Written: Mon Feb 4, 2013

 Changelog: 
 2.4.2013 / pjcraig: the new parser is born. Refactored to handle strings and
 return JSON for the API. Code from original parser reduced from 503 lines to
 approx 250 of actual code.
 ***************************/

class NewParse {
		
	function Parse() {
		error_reporting(E_ALL);        		// Enable all error reporting.
		ini_set('display_errors', 1);  		// Display errors on screen.
		//set_error_handler('ErrorHandler');	// Enable this to allow Fatal Error to continue.
		date_default_timezone_set('America/Indianapolis');
		
		mb_internal_encoding("UTF-8"); 		// Setting mb_internal_encoding to UTF-8
		mb_regex_encoding('UTF-8');			// Setting mb_regex_encoding to UTF-8
		
		include('includes/printHTML.php');
		include('includes/parseLib.php');
		include('includes/parseDate.php');
		include('includes/parseFormat.php');
		include('includes/parseAuthor.php');
		include('includes/parseAuthorAPA.php');
		include('includes/parseAuthorMLA.php');
		include('includes/parseAuthorSearch.php');
		include('includes/parseTypeAPA.php');
		include('includes/parseTypeMLA.php');
		include('includes/parseJournal.php');
		include('includes/parseJournalMLA.php');
		include('includes/parseBibtex.php');
		include('includes/parseEntryToDB.php');
		include('includes/parseVolume.php');
		include('includes/parseInbook.php');
		include('includes/parseBook.php');
		include('includes/parseURL.php');
		include('includes/parseProceedingAPA.php');
		include('includes/parsePublisherSearch.php');
		include('includes/parsePublisher.php');
		include('includes/parseEditedBook.php'); 
        include('/home/patrick/Sites/pubs/lib/constants.php');
    } 

    function execute($raw, $submitter, $owner, $timestamp) {
      // Variable declarations
      $count = 0;
      $collections = array();
      $timestamp_count = 0;
      $ti_toggle = false;
      $original_line = "";

      global $previous_author_str;
      $previous_author_str = -1;
      global $same_author_str;
      $same_author_str = false;
      
      // Simple checks for API usage.  
      if ($submitter == "")
        $submitter = "API User";
      
      if ($owner == "")
        $owner == "API User";

      $entry = array('raw' => "", 'year' => "", 'author' => "", 'type' => "", 'title' => "", 'name' => "", 'format' => "", 
					 'volume' => "", 'pages' => "", 'chapter' => "", 'number' => "", 'editor' => "", 'booktitle' => "", 'publisher' => "",
					 'location' => "", 'url'=> "", 'submitter' => $submitter, 'owner' => $owner, 'entryTime' => $timestamp, 'citation_id' => -1);  
			
												
	  // Encoding: Decode everything into UTF-8.
	  if(($encoding = mb_detect_encoding($raw, "UTF-8, ISO-8859-1")) == "UTF-8") {  // ISO-8859-1 is always last
				// Already in UTF-8.
			} else {	
				$raw = @iconv($encoding,'UTF-8', $raw);  // @ suppresses error msgs
			}
						
			// Check for ---, to replace RAW (only) with previous citation's authors
			$raw_addition = "";
			$original_raw = $raw; // Make a copy of line
			if($previous_author_str != -1)
			{
				if(preg_match('/^(\p{Pd}){3,5}[,]?/u',$raw, $match) == 1) {  // UTF-8 	\x{2013} = EN-DASH | \x{2014} = EM-DASH | minus sign (\x{2013}{3,5}|\x{2014}{3,5}|\-{3,5})
					$raw = str_replace($match[0],$previous_author_str,$raw);
					$raw_addition = " [ Replaced \"".$match[0]."\" with \"".trim($previous_author_str)."\" ]";
					$same_author_str = true;	// Use the previous_author_str again
				} else {
					$same_author_str = false;	// When false, previous_author_str should be set again.
				}
			}
			
			if($raw == "" || mb_substr($raw, 0, 2) == "//"){continue;}  	// Check for empty lines and remove it.
			else {$count++;}												// Update Count
			
			/*****************************************/ // TI:
			if(mb_substr($raw, 0, 3) == "TI:")	{
				$ti_toggle = true;
				
				// Get collection name
				$c_name = mb_substr($raw,3);
				
				// Increase timestamp count for new c_name			
				$timestamp_count++;
				
				// Set main array
				$collections[] = array($c_name, $timestamp.".$timestamp_count");
				continue;
			} else if($ti_toggle)	{
				// Set timestamp number
				$entry['entryTime'] = $timestamp.".$timestamp_count";
			}	
			
			/*******************************************/
			
			$entry['raw'] = $original_raw;                 // Save original entry.
			
			// URL processing
			$entry['url'] = parseURL($raw);
			
			$parseDate = parseDate($raw);					// Get year and volume info
			$vol_info = $parseDate['info'];					// Save volume info
			
			// Volume processing
			$entry['volume'] = $vol_info['volume'];
			$entry['chapter'] = $vol_info['chapter'];
			$entry['number'] = $vol_info['number'];
			$entry['pages'] = $vol_info['pages'];		
			
			$entry['year'] = $parseDate['year'];  			// Save year
			$splitStr = $this->splitYear($entry['year'], $raw); 			// Split string by first date parsed.

			// Check for format.
			if(parseFormat($raw, $entry['year']) == "MLA")
			{
				$entry['format'] = "MLA";
				$typeInfo = parseTypeMLA($raw);// Decide entry type.
				
				$entry['author'] = $typeInfo['author']; 		// Get authors
				$entry['type'] = $typeInfo['type'];
				
				if($entry['type'] == "article")	{			// Journal article entry
					$entry['title'] = $typeInfo['title'];
					$entry['name'] = $typeInfo['name'];
                } else if ($entry['type'] == "inbook") {				// Inbook article/chapter entry
                    $entry['title'] = $typeInfo['title'];
					$entry['booktitle'] = $typeInfo['booktitle'];
                    if(is_array($typeInfo['editor'])) {
						$entry['editor'] = print_str_array($typeInfo['editor']);
					} else  {
						$entry['editor'] = $typeInfo['editor'];
					}
					$entry['publisher'] = $typeInfo['publisher'];
					$entry['location'] = $typeInfo['location'];
				} else if($entry['type'] == "book") {				// Book entry
					$entry['title'] = $typeInfo['title'];
					$entry['publisher'] = $typeInfo['publisher'];
					$entry['location'] = $typeInfo['location'];
				} else { 
                    ; }// Do nothing
			} else {
			    $entry['format'] = "APA";
				$entry['author'] = parseAuthorAPA($splitStr[0]); 	// Parse author: Assuming author located before the publication year.
				if(!empty($splitStr[1])){
					$typeInfo = parseTypeAPA($raw, $splitStr[1], $splitStr[0]);		// Decide entry type. $splitStr[1] is string after first year found.
				} else {
					$typeInfo = parseTypeAPA($raw, "", $splitStr[0]);
				}
				$entry['type'] = $typeInfo['type'];
				
				if($entry['type'] == "proceedings")	{		// Proceeding entry
					$entry['title'] = $typeInfo['title']; 
					$entry['name'] = $typeInfo['name']; 
					$entry['publisher'] = $typeInfo['publisher'];
					$entry['location'] = $typeInfo['location'];
				} else if($entry['type'] == "article") {			// Journal article entry
					$entry['title'] = $typeInfo['title'];
					$entry['name'] = trim($typeInfo['name']);
				} else if($entry['type'] == "inbook")	{			// Inbook article/chapter entry
					$entry['title'] = $typeInfo['title'];
					$entry['booktitle'] = $typeInfo['booktitle'];
					if(is_array($typeInfo['editor'])){
						$entry['editor'] = print_str_array($typeInfo['editor']);
					} else {
						$entry['editor'] = $typeInfo['editor'];
					}
					$entry['publisher'] = $typeInfo['publisher'];
					$entry['location'] = $typeInfo['location'];
				} else if($entry['type'] == "book" || $entry['type'] == "edited_book") {	// Book entry or Edited Book entry
					$entry['title'] = $typeInfo['title'];
					$entry['publisher'] = $typeInfo['publisher'];
					$entry['location'] = $typeInfo['location'];
				} else {  
                   ; // Do nothing
				}
			}
			
			// Done Parsing Here!!!
			// Clean up "unknown" values from entry
			$entry = $this->remove_unknown_from_entry($entry);
			/*$duplicate_toggle = false; // Toggle for printHTML
			{
				if(!$duplicate_toggle) { printHTML($entry, $count); }
			}	*/		
			// Catch Exception and print it out. Then continue with the next entry.
			//catch(Exception $e) {
			//	return false;
			//}
			// <<<<<<<<<<<<<< END >>>>>>>>>>>>>>>>>>
	     	/*************************************/ // TI:
            //if($ti_toggle)	
			//  return $collections;
		
            return json_encode($entry);
	}

    function ErrorHandler($errno, $errstr, $errfile, $errline) { 
      if($errno == E_USER_ERROR || $errno == E_ERROR) {
          echo "FATAL ERROR OCCURRED.<br />";
      }
      echo "Error No. : $errno   <br />
            Error Str : $errstr  <br />
            Error File: $errfile <br />
            Error Line: $errline <br /><br />";
    }

    function connectDB($db_name, $db_user, $db_password, $db_host) {
        

        @ $db = mysql_pconnect($db_host, $db_user, $db_password);
        if (!$db) {
            echo 'Error: Could not connect to database.';
            exit;
        }

        mysql_select_db($db_name);
        $query = "SET NAMES = 'utf8'"; // Lets MySQL know we're working in UTF8.
        mysql_query($query);
    }

    function remove_unknown_from_entry($entry) {
      foreach ($entry as $field=>$value) {
          if($value == "unknown") {
              $entry[$field] = "";
          }
      }
      return $entry;
    }

    function parseJSONToDB($JSON) {
     $entry = Array();
     /* $link = $this->connectDB(DB_NAME, DB_USER, DB_PASSWORD, DB_HOST);
      if (!$link) {
          echo "error connecting to db!";
      }
      if (!mysql_select_db(DB_NAME, $link)) {
          echo "error selecting database!";
      }*/
      $entry = json_decode($JSON, true);
      return parseEntryToDB($entry);
    }

      function splitYear($yearArray, $line) {
        $splitStr = "";

        if($yearArray[0] == 'unknown'){ 
            // Split up to first [.] - Author. The rest is title
            if(preg_match('/^(.*)[.](.*)/u',$line, $match) == 1)
            {
                $splitStr[0] = $match[1];  // Author part
                $splitStr[1] = $match[2];  // Title part
            }
            else // No [.] in string
            {
                $splitStr[0] = "";      // Author part
                $splitStr[1] = $line;   // Title part
            } 
        }
        else if(strcasecmp($yearArray[0], "in press") == 0){     // Year is "in press" instead of number
            $tmp = str_ireplace("in press","IN PRESS",$line);
            $splitStr = explode('IN PRESS', $tmp, 2);
        }
        else if(strcasecmp($yearArray[0], "forthcoming") == 0){  // Year is "forthcoming" instead of number
            $tmp = str_ireplace("forthcoming","FORTHCOMING",$line);
            $splitStr = explode('FORTHCOMING', $tmp, 2);
        }
        else {
            $splitStr = explode($yearArray[0], $line, 2);        // Split the string by using first year found and take first part/token.
        }

        return $splitStr;
    }

    
}
?>
