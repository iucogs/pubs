<?php

class Parse
{
	var $db_ids;
	var $options;
	var $collection;
	var $citation;
		
	function Parse()
	{
		error_reporting(E_ALL);        		// Enable all error reporting.
		ini_set('display_errors', 1);  		// Display errors on screen.
		//set_error_handler('ErrorHandler');	// Enable this to allow Fatal Error to continue.
		date_default_timezone_set('America/Indianapolis');
		
		mb_internal_encoding("UTF-8"); 		// Setting mb_internal_encoding to UTF-8
		mb_regex_encoding('UTF-8');			// Setting mb_regex_encoding to UTF-8
		
		require_once('../classes/Collections.class.php');
		$this->collection = new Collections();
		
		require_once('../classes/Citations.class.php');
		$this->citation = new Citations();

		//require_once 'Zend/Feed.php';
		//require_once 'Zend/Search/Lucene.php';
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
		//include('includes/fuzzymatch.php');
		
		// Options
		$this->options = array('html' => false, 
							   'db' => true, 
							   'db_name' => 'dev', 
							   'db_user' => 'dev', 
							   'db_password' => 'minddev08', 
							   'db_host' => '156.56.91.21'	// Local, if remote add :3306
							   );
	}
	
	function setOptions($options)
	{
		// Print out html 
		if(array_key_exists('html', $options))
		{
			if(is_bool($options['html'])) $this->options['html'] = $options['html'];
		}
		
		// Save to DB
		if(array_key_exists('db', $options))
		{
			if(is_bool($options['db'])) $this->options['db'] = $options['db'];
		}
		
		// DB Name
		if(array_key_exists('db_name', $options))
		{
			if(is_string($options['db_name'])) $this->options['db_name'] = $options['db_name'];
		}

		// DB User
		if(array_key_exists('db_user', $options))
		{
			if(is_string($options['db_user'])) $this->options['db_user'] = $options['db_user'];
		}
		
		// DB Password
		if(array_key_exists('db_password', $options))
		{
			if(is_string($options['db_password'])) $this->options['db_password'] = $options['db_password'];
		}
		
		// DB Host
		if(array_key_exists('db_host', $options))
		{
			if(is_string($options['db_host'])) $this->options['db_host'] = $options['db_host'];
		}
	}
	
	function printOptions()
	{
		// File path.
		//echo "<center>filename => ".$this->filename."</center>";
	
		echo "<center>";
		foreach($this->options as $key => $value)
		{
			echo "$key => ";
			if(is_bool($value)) {
				echo ($value ? '<span style="font-weight:bold;color:green">true</span>' : '<span style="font-weight:bold;color:red">false</span>')." <br />";
			}
			else if(is_string($value)) {
				echo '<span style="font-weight:bold;color:darkblue">'.$value.'</span><br />';
			}
			else {
				echo ($value ? '<span style="font-weight:bold;color:green">true</span>' : '<span style="font-weight:bold;color:red">false</span>')." <br />";
			}
		}
		echo "</center>";
	}
	
