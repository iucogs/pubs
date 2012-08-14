<?php 

require_once('../lib/constants.php');	// Definition for PARSE_VERSION, DB_NAME
require_once('../../'.PARSE_VERSION.'/Parse.class.php');
require_once('../classes/Collections.class.php');
require_once('../classes/Citations.class.php');
require_once('../classes/Logger.class.php');

// Create parse object
$parse = new Parse();
$parse->setOptions(array('db_name' => DB_NAME, 'db_user' => DB_USER, 'db_password' => DB_PASSWORD, 'db_host' => DB_HOST));

// Create collection object and citation object.
$collection = new Collections();
$citations = new Citations();

// Globals 
$MODE = "";															// MODE for return mode[ JSON | JAVASCRIPT ];
$JS_RESPONSE_FUNCTION = "Page.parseFileIntoCollection_response"; 	// Default function for JAVASCRIPT MODE to send response to.

// Local error variable
$error = 0;

//Logger::instance()->clear();

if(isset($GLOBALS['HTTP_RAW_POST_DATA']))
{
	// SET MODE (Used by all functions for return mode, JSON or JAVASCRIPT)
	$MODE = "json";

	$jsonObj = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
	
	// Get info from JSON data.
	$return_arr = initialize_JSON_data($jsonObj);
	
	// Initialize JSON variables
	$filename = $return_arr['filename'];						// value is citation entries sent.
	$collection_name = $return_arr['collection_name'];
	$action = $return_arr['action'];
	$submitter = $return_arr['submitter'];
	$owner = $return_arr['owner'];
	
	// Initialize local variables
	$entryTime = time();
	
	
	if(empty($filename))
	{
		return_error(5);
	}
	else if(empty($collection_name))
	{
		return_error(5);	
	}
	else
	{	
		parseIntoCollection($filename, $collection_name, $action, $entryTime, $submitter, $owner);
	}
}
else if(isset($_FILES["myfile"]))  // Process file upload.
{
	// SET MODE (Used by all functions for return mode, JSON or JAVASCRIPT)
	$MODE = "javascript";

	// Get info from POST data.
	$return_arr = initialize_file_upload();
	
	// Initialize POST variables
	$result = $return_arr['result'];
	$filename = $return_arr['filename'];
	$collection_name = $return_arr['collection_name'];
	$action = $return_arr['action'];
	$submitter = $return_arr['submitter'];
	$owner = $return_arr['owner'];
	
	// Initialize local variables
	$entryTime = time();
	//$filename = "../uploads/".$filename;  // Update file path for parse.
	
	// Return upload error immediately.
	if($result != 1) {
		?>	   
		<script language="javascript" type="text/javascript">window.top.window.stopUpload(<?php echo $result; ?>,'<?php echo $filename; ?>');</script>
		<?php
	}
	else 
	{
		parseIntoCollection($filename, $collection_name, $action, $entryTime, $submitter, $owner);
	}
}
else	// Neither file upload or JSON request. Try alert then echo JSON string.
{
	return_mode_error();	
}

/************************************/
//			   FUNCTIONS			//
/************************************/