	function execute($filename, $collection_id, $collection_name, $submitter, $owner, $timestamp) // filename must be full path unless the file is on the same directory as parse
	{
		//echo "<h1>Citation Parser v1.0</h1>";
		
		// Variables declarations
		$data = array();			// Hold all entries.
		$db_ids_holder = array();	// Hold all db ids inserted.
		$line = ""; 				// A line of raw entry.
		$newline = "<br />";		// New line for HTML output.
		$count = 0;					// Count for HTML printing
		
		
		/*******************************/ // TI:
		$collections = array();  	// [c_name:timestamp]
		$timestamp_count = 0;		// timestamp.1 (will be incremented the very first time to 1)
		$ti_toggle = false;
				
		
		/*******************************/

		global $previous_author_str;
		$previous_author_str = -1;	// Holds the previous author/authors string for ---,
		global $same_author_str;
		$same_author_str = false;
		
		//$filename = getcwd().DIRECTORY_SEPARATOR.$filename;		// Get full path
		if(!file_exists($filename))
		{
			echo "Error accessing file '".$filename."'. Make sure the file exists and have the right permissions. "; // Error?
		}
		
		// Print file path.
		if($this->options['html']) echo "<center>file path => <b>".$filename."</b></center>";
		
		$file = @fopen($filename, "r") or exit("Unable to open file! <$filename>"); 	// Check if file exist and open it
		
		//echo "Parsing... ".$filename.$newline.$newline.$newline;
		
		// Connect to database
		$this->connectDB($this->options['db_name'], $this->options['db_user'], $this->options['db_password'], $this->options['db_host']);
		
		// Truncate tables
		//mysql_query("TRUNCATE TABLE authors");
		//mysql_query("TRUNCATE TABLE citation");
		//mysql_query("TRUNCATE TABLE author_of");
		
		//$processing_arr = array(1, 12, 13, 15, 52, 55, 56, 58, 64, 68, 79, 80, 82);
		
		// Find total lines to be parsed
		$lines = 0;
		while (!feof($file)) {
			$line = trim(fgets($file));
			if($line == "" || mb_substr($line, 0, 2) == "//"){continue;}  	// Check for empty lines and skip
			if(mb_substr($line, 0, 3) == "TI:"){continue;}					// Check for TI and skip
			$lines++;
		}
		rewind($file); // Rewind the file pointer to the beginning.
		
		// Reset progress session if it is set
		if(isset($_SESSION['progress']))
		{
			session_start();
			$_SESSION['progress'] = array("parse", 0, $lines);
			session_write_close();
		}
		
		// Output a line of the file until the end is reached
		while(!feof($file))
		{
			//sleep(1);
			// TO-DO: Try and catch clause - if catch: then log it.
			// 		  To prevent code from dying in the middle.
			try{
			
			/*if(in_array($count, $processing_arr))	{ echo $count; }
			else
			{
				//$count++;
				//continue;
			}*/

			
			// [0]: Raw Entry [1]: Year [2]: Authors Name
			$entry = array('raw' => "", 'year' => "", 'author' => "", 'type' => "", 'title' => "", 'name' => "", 'format' => "", 
						'volume' => "", 'pages' => "", 'chapter' => "", 'number' => "", 'editor' => "", 'booktitle' => "", 'publisher' => "",
						'location' => "", 'url'=> "", 'submitter' => $submitter, 'owner' => $owner, 'entryTime' => $timestamp, 'citation_id' => -1);  
			
			$line = trim(fgets($file));     				// Read a line
												
			// Encoding: Decode everything into UTF-8.
			if(($encoding = mb_detect_encoding($line, "UTF-8, ISO-8859-1")) == "UTF-8")   // ISO-8859-1 is always last
			{	
				// Already in UTF-8.
			}
			else
			{	
				$line = @iconv($encoding,'UTF-8', $line);  // @ suppresses error msgs
			}
						
			// Check for ---, to replace RAW (only) with previous citation's authors
			$raw_addition = "";
			$original_line = $line; // Make a copy of line
			if($previous_author_str != -1)
			{
				if(preg_match('/^(\p{Pd}){3,5}[,]?/u',$line, $match) == 1)  // UTF-8 	\x{2013} = EN-DASH | \x{2014} = EM-DASH | minus sign (\x{2013}{3,5}|\x{2014}{3,5}|\-{3,5})
				{
					$line = str_replace($match[0],$previous_author_str,$line);
					$raw_addition = " [ Replaced \"".$match[0]."\" with \"".trim($previous_author_str)."\" ]";
					$same_author_str = true;	// Use the previous_author_str again
				}
				else {
					$same_author_str = false;	// When false, previous_author_str should be set again.
				}
			}
			
			if($line == "" || mb_substr($line, 0, 2) == "//"){continue;}  	// Check for empty lines and remove it.
			else {
				$count++; // Update Count
				if(isset($_SESSION['progress']))
				{
					session_start();
					$_SESSION['progress'] = array("parse", $count, $lines);
					session_write_close();
				}
			}
			
			/*****************************************/ // TI:
			if(mb_substr($line, 0, 3) == "TI:")
			{
				$ti_toggle = true;
				
				// Get collection name
				$c_name = mb_substr($line,3);
				
				// Increase timestamp count for new c_name			
				$timestamp_count++;
				
				// Set main array
				$collections[] = array($c_name, $timestamp.".$timestamp_count");
				continue;
			}
			else if($ti_toggle)
			{
				// Set timestamp number
				$entry['entryTime'] = $timestamp.".$timestamp_count";
			}	
			
			/*******************************************/
			
			$entry['raw'] = $original_line.$raw_addition;			// Save raw entry.		
			
			// URL processing
			$entry['url'] = parseURL($line);
			
			$parseDate = parseDate($line);					// Get year and volume info
			$vol_info = $parseDate['info'];					// Save volume info
			
			// Volume processing
			$entry['volume'] = $vol_info['volume'];
			$entry['chapter'] = $vol_info['chapter'];
			$entry['number'] = $vol_info['number'];
			$entry['pages'] = $vol_info['pages'];		
			
			$entry['year'] = $parseDate['year'];  			// Save year
			$splitStr = $this->splitYear($entry['year'], $line); 			// Split string by first date parsed.
			
			// Check for format.
			if(parseFormat($line, $entry['year']) == "MLA")
			{
				$entry['format'] = "MLA";
				$typeInfo = parseTypeMLA($line);// Decide entry type.
				
				$entry['author'] = $typeInfo['author']; 		// Get authors
				$entry['type'] = $typeInfo['type'];
				
				if($entry['type'] == "article")					// Journal article entry
				{
					$entry['title'] = $typeInfo['title'];
					$entry['name'] = $typeInfo['name'];
				}
				else if($entry['type'] == "inbook")				// Inbook article/chapter entry
				{
					$entry['title'] = $typeInfo['title'];
					$entry['booktitle'] = $typeInfo['booktitle'];
					if(is_array($typeInfo['editor'])){
						$entry['editor'] = print_str_array($typeInfo['editor']);
					}
					else {
						$entry['editor'] = $typeInfo['editor'];
					}
					$entry['publisher'] = $typeInfo['publisher'];
					$entry['location'] = $typeInfo['location'];
				}
				else if($entry['type'] == "book")				// Book entry
				{
					$entry['title'] = $typeInfo['title'];
					$entry['publisher'] = $typeInfo['publisher'];
					$entry['location'] = $typeInfo['location'];
				}
				else{  // Do nothing
				}
			}
			else                              
			{
				$entry['format'] = "APA";
				$entry['author'] = parseAuthorAPA($splitStr[0]); 	// Parse author: Assuming author located before the publication year.
				
				if(!empty($splitStr[1])){
					$typeInfo = parseTypeAPA($line, $splitStr[1], $splitStr[0]);		// Decide entry type. $splitStr[1] is string after first year found.
				}
				else{
					$typeInfo = parseTypeAPA($line, "", $splitStr[0]);
				}
				$entry['type'] = $typeInfo['type'];
				
				if($entry['type'] == "proceedings")			// Proceeding entry
				{
					$entry['title'] = $typeInfo['title']; 
					$entry['name'] = $typeInfo['name']; 
					$entry['publisher'] = $typeInfo['publisher'];
					$entry['location'] = $typeInfo['location'];
				}
				else if($entry['type'] == "article")			// Journal article entry
				{
					$entry['title'] = $typeInfo['title'];
					$entry['name'] = trim($typeInfo['name']);
				}
				else if($entry['type'] == "inbook")				// Inbook article/chapter entry
				{
					$entry['title'] = $typeInfo['title'];
					$entry['booktitle'] = $typeInfo['booktitle'];
					if(is_array($typeInfo['editor'])){
						$entry['editor'] = print_str_array($typeInfo['editor']);
					}
					else {
						$entry['editor'] = $typeInfo['editor'];
					}
					$entry['publisher'] = $typeInfo['publisher'];
					$entry['location'] = $typeInfo['location'];
				}
				else if($entry['type'] == "book" || $entry['type'] == "edited_book")	// Book entry or Edited Book entry
				{
					$entry['title'] = $typeInfo['title'];
					$entry['publisher'] = $typeInfo['publisher'];
					$entry['location'] = $typeInfo['location'];
//					if(is_array($typeInfo['editor'])){
//						$entry['editor'] = print_str_array($typeInfo['editor']);
//					}
//					else {
//						$entry['editor'] = $typeInfo['editor'];
//					}
				}
				else{  // Do nothing
				}
			}
			
			// Done Parsing Here!!!
			
			// Clean up "unknown" values from entry
			$entry = $this->remove_unknown_from_entry($entry);
			
			$duplicate_toggle = false; // Toggle for printHTML
						
			if($this->options['db'])  // writing to db as opposed to printing to HTML
			{
				if(($citation_id = parseEntryToDB($entry)) != false)
				{
					$entry['citation_id'] = $citation_id; // Just so that printHTML can print citation id.
					$db_ids_holder[] = $citation_id;	 // storing citation ids
					
					if (($result = $this->collection->insert_member_of_collection($collection_id, array($citation_id), $submitter, $owner)) == -1)
					{
						// return error
					}
					if (($result = $this->citation->updateSimilarToWhenSaving($citation_id)) == false)
					{
						// return error
					}
					
				}
				else
				{
					$duplicate_toggle = true;  // debugging; no longer for handling duplicates; could be used for errors in inserting entry
				}
				
				
			}
			
			if($this->options['html'])  //for debugging only
			{
				if(!$duplicate_toggle) { printHTML($entry, $count); }
			}
				
			//$data[] = $entry;  							    // Save current entry into data array.
			
			}  // Try clause
			
			// Catch Exception and print it out. Then continue with the next entry.
			catch(Exception $e)
			{
				//echo 'Message: '.$e->getMessage();
				return false;
			}
		}
		fclose($file);
		
		// Save to global db_ids for easy access.
		$this->db_ids = $db_ids_holder;
		
		// Print out to bibtex file here.
		//bibtexWriter($data);
		
		//echo "End Of Program";
		// <<<<<<<<<<<<<< END >>>>>>>>>>>>>>>>>>
		
		/*************************************/ // TI:
		if($ti_toggle)
		{
			return $collections;
		}
		else
		{
			return true;
		}
		/*************************************/
	}

	function ErrorHandler($errno, $errstr, $errfile, $errline)
	{
		if($errno == E_USER_ERROR || $errno == E_ERROR)
		{
			echo "FATAL ERROR OCCURRED!!<br />";
		}
		echo "Error No.	: $errno   <br />
			  Error Str	: $errstr  <br />
			  Error File: $errfile <br />
			  Error Line: $errline <br /><br />";
	}

	// Create connection to database.
	function connectDB($db_name, $db_user, $db_password, $db_host)
	{
		//@ $db = mysql_pconnect('dev.cogs.indiana.edu','dev','minddev08');
		@ $db = mysql_pconnect($db_host, $db_user, $db_password);
			
		if(!$db){
			echo 'Error: Could not connect to database.';
			exit;
		}
		
		mysql_select_db($db_name);
		
		// Telling MySQL that we're expecting and sending data as UTF8
		$query = "SET NAMES 'utf8'";
		mysql_query($query);
	}	
	
	// Split string by first year found (first part is usually the authors' name). 
	// $splitStr[0] - before , $splitStr[1] - after
	function splitYear($yearArray, $line)
	{
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
				$splitStr[0] = "";  	// Author part
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
	
	function external_write_EntryToDB($entry)
	{
		// Connect to database
		$this->connectDB($this->options['db_name'], $this->options['db_user'], $this->options['db_password'], $this->options['db_host']);
		
		if(($citation_id = parseEntryToDB($entry)) != false)
		{
			//$entry['citation_id'] = $citation_id; 		// Might want to return the entire entry instead?
			return $citation_id;
		}
		else
		{
			return false;
		}	
	}
	
	// This function simply removes unknown value in any field in entry array 
	function remove_unknown_from_entry($entry)
	{
		foreach($entry as $field=>$value)
		{
			if($value == "unknown")
			{
				$entry[$field] = "";	
			}
		}		
		return $entry;
	}
}		
?>