// Function that will do all (add, insert, parse).
function parseIntoCollection($filename, $collection_name, $action, $entryTime, $submitter, $owner)
{
	global $collection;
	global $citations;
	global $parse;
	
	if($action == "new") // Create collection
	{
		$collection_name = trim($collection_name);
		$collection_id = $collection->checkCollection($collection_name, $submitter, $owner);
		$is_TI = is_file_TI($filename); 		// Check if entries are TI: type
		$collection_status = "exists";
		$continue_to_parse = false;
		
		// Initialize $continue_to_parse
		if($is_TI == true) {			// Doesn't matter if collection_name exists or empty, processing TI:
			$continue_to_parse = true; 
		}
		else {  						// Not TI:
			if($collection_id == false)  	
			{
				if(empty($collection_name)) {	// Not TI: but no collection_name given
					$continue_to_parse = false;
					$collection_status = "empty_name";
				}
				else {					// Impossible error: Not TI:, Empty collection_name but also collection_id is false?
					$continue_to_parse = true;	
				}
			}
			else {						// Not TI: Processing regular file upload
				$continue_to_parse = true;
			}
		}
		
		// Continue parsing based on rules above.
		if($continue_to_parse) 	
		{	
			// Check for Bibtext or EndNote format.
			$format = get_entry_format($filename);
					
			if($format == "text") 
			{
				$parse_result = $parse->execute($filename, $submitter, $owner, $entryTime);	// Execute Parse
			}
			else
			{
				$parse_result = import($format, $filename, $submitter, $owner, $entryTime);	
			}
			
			/*********************************************************/
			if(is_array($parse_result))  // TI:  multiple collections
			{
				parse_ti($parse_result, $submitter, $owner, $entryTime);
			}
			/*********************************************************/	
			else if($parse_result)
			{	
				// Update similar_to DB			
				$citation_ids = updateSimilarToDB_and_returnCitationIDs($entryTime);
				if($citation_ids != false) {
					// Add citations to collection. (Still check if collection exists or not).
					action_new_add_collections($collection_name, $citation_ids, $entryTime, $submitter, $owner);
				}
				else {	// Error updating similar to.
					return_error(5);
				} 
			}
			else
			{
				return_error(2); // Error writing to DB
			}
		}
		else	// Return collection exists message.
		{
			$responseObj = array("error" => $collection->error, "collection_status" => $collection_status, "collection_id" => $collection_id, "collection_name" => $collection_name);
			sendResponse($responseObj);
		}
	}
	else if($action == "insert")  // Insert into current collection
	{
		if(($collection_id = $collection->checkCollection($collection_name, $submitter, $owner)) != false) // Collection id exist.
		{
			// Check for Bibtext or EndNote format.
			$format = get_entry_format($filename);
					
			if($format == "text") 
			{		
				$parse_result = $parse->execute($filename, $submitter, $owner, $entryTime);	// Execute Parse
			}
			else
			{
				$parse_result = import($format, $filename, $submitter, $owner, $entryTime);	
			}
			
			// Update similar_to DB			
			$citation_ids = updateSimilarToDB_and_returnCitationIDs($entryTime);
			if($citation_ids != false) {
				// Insert citations to collection.
				action_insert_into_collections($collection_id, $citation_ids, $entryTime, $submitter, $owner);
			}
			else {	// Error updating similar to.
				return_error(5);
			} 
		}
		else	// Collection id doesnt exists. Simply create new collection or return error?
		{
			return_error(5);
		}
	}
	else  // Action is other than "new" or "insert"
	{
		return_error(5);
	}
}

// send response.
function sendResponse($responseObj) 
{
	global $MODE;
	global $JS_RESPONSE_FUNCTION; // This is only for javascript mode
	
	$jsonString = json_encode($responseObj);
	
	if($MODE == "javascript") {
		?><script language="javascript" type="text/javascript">window.top.window.<?php echo $JS_RESPONSE_FUNCTION; ?>('<?php echo $jsonString; ?>');</script><?php
	}
	else if($MODE == "json")
	{
		echo $jsonString;
	}
	else 
	{
		return_mode_error();
	}
		
	//die(); // Stop code execution
}

function return_error($new_error) // $function is only for javascript
{
	global $error;
	$error .= $new_error;
	$responseObj = array("error" => $error);
	sendResponse($responseObj);
}

function return_mode_error()
{
	// Neither file upload or JSON request. Try alert then echo JSON string.
	?><script language="javascript" type="text/javascript">alert("Error: Unknown MODE. Should be either JSON or JAVASCRIPT.");</script><?php
	return_error("Unknown MODE!");
}

function is_file_TI($filename)
{
	$found = false;
	$file = @fopen($filename, "r") or exit("Unable to open file! <$filename>"); 	// Check if file exist and open it
	
	// Output a line of the file until the end is reached
	while(!feof($file))
	{
		$line = trim(fgets($file)); 
		if(mb_substr($line, 0, 3) == "TI:")
		{
			$found = true;
		}
	}
	
	return $found;
}

function parse_ti($parse_result, $submitter, $owner, $entryTime)
{
	global $collection;
	global $citations;
	global $error;

	/*********************************************************/
	if(is_array($parse_result))  // TI:  multiple collections
	{
		// Collections ids inserted into DB
		$collection_ids = array();
		
		$temp = array();
		
		$temp['parse_result'] = $parse_result;
		
		// Add collection to DB
		foreach($parse_result as $arr)
		{
			$c_name = $arr[0];
			$timestamp = $arr[1];
			// Grab the citation_ids
			$citation_ids = array();
			$cite_result = $citations->getCitations_byTimestamp_all($timestamp);
			
			// $cite_result[0] is now the citations. $cite_result[1] is the similar_citations
			foreach($cite_result[0] as $citation)
			{
				$citation_ids[] = $citation['citation_id'];
			}
			
			// Update similar_to DB
			if($citations->updateSimilarToByTimestamp($timestamp))
			{
				// Successful
			}
			else $error .= 5;

			$coll_result = $collection->createAndAddCollection($c_name, $citation_ids, $submitter, $owner, true);  // Optional FORCE_CREATE collection_name
			
			// Create or update collections table entries
			// Ruth 6/27/12
	//		$citations->createAndUpdateCollectionsTable($coll_result[1], $submitter, $owner);
			
			if($coll_result != -1) // Collection exists. Shouldn't be here due to FORCE CREATE
			{
				// Save collection id
				$collection_ids[] = $coll_result;
			}
		}
		
		// Send response.
		$responseObj = array("error" => $error, "collection_status" => "ti", "parsed_submitter" => $submitter, "parsed_owner" => $owner, "parsed_timestamp" => $entryTime);
		sendResponse($responseObj); 
	}
	/**********************************************************/
}

function action_new_add_collections($collection_name, $citation_ids, $entryTime, $submitter, $owner)
{
	global $collection;
	global $citations;
	
	$result_arr = $collection->createAndAddCollection($collection_name, $citation_ids, $submitter, $owner);

	if($result_arr != false) // Either collection doesnt exist(1) or collection exist (-1)
	{
		list($collection_status, $collection_id, $insert_count, $duplicates) = $result_arr;
		
		// Create or update collections table entries
		// Ruth 6/27
	//	$citations->createAndUpdateCollectionsTable($collection_id, $submitter, $owner);
		
		// Send response.
		$responseObj = array("error" => $collection->error, "collection_status" => $collection_status, 
						"collection_id" => $collection_id, "collection_name" => $collection_name, 
						"insert_count" => $insert_count, "duplicates" => $duplicates, "parsed_timestamp" => $entryTime);		
		sendResponse($responseObj); 
	}
	else // DB create or insert error.
	{
		return_error(5);
	}
}

function updateSimilarToDB_and_returnCitationIDs($entryTime)
{
	global $citations;
	
	// Update similar_to DB
	if($citations->updateSimilarToByTimestamp($entryTime))
	{
		$citation_ids = array();
		
		// Get parsed citation_ids
		$result = $citations->getCitations_byTimestamp_all($entryTime);
		foreach($result[0] as $citation)
		{
			$citation_ids[] = $citation['citation_id']; ;
		}
		//print_r($citation_ids);
		return $citation_ids;		
	}
	else
	{
		// Return similar to error here.
		return false;
	}
}

function action_insert_into_collections($collection_id, $citation_ids, $entryTime, $submitter, $owner)
{
	global $collection;
	global $citations;
	
	$insert_result = $collection->insert_member_of_collection($collection_id, $citation_ids, $submitter, $owner);
	if($insert_result != -1)  // Collection insertion successful.
	{
		list($collection_id, $insert_count, $duplicates) = $insert_result;
		
		// Get collection name using collection_id.
		$collection_array = $collection->getCollectionByID($collection_id);
		$collection_name = $collection_array['collection_name'];
		
		// Create or update collections table entries
		// Ruth 6/27
	//$citations->createAndUpdateCollectionsTable($collection_id, $submitter, $owner);
		
		// collection_status 0 means "inserted into existing collection".
		$responseObj = array("error" => $collection->error, "collection_status" => "exists_inserted", 
						"collection_id" => $collection_id, "collection_name" => $collection_name, 
						"insert_count" => $insert_count, "duplicates" => $duplicates, "parsed_timestamp" => $entryTime);
		sendResponse($responseObj);
	}
	else
	{
		return_error(5);
	}
}

function initialize_file_upload()
{
	// Upload result
	$result = 0;
	
	// For security
	str_replace('.', '', $_FILES['myfile']['name']);
	str_replace('/', '', $_FILES['myfile']['name']);
	
	$filename = $_FILES["myfile"]["name"];
	$collection_name = $_POST["entryName"];
	$action = $_POST["parse_action"];
	$submitter = $_POST["submitter"]; 
	$owner = $_POST["owner"]; 
	
	if (($_FILES["myfile"]["type"] == "text/plain") && ($_FILES["myfile"]["size"] < 10000000)) // 10,000 kb ~ 9.5 MB 
	{
			$result = 1;
			$filename = $_FILES['myfile']['tmp_name'];  // Full path to OS temp file
	}
	else 
	{
		if ($_FILES["myfile"]["type"] != "text/plain") 
		{
			$result = 2; //  invalid file type.
		}
		else if ($_FILES["myfile"]["size"] >= 10000000) 
		{
			$result = 3;  //File is too big
		}
	}
	sleep(1);
	
	$return_arr = array('result' => $result, 'filename' => $filename, 'collection_name' => $collection_name, 'action' => $action, 'submitter' => $submitter, 'owner' => $owner);
	
	return $return_arr;
}

function initialize_JSON_data($jsonObj)
{
	if(isset($jsonObj->{'request'}->{'submitter'})){
		$submitter = trim($jsonObj->{'request'}->{'submitter'});
	}
	if(isset($jsonObj->{'request'}->{'owner'})){
		$owner = trim($jsonObj->{'request'}->{'owner'});
	}
	if(isset($jsonObj->{'request'}->{'collection_name'})){ 
		$collection_name = trim($jsonObj->{'request'}->{'collection_name'});
	}
	if(isset($jsonObj->{'request'}->{'entries'})) { // Grab values (entries)
		$value = $jsonObj->{'request'}->{'entries'};
	}
	if(isset($jsonObj->{'request'}->{'action'})) {
		$action = $jsonObj->{'request'}->{'action'};
	}

	
	$filename = tempnam(sys_get_temp_dir(),''); // Create unique temp file in OS temp folder.
	$handle = fopen($filename, 'w') or exit("Unable to open file!"); // Check if file exist and open it
	fwrite($handle, $value);
	fclose($handle);
	
	$return_arr = array('filename' => $filename, 'collection_name' => $collection_name, 'action' => $action, 'submitter' => $submitter, 'owner' => $owner);
		
	return $return_arr;
}

//*******************
// IMPORT FUNCTIONS
//*******************
function get_entry_format($filename)
{
	$format = "text";
	$file = @fopen($filename, "r") or exit("Unable to open file! <$filename>"); 	// Check if file exist and open it
	
	$line = trim(fgets($file)); 
	while($line == "") $line = trim(fgets($file));
	
	if(trim(mb_substr($line, 0, 2)) == "%0")		// Endnote
	{
		$format = "endnote";
	}
	else if(trim(mb_substr($line, 0, 1)) == "@")	// Bibtex
	{
		$format = "bibtex";
	}
	else 											// Plain text
	{
		$format = "text";
	}
	
	return $format;
}

function import($format, $filename, $submitter, $owner, $timestamp)
{
	if($format == "endnote")
	{
		return import_endnote($filename, $submitter, $owner, $timestamp);
	}
	else if($format == "bibtex")
	{
		return import_bibtex($filename, $submitter, $owner, $timestamp);
	}
	else
	{
		return false;	
	}
}

function import_endnote($filename, $submitter, $owner, $timestamp)
{
	global $parse;
	
	$handle = fopen($filename, 'r') or exit("Unable to open file!"); // Check if file exist and open it
	
	$field_map = array("year" => "%D","title" => "%T","journal" => "%J","volume" => "%V","number" => "%N",
					   "pages" => "%P","publisher" => "%I","location" => "%C","booktitle" => "%B", "url" => "%U",
					   "editor" => "%E", "author" => "%A");
	$type_map = array(
				"article" => "Journal Article",
				"book" => "Book",
				//"edited_book" => "Book",
				"conference" => "Conference Paper",
				"inbook" => "Book Section",
				//"incollection" => "Generic",
				//"inproceedings" => "Conference Paper",
				//"manual" => "Generic",
				//"mastersthesis" => "Thesis",
				"phdthesis" => "Thesis",
				"proceedings" => "Conference Proceedings",
				//"techreport" => "Generic", 
				"unpublished" => "Unpublished Work", 
				"misc" => "Generic", 
				//"translated_book" => "Book", 
				"web_published" => "Web Published");	
	
	$found_an_entry = true;
	$entry = array();
	$author_array = array();
	
	while(!feof($handle))
	{
		$line = trim(fgets($handle)); 
		
		$endnote_key = mb_substr($line, 0, 2);				// Grab endnote tag.
				
		if($endnote_key == "%0")							// Starts of an endnote entry.
		{
			if($found_an_entry) {		
				$found_an_entry = false;					// First run toggle.
			}
			else {
				$entry['author'] = $author_array;
				$parse->external_write_EntryToDB($entry);	// Write previous entry to DB only when it's not first run.
			}
			
			// Reset entry and author_array
			$author_array = array();
			$entry = array('raw' => "", 'year' => "", 'author' => "", 'type' => "", 'title' => "", 'name' => "", 'format' => "", 
						'volume' => "", 'pages' => "", 'chapter' => "", 'number' => "", 'editor' => "", 'booktitle' => "", 'publisher' => "",
						'location' => "", 'url'=> "", 'submitter' => $submitter, 'owner' => $owner, 'entryTime' => $timestamp, 'citation_id' => -1); 
			
			$entry['raw'] = $line."\n";								// Append raw field.
			$type_value = trim(mb_substr($line, 2));
			$found_key = array_search($type_value, $type_map);	// Find [endnote type value => pubs's citation type] equivalent.
			
			if($found_key !== FALSE) {
				$entry['type'] = $found_key;					// Citation format/type
			}
			else {
				$entry['type'] = "misc";						// Any other type is "misc"
			}
		}
		else if($endnote_key == "%A")
		{
			$author_array[] = trim(mb_substr($line, 2));
		}
		else if($endnote_key == "%D")
		{
			$year = trim(mb_substr($line, 2));
			$entry['year'] = array($year);
		}
		else
		{
			if(!empty($line)) $entry['raw'] .= $line."\n";			// Append raw field if $line is not empty lines.
			$field = array_search($endnote_key, $field_map);	// Find [endnote key => pubs's field] equivalent.
			
			if($field !== FALSE) 
			{
				$entry[$field] = trim(mb_substr($line, 2));		// Save data into $entry
				//echo "TRUE: ".trim(mb_substr($line, 2))."<br />";
			}
			else
			{
				// Unknown endnote tag. Do nothing since all lines are appended in raw.
				//echo "FALSE: ".trim(mb_substr($line, 2))."<br />";
			}
		}
	}
	
	if($found_an_entry == false) 					// Means at least an entry has been found.
	{	
		$entry['author'] = $author_array;
		$parse->external_write_EntryToDB($entry);	// Write last entry to DB
	}
	
	fclose($handle);
	return true;
}

function import_bibtex($filename, $submitter, $owner, $timestamp)
{
	global $parse;
	
	$handle = fopen($filename, 'r') or exit("Unable to open file!"); // Check if file exist and open it
	
	$field_map = array("year","title","journal","volume","number","pages","publisher","location","booktitle","url","editor","author");
	
	$type_map = array("article","book","edited_book","conference","inbook","incollection","inproceedings","manual","mastersthesis","phdthesis",
					  "proceedings","techreport","unpublished","misc","translated_book","web_published");	
	
	$found_an_entry = true;
	$entry = array();
	
	while(!feof($handle))
	{
		$line = trim(fgets($handle)); 
		
		$bibtex_key = mb_substr($line, 0, 1);				// Grab bibtex tag.
				
		if($bibtex_key == "@")								// Starts of an endnote entry.
		{
			if($found_an_entry) {		
				$found_an_entry = false;					// First run toggle.
			}
			else {
				$parse->external_write_EntryToDB($entry);	// Write previous entry to DB only when it's not first run.
			}
			
			// Reset entry
			$entry = array('raw' => "", 'year' => "", 'author' => "", 'type' => "", 'title' => "", 'name' => "", 'format' => "", 
						'volume' => "", 'pages' => "", 'chapter' => "", 'number' => "", 'editor' => "", 'booktitle' => "", 'publisher' => "",
						'location' => "", 'url'=> "", 'submitter' => $submitter, 'owner' => $owner, 'entryTime' => $timestamp, 'citation_id' => -1); 
			
			$entry['raw'] = $line."\n";								// Append raw field.
			
			if(preg_match('/^@(.*){(.*)$/', $line, $match)) {
				$type_value = trim($match[1]);
			}
			else $type_value = "";
			
			$found_key = array_search($type_value, $type_map);	// Find [endnote type value => pubs's citation type] equivalent.
			
			if($found_key !== FALSE) {
				$entry['type'] = $type_value;					// Citation format/type
			}
			else {
				$entry['type'] = "misc";						// Any other type is "misc"
			}
		}
		else
		{	
			if(trim($line) == "}") 						// Skip end of bibtex entry.
			{
				$entry['raw'] .= $line."\n";			// Append to raw field
			}			
			else if(!empty($line)) 
			{
				$entry['raw'] .= $line."\n";			// Append raw field if $line is not empty lines.

				if(preg_match('/^(.*)=\s*"\s*(.*)\s*"\s*,*\s*$/', $line, $match)) {
					$field = trim($match[1]);
					$value = trim($match[2]);
				}
				else
				{
					$field = "";
					$value = "";	
				}
				
				$found_field = array_search($field, $field_map);		// Find [endnote key => pubs's field] equivalent.
				
				if($found_field !== FALSE) 
				{
					if($field == "author")
					{
						$entry['author'] = parse_bibtex_author($value);
					}
					else if($field == "year")
					{
						$entry['year'] = array($value);
					}
					else
					{
						$entry[$field] = $value;		// Save data into $entry
					}
				}
				else
				{
					// Unknown endnote tag. Do nothing since all lines are appended in raw.
				}
			}
		}
	}
	
	if($found_an_entry == false) 					// Means at least an entry has been found.
	{	
		$parse->external_write_EntryToDB($entry);	// Write last entry to DB
	}
	
	fclose($handle);
	return true;
}

function parse_bibtex_author($author_string)
{
	// Explode on "and"
	$split_array = preg_split('/[\s,]+(and|&)[\s,]+/i', $author_string, -1, PREG_SPLIT_NO_EMPTY);
	
	$auth_array = array();
	
	foreach($split_array as $name)
	{
		$split_name = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY);
		
		// Grab last word of each as lastname.
		$lastname = end($split_name);
	
		// The rest is firstname
		$firstname = "";
		for($i = 0; $i < sizeof($split_name) - 1; $i++)
		{
			$firstname .= $split_name[$i];
		}
		
		$auth_array[] = $lastname.", ".$firstname.",";
	}
	
	return $auth_array;
}


?>