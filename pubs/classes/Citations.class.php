<?php 

// Read from the database and output it as an XML document.
class Citations
{
	var $link;
	var $table;
	var $limit;
	var $page;
	var $error;
	var $debug;
	
	function Citations()
	{
		require_once('Logger.class.php');			// Class for logging
		require_once('../lib/mysql_connect.php');
		require_once('../lib/fuzzymatch.php');
		$this->table = "citations";
		$this->limit = '';
		$this->page = 1;
		$this->error = 0;
		$this->debug = array();
	}
	
	function connectDB_multi()
	{
		$link;
		if(!$link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)) {
			$this->error .= 1;
		}
		
		// Check connection 
		if (mysqli_connect_errno()) {
			echo "Connect failed: ".mysqli_connect_error();
			$this->error .= 1;
		}
		return $link;
	}
	
	// Create connection to database.
	function connectDB()
	{
		$link;
		if (!$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)) {
    		$this->error .= 1;
		}
			
		if (!mysql_select_db(DB_NAME, $link)) {
			$this->error .= 1;
		}
		return $link;
	}
	
	function add_location_to_db($location)
	{
		$this->link = $this->connectDB();
		$query = "SELECT * FROM locations WHERE location='".mysql_real_escape_string($location)."'";
		$result = $this->doQuery($query, $this->link);
		if(mysql_num_rows($result) == 0)
		{
			$query = "INSERT INTO locations (location) VALUES ('".mysql_real_escape_string($location)."')";
			$result = $this->doQuery($query, $this->link);
			return true;
		}
		else {
			return false;
		}
	}
	
	function add_publisher_to_db($publisher) 
	{
		$this->link = $this->connectDB();
		$query = "SELECT * FROM publishers WHERE publisher='".mysql_real_escape_string($publisher)."'";
		$result = $this->doQuery($query, $this->link);
		if(mysql_num_rows($result) == 0)
		{
			$query = "INSERT INTO publishers (publisher) VALUES ('".mysql_real_escape_string($publisher)."')";
			$result = $this->doQuery($query, $this->link);
			return true;
		}
		else {
			return false;
		}
	}
	
	function move_tmp_file($filename, $citation_id)
	{
		if(empty($filename)) {
			$this->error .= 9;  //
		}
		else {
			$target_path = PDF_DIRECTORY."/".PUBS_VERSION."/temp/";
			$destination_path = PDF_DIRECTORY."/".PUBS_VERSION."/".$citation_id."/";
			// check if destination directory exists
			if (file_exists($destination_path)) {
				
			}
			else {
				//if not create it
				if (mkdir($destination_path, 0777))  // Somehow directory stays at 0755
				{
					chmod($destination_path, 0777);  // For directory permission change.
				} 
				else 
				{
					$this->error .= 9;
					return false;
				}
			}
			
			if(file_exists($target_path.$filename))
			{		
				if(rename($target_path.$filename, $destination_path.$filename)) {
					chmod($destination_path.$filename, 0777);	// Set file permission.
					return true;
				}
				else {
					return false;
				}
				return false;
			}
			else {
				return false;
			}
		}
	}
	
	function move_to_deleted($filename, $citation_id)
	{
		if(empty($filename)) {
			$this->error .= 3;
		}
		else {
			$target_path = PDF_DIRECTORY."/".PUBS_VERSION."/".$citation_id."/";
			$destination_path = PDF_DIRECTORY."/".PUBS_VERSION."/deleted/";
			
			if(file_exists($target_path.$filename))
			{	
				if(rename($target_path.$filename, $destination_path.$filename)) {
					return true;
				}
				else {
					return false;
				}
				return false;
			}
			else
			{
				return false;
			}
		}
	}
	
	function doQuery($query, $link) {  //added for json function
	
		if (!$result = mysql_query($query, $link)) {
			$this->error .= 1;
			echo mysql_error($link);
			echo "<br />";
			echo $query."<br /><br />";
		}
		return $result;	
	}
	
	function trim_all_in_array($array)
	{
		// Alter $str (or array value) by reference.
		foreach($array as &$str)
		{
			if(is_string($str)) $str = trim($str);
		}
		// Unset reference since it is still exist.
		unset($str);
		
		$this->debug['trim'] = $array;
		return $array;
	}
	
	function save($args, $args_authors, $coll_id)
	{
		$this->link = $this->connectDB();
				
		// Trim values in arguments
		$args = $this->trim_all_in_array($args);
		$args_authors = $this->trim_all_in_array($args_authors);
		$args['last_modified'] = time();
		$new_citation_id = "-1";
		$current_citation_id = $args['citation_id'];
		$original_file = "";
		
		// Check for verified. 
		// If it is set then update location table for parse searching.
		if(!empty($args['verified']) && $args['verified'] == 1)
		{
			if(!empty($args['location'])) 
			{
				if($this->add_location_to_db($args['location']))
				{
					//$this->error .= $args['location']." added to 'location' DB.<br />";
				}
				else
				{
					//$this->error .= $args['location']." is in DB<br />";
				}
			}
			if(!empty($args['publisher'])) 
			{
				if($this->add_publisher_to_db($args['publisher']))
				{
					//$this->error .= $args['publisher']." added to 'publisher' DB.<br />";
				}
				else
				{
					//$this->error .= $args['publisher']." is in DB<br />";
				}
			}
		}
		
		// Update citation table
		$query = "SELECT * FROM $this->table WHERE citation_id='".mysql_real_escape_string($current_citation_id)."'";
		$result = $this->doQuery($query, $this->link);
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
        {
			$original_file = $row['filename'];
			//$this->error .= "Citation ID: ".$row['citation_id']."<br />";
		}
		if (mysql_num_rows($result) > 0)
		{
			$query = "UPDATE $this->table SET "; 
			$count = 1; 
			foreach($args as $key => $value)
			{			
				// Check for last element, don't add comma
				//if($count == sizeof($args)){  
				//	$query .= $key."='".mysql_real_escape_string($value)."' ";
				//}	
				//else{
				if($key == "filename" && empty($value))
				{
					// Skip because file might already exist.
				}
				else
				{
					$query .= $key."='".mysql_real_escape_string($value)."', ";
				}
				//}
				//$count++;
			}
			
			$query = substr($query, 0, -2); // Take out last comma and space
			$query .= " "; // Add the space again.

			$query .= " WHERE citation_id='".mysql_real_escape_string($current_citation_id)."';";
			$result = $this->doQuery($query, $this->link);
			if (!$result) {	$this->error .= 2; }
			else { 
				//$this->error .= "Previous citation updated successfully.<br />"; 
				
				// Filename uploaded and original file exist.
				if(!empty($args['filename']) && !empty($original_file)) { 
					// Move original file to deleted.
					if($this->move_to_deleted($original_file, $current_citation_id)) {
					}
					else { $this->error .= "A"; }
					
					// Move uploaded file from temp folder to save folder.
					if($this->move_tmp_file($args['filename'], $current_citation_id))	{ //"File ".$args['filename']." moved successfully."
					}
					else { $this->error .= "B"; }
				}
				// File uploaded and no original file exist.
				else if(!empty($args['filename']) && empty($original_file)) {
					// Move uploaded file from temp folder to save folder.
					if($this->move_tmp_file($args['filename'], $current_citation_id))	{ //"File ".$args['filename']." moved successfully."
					}
					else { $this->error .= 7; }
				}
				// File was cleared but original file exist. NOTE: Should delete or ignore? 
				else if(empty($args['filename']) && !empty($original_file)) {
					// Do nothing for now.
				}
				// File was not uploaded and original file does not exist.
				else {
					// Do nothing.
				}		
			}
		}
		else   // Insert citation instead
		{
			// Build query
			$args_str = "";
			$value_str = "";
			foreach($args as $key => $value)
			{
				if($key != "citation_id") // Skip citation_id
				{
					$args_str .= "".mysql_real_escape_string($key).",";
					$value_str .= "'".mysql_real_escape_string($value)."',";
				}
			}
				
			$args_str = substr($args_str, 0, -1);
			$value_str = substr($value_str, 0, -1);
			
			$query = "INSERT INTO $this->table (".$args_str.") VALUES (".$value_str.")";
			//$this->error .= $query."<br />";
			$result = $this->doQuery($query, $this->link);
			if (!$result) { $this->error .= 2; }
			else { 
				//$this->error .= "Previous new citation added successfully.<br />"; 
				$new_citation_id = (int)mysql_insert_id();
				$current_citation_id = $new_citation_id;
				
				if(!empty($args['filename'])) {
					if($this->move_tmp_file($args['filename'], $current_citation_id))	{
						//$this->error .= "File ".$args['filename']." moved successfully.<br />"; 
					}
					else {
						$this->error .= 8; //3
					}
				}
				else {  //Filename is empty.
					
				}
			}
		}
	
		$current_citation_id = $this->updateAuthors($args, $args_authors, $current_citation_id);
				
		if(!empty($current_citation_id)){
			
			// Save collection_index
			$this->saveOneCitationToCollectionsTable($current_citation_id, $coll_id, $args['submitter'], $args['owner']);
			//$this->createAndUpdateCollectionsTable($coll_id, $args['submitter'], $args['owner']);
			
			// Update similarTo table using doFuzzyMatch function. 
			if($this->updateSimilarToWhenSaving($current_citation_id, $args['submitter'], $args['owner']))
			{
				return $current_citation_id;
			}
			else 
			{
				$this->error .= 2; 
				return false;
			}
		}
		else{
			$this->error .= 2;
			return false;

		}
	}
	
	// Create misc for all owners
	function createMiscCollectionForAllOwners()
	{
		$this->link = $this->connectDB();
		$query = "SELECT DISTINCT owner FROM collections";
		$result = $this->doQuery($query, $this->link);
		$row = mysql_fetch_assoc($result);
		
		while ($row = mysql_fetch_assoc($result)) {
			$return_arr[] = $this->createMiscCollectionForOneOwner($row['owner']);			
		}
		
		return !array_search(false,$return_arr);
	}
	
	// For every owner, create a MISC collection.
	function createMiscCollectionForOneOwner($owner)
	{
		$this->link = $this->connectDB();
		
		// Delete first
		//$query = "DELETE FROM collections_table WHERE coll_name = 'misc'";
		//$result = $this->doQuery($query, $this->link);
		
		// Create a misc collection.
		$query = "SELECT * FROM collections WHERE owner = '".$owner."' AND collection_name = 'misc'";
		$result = $this->doQuery($query, $this->link);
		$misc_coll_id = "";
		if (mysql_num_rows($result) == 0)  // Only insert when misc doesn't exist for the owner
		{
			$query = "INSERT INTO collections (`collection_id` ,`collection_name` ,`user_id` ,`submitter` ,`owner`) VALUES (NULL , 'misc', '0', '".$owner."', '".$owner."');";
			$result = $this->doQuery($query, $this->link);
			$misc_coll_id = mysql_insert_id();
		}
		else {
			$row = mysql_fetch_assoc($result);
			$misc_coll_id = $row['collection_id'];
			
			// Delete first
			$query = "DELETE FROM collections_table WHERE coll_id = '".$misc_coll_id."'";
			$result = $this->doQuery($query, $this->link);
		}
			
		// Get the all the citations that the owner owns but not already in a collection.		
		$query = "SELECT mt.* FROM (";
		$query .= "SELECT ";
		$query .= "'".$misc_coll_id."' AS coll_id, 'misc' AS coll_name, ";
		$query .= "IFNULL(verified0ln,author0ln) AS author0ln, IFNULL(verified0fn,author0fn) AS author0fn, ";
		$query .= "IFNULL(verified1ln,author1ln) AS author1ln, IFNULL(verified1fn,author1fn) AS author1fn, ";
		$query .= "IFNULL(verified2ln,author2ln) AS author2ln, IFNULL(verified2fn,author2fn) AS author2fn, ";
		$query .= "IFNULL(verified3ln,author3ln) AS author3ln, IFNULL(verified3fn,author3fn) AS author3fn, ";
		$query .= "IFNULL(verified4ln,author4ln) AS author4ln, IFNULL(verified4fn,author4fn) AS author4fn, ";
		$query .= "IFNULL(verified5ln,author5ln) AS author5ln, IFNULL(verified5fn,author5fn) AS author5fn, ";
		$query .= "c.*  ";
		$query .= "FROM ( citations c ";
		$query .= "LEFT JOIN ( SELECT ao.citation_id, a.lastname AS verified0ln, a.firstname AS verified0fn FROM author_of ao, authors a WHERE ao.position_num =1 AND a.author_id = ao.author_id ) a0 ON a0.citation_id = c.citation_id ";
		$query .= "LEFT JOIN ( SELECT ao.citation_id, a.lastname AS verified1ln, a.firstname AS verified1fn FROM author_of ao, authors a WHERE ao.position_num =2 AND a.author_id = ao.author_id ) a1 ON a1.citation_id = c.citation_id ";
		$query .= "LEFT JOIN ( SELECT ao.citation_id, a.lastname AS verified2ln, a.firstname AS verified2fn FROM author_of ao, authors a WHERE ao.position_num =3 AND a.author_id = ao.author_id ) a2 ON a2.citation_id = c.citation_id ";
		$query .= "LEFT JOIN ( SELECT ao.citation_id, a.lastname AS verified3ln, a.firstname AS verified3fn FROM author_of ao, authors a WHERE ao.position_num =4 AND a.author_id = ao.author_id ) a3 ON a3.citation_id = c.citation_id ";
		$query .= "LEFT JOIN ( SELECT ao.citation_id, a.lastname AS verified4ln, a.firstname AS verified4fn FROM author_of ao, authors a WHERE ao.position_num =5 AND a.author_id = ao.author_id ) a4 ON a4.citation_id = c.citation_id ";
		$query .= "LEFT JOIN ( SELECT ao.citation_id, a.lastname AS verified5ln, a.firstname AS verified5fn FROM author_of ao, authors a WHERE ao.position_num =6 AND a.author_id = ao.author_id ) a5 ON a5.citation_id = c.citation_id ";
		$query .= "LEFT JOIN authors_unverified au ON c.citation_id = au.citation_id  ) ) 
		AS mt ";
		$query .= "WHERE  (owner='".$owner."' AND citation_id NOT IN (SELECT moc.citation_id FROM member_of_collection moc, collections col WHERE moc.collection_id = col.collection_id AND col.owner = '".$owner."') )";
		
		// Wrap with insert
		$query = "INSERT INTO collections_table (".$query.")";
		$result = $this->doQuery($query, $this->link);
		
		return $result;	
	}
	
	// Truncate collections table
	function truncateCollectionsTable() {
		$this->link = $this->connectDB();
		$query = "TRUNCATE TABLE collections_table";
		$result = $this->doQuery($query, $this->link);
		return $result;
	}
	
	// Update Collections Table for every collections
	function updateEveryCollectionsTable()
	{
		$this->link = $this->connectDB();

		$query = "SELECT collection_id, submitter, owner FROM collections";
		$result = $this->doQuery($query, $this->link);
		$return_arr = array();

		while ($row = mysql_fetch_assoc($result)) {
			$return_arr[] = $this->createAndUpdateCollectionsTable($row['collection_id'], $row['submitter'], $row['owner']);			
		}
		
		return !array_search(false,$return_arr);
	}
	
	// Check collection_index if it is updated.
	function createAndUpdateCollectionsTable($coll_id, $submitter, $owner)
	{
		$this->link = $this->connectDB();
		
		// Get all citations for current coll_id
		//$query = "SELECT DISTINCT citation_id FROM collections c LEFT JOIN member_of_collection moc ON moc.collection_id = c.collection_id WHERE c.collection_id = $coll_id";
		$query = "SELECT citation_id FROM collections c, member_of_collection moc WHERE moc.collection_id = c.collection_id AND c.collection_id = $coll_id";
		$result = $this->doQuery($query, $this->link);
		$return_arr = array();
		if(mysql_num_rows($result) > 0) { // Collection with citations in it
			while ($row = mysql_fetch_assoc($result)) {
				$citation_id = $row['citation_id'];
				$return_arr[] = $this->saveOneCitationToCollectionsTable($citation_id, $coll_id, $submitter, $owner);	 
			}
		}
		else {	// Collection without any citation in it
			$return_arr[] = true; // Add true value since we update successfully without doing anything.
		}
		
		return !array_search(false,$return_arr);
	}
	
	function determineMultipleCollectionOwners($citation_id, $submitter, $owner)
	{
		$this->link = $this->connectDB();
		
		$query = "SELECT citation_id FROM collections c, member_of_collection moc WHERE moc.collection_id = c.collection_id AND c.citation_id = moc.citation_id AND c.owner != $owner";
		$result = $this->doQuery($query, $this->link);
		$return_arr = array();
		if(mysql_num_rows($result) > 0) { // Citation in collection with different owner
			return true;
		}
		else {	// Citation not in collection with different owner
			return false;
		}	
	}
	
	// Check if a given collection has been deleted and remove from collections_table
	function deletedCollectionFromCollectionsTable($coll_id, $submitter, $owner)
	{
		$this->link = $this->connectDB();
		
		$query = "DELETE FROM collections_table WHERE coll_id NOT IN (SELECT collection_id AS coll_id FROM collections WHERE collection_id = $coll_id) AND coll_id = $coll_id";
		$result = $this->doQuery($query, $this->link);
		if($result) return true; else return false;
	}
	
	function deleteAllCollectionsTableNotExistInCollections()
	{
		$this->link = $this->connectDB();
		
		$query = "DELETE FROM collections_table WHERE coll_id NOT IN (SELECT collection_id AS coll_id FROM collections)";
		$result = $this->doQuery($query, $this->link);
		if($result) return true; else return false;
	}
	
	// Save to collection table for fast retrieving.
	function saveOneCitationToCollectionsTable($citation_id, $coll_id, $submitter, $owner)
	{
		$this->link = $this->connectDB();
		
		// Skip all non numbered collections for now.
		if(is_numeric($coll_id) == false) return false;
		
		// Grab info from citations table
		$citation_arr = $this->getCitation_byID($submitter, $owner, $citation_id);
		
		$citation_arr = $citation_arr[0];
		
		// Grab collection_name
		$query = "SELECT collection_name FROM collections c WHERE c.collection_id = $coll_id";
		$result = $this->doQuery($query, $this->link);
		$row = mysql_fetch_assoc($result);
		$coll_name = $row['collection_name'];
		
		// Check if collection_index entry exist
		$query = "SELECT * FROM collections_table ci WHERE ci.coll_id = $coll_id AND citation_id = $citation_id";
		$result = $this->doQuery($query, $this->link);
		
		$skip_column_list = array("collection_id", "author0id", "author1id", "author2id", "author3id", "author4id", "author5id" );
		
		if (mysql_num_rows($result) > 0)
		{
			$set_query = "";
			$set_query .= "coll_id=$coll_id, coll_name='".mysql_real_escape_string($coll_name)."', ";
			
			foreach($citation_arr as $field => $value)
			{
				if(!in_array($field, $skip_column_list))
				{
					$value = mysql_real_escape_string($value);
					$set_query .= "$field='$value', ";
				}
			}
			
			$set_query = substr($set_query,0, -2);
			
			// Update index
			$query = "UPDATE collections_table SET ".$set_query." WHERE coll_id = $coll_id AND citation_id = $citation_id";	//column1=value, column2=value2,...
		}
		else
		{
			$col_query = "coll_id, coll_name, ";
			$val_query = "$coll_id, '".mysql_real_escape_string($coll_name)."', ";
			
			foreach($citation_arr as $field => $value)
			{
				if(!in_array($field, $skip_column_list))
				{
					$col_query .= "$field, ";
					$value = mysql_real_escape_string($value);
					$val_query .= "'".$value."', ";
				}
			}
			
			$col_query = substr($col_query,0, -2);
			$val_query = substr($val_query,0, -2);
			
			// Insert into index
			$query = "INSERT INTO collections_table (".$col_query.") VALUES (".$val_query.")";
		}
		// Update or insert
		$result = $this->doQuery($query, $this->link);
		
		// Update all citations
		$this->updateAllCitationsInColletionTable_byCitationID($citation_arr, $citation_id, $skip_column_list);
		
		if ($result) return true; else return false;
	}
	
	function updateAllCitationsInColletionTable_byCitationID($citation_arr, $citation_id, $skip_column_list)
	{
		$this->link = $this->connectDB();
		$set_query = "";
		//$set_query .= "coll_id=$coll_id, coll_name='".mysql_real_escape_string($coll_name)."', ";
		
		foreach($citation_arr as $field => $value)
		{
			if(!in_array($field, $skip_column_list))
			{
				$value = mysql_real_escape_string($value);
				$set_query .= "$field='$value', ";
			}
		}
		
		$set_query = substr($set_query,0, -2);
		
		// Update all citations in collection table
		$query = "UPDATE collections_table SET ".$set_query." WHERE citation_id = $citation_id";	//column1=value, column2=value2,...
		$result = $this->doQuery($query, $this->link);
		if ($result) return true; else return false;
	}
	
	function updateAuthors($args, $args_authors, $current_citation_id)
	{
		// Flush author_of table associated with citation_id
		$query = "DELETE FROM author_of WHERE citation_id = $current_citation_id";
		$result = $this->doQuery($query, $this->link);
		
		// Flush authors_unverified and reinsert
		$query = "DELETE FROM authors_unverified WHERE citation_id = $current_citation_id";
		$result = $this->doQuery($query, $this->link);
		
		$queryArray = array();
		
		for($i = 0; $i < 6; $i++)
		{
			// Insert "verified" authors into author_of
			if(empty($args_authors['author'.$i.'id'])) 	// Unverified authors
			{
				if(empty($args_authors['author'.$i.'ln']) && empty($args_authors['author'.$i.'fn'])) // RUTH 080210
				{
					// Do nothing since the author is empty. To be deleted completely (entry won't be added into authors_unverified).
				}
				else {
					// Build array to be used to insert data into authors_unverified.
					$queryArray[$i] = array($args_authors['author'.$i.'ln'], $args_authors['author'.$i.'fn']);
				}
			}
			else										// Verified authors
			{
				if(empty($args_authors['author'.$i.'ln']) && empty($args_authors['author'.$i.'fn'])) // RUTH 080210
				{
					// Do nothing since the author is empty. To prevent empty author row with empty fn and ln to be verified authors.
				}
				else 
				{
					$query = "INSERT INTO author_of (author_id, citation_id, position_num) VALUES ('".mysql_real_escape_string($args_authors['author'.$i.'id'])."','".$current_citation_id."','".($i+1)."')";
					$result = $this->doQuery($query, $this->link);
				}
			}
		}
		
		if (!empty($queryArray))
		{
			$query_keys = "";
			$query_values = "";
			foreach ($queryArray as $key => $val)
			{
				$query_keys .= ", author".($key)."ln, author".($key)."fn ";
				$query_values .= ", '".mysql_real_escape_string($val[0])."', '".mysql_real_escape_string($val[1])."' ";
			}
			$query = "INSERT INTO authors_unverified (citation_id".$query_keys.") VALUES ('".$current_citation_id."'".$query_values.")";
			$result = $this->doQuery($query, $this->link);
		}
		
		return $current_citation_id;
	}
	
	function updateSimilarToWhenSaving($current_citation_id, $submitter, $owner)
	{	
		$return_value = true;
		$citations_arr = $this->getCitation_byID($submitter, $owner, $current_citation_id);
		
		$single_citation = $citations_arr[0];
		
		// Year in citation is no longer an array. Parse is setup to only save the first year found.
		if(!empty($single_citation))
			$fuzzy_args = array("lastname" => $single_citation['author0ln'], "year" => $single_citation['year'], "title" => $single_citation['title']);
		else $fuzzy_args = array();
		
		if(!empty($fuzzy_args))
		{
			$ratios_array = $this->doFuzzyMatch($fuzzy_args, $current_citation_id);
			if(!empty($ratios_array)) // Do fuzzy match on current citation id.
			{
				if(!$this->updateSimilarToDB($ratios_array))	// Update similar_to DB
				{
					$return_value = false;   	// If updateSimilarToDB returns false, return false since there is an error.
												// If it's true, just let it be since there might already a previous error.
				}
			}
			else {} // No fuzzy match for current citation_id.
		}
		else $return_value = false;
		
		return $return_value;
	}
	
	function checkAuthors($args, $args_authors, $coll_id)
	{
		$this->link = $this->connectDB();
		$problemFlag = 0;
		$authorsArray = array();
		$all_authors_empty_count = 0;
			
		for ($i=0; $i<6; $i++)
		{
			$suggestions = array();
		
			$author_id = $args_authors['author'.$i.'id'];
			$lastname = trim($args_authors['author'.$i.'ln']);
			$firstname = trim($args_authors['author'.$i.'fn']);
			// try to find author_id of a real author
		//	if (($lastname != "") && ($firstname != ""))
			if ($lastname != "") // allow blank firstname
			{
				list($author_id, $suggestions) = $this->determineWhetherAuthorIsVerified($lastname, $firstname);
				if ($author_id == -1) //potential new author
				{
					$problemFlag = 1;
				}
				else if($author_id > 0) // Valid author_id is returned.
				{
					$args_authors['author'.$i.'id'] = $author_id;
				}
				else // -2 means author fn and ln are empty (code shouldn't get here).
				{
					$problemFlag = 1;
					$author_id = -2;
					$lastname = "Error";
					$firstname = "Error";
				}
			}
			else
			{
				$all_authors_empty_count++;
			}	
				
			$authorsArray[$i][0] = $author_id;
			$authorsArray[$i][1] = $lastname;
			$authorsArray[$i][2] = $firstname;
			$authorsArray[$i][3] = $suggestions;		
		}
		
		if($all_authors_empty_count == 6)
		{
			$authorsArray = array_fill(0, 6, array(-2,'','', array('','',''))); // Return all ids as -2		
			return $authorsArray;
		}
		else if ($problemFlag == 1) 
		{
			return $authorsArray;
		}
		else
		{		
			$new_or_current_id = $args['citation_id'];
			if(($new_or_current_id = $this->save($args, $args_authors, $coll_id)) == false)
			{
				$this->error .= 2;
				return false;
			}
			else
			{
				$result = $this->getCitation_byID($args['submitter'],$args['owner'],$new_or_current_id);
				return $result;
			}
		}
	} 
	
/*	function createNewAuthorsAndSave($args, $args_authors)
	{
		$this->link = $this->connectDB();
		
		for ($i=0; $i<6; $i++)
		{
			$author_id = $args_authors['author'.$i.'id'];
			$lastname = trim($args_authors['author'.$i.'ln']);
			$firstname = trim($args_authors['author'.$i.'fn']);
			
			
			// create a new author or get author_id
	//		if (($author_id != -2) && ($author_id != ""))
			if ($author_id == -1) //new author
				{
					$temp_author_id = $this->newVerifiedAuthor($firstname, $lastname, $args['submitter'], $args['owner']);
					if ($temp_author_id != false)
					{
						$author_id = $temp_author_id;
						$args_authors['author'.$i.'id'] = $author_id;
					}
					else
					{
						//error
					}
				}
			
			else if ($author_id == -2) // leave author unverified
			{
				$args_authors['author'.$i.'id'] = "";
			}	
			
			else // Author unchanged.  author id should exist and have been sent through by the javascript to this point.  Check anyway to verify author id.  If author id comes back as -1, treat as an unverified author.
			{
				list($author_id, $suggestions) = $this->determineWhetherAuthorIsVerified($lastname, $firstname);
				if ($author_id == -1) // leave author unverified
				{
					$args_authors['author'.$i.'id'] = "";
				}
				else {
					$args_authors['author'.$i.'id'] = $author_id;	
				}
			}

		}
		
		print_r($args_authors);
		$citation_id = $this->save($args, $args_authors);
		$result = $this->getCitation_byID($args['submitter'], $args['owner'],$citation_id);
		return $result;
	}*/
	
	function createNewAuthorsAndSave($args, $args_authors, $coll_id)
	{
		$this->link = $this->connectDB();
		
		for ($i=0; $i<6; $i++)
		{
			$author_id = $args_authors['author'.$i.'id'];
			$lastname = trim($args_authors['author'.$i.'ln']);
			$firstname = trim($args_authors['author'.$i.'fn']);
			
			
			// create a new author or get author_id
	//		if (($author_id != -2) && ($author_id != ""))
			if ($author_id == -2) // leave author unverified
			{
				$args_authors['author'.$i.'id'] = "";
			}	
			
			else // Author unchanged.  author id should exist and have been sent through by the javascript to this point.  Check anyway to verify author id.  If author id comes back as -1, treat as an unverified author.
			{
				list($author_id, $suggestions) = $this->determineWhetherAuthorIsVerified($lastname, $firstname);
				if ($author_id == -1) // new author
				{
					$temp_author_id = $this->newVerifiedAuthor($firstname, $lastname, $args['submitter'], $args['owner']);
					if ($temp_author_id != false)
					{
						$author_id = $temp_author_id;
						$args_authors['author'.$i.'id'] = $author_id;
					}
					else
					{
						//error
					}
				}
				else {
					$args_authors['author'.$i.'id'] = $author_id;	
				}
			}

		}
		
		$citation_id = $this->save($args, $args_authors, $coll_id);
		$result = $this->getCitation_byID($args['submitter'], $args['owner'],$citation_id);
		return $result;
	}
	
	function determineWhetherAuthorIsVerified($lastname, $firstname)
	{
		$firstname = trim($firstname);
		$lastname = trim($lastname);
	
		if(empty($lastname) && empty($firstname)) // Check if firstname and lastname are empty.
		{
			return array(-2, array());
		}
	
		$query = "SELECT * FROM authors WHERE lastname='".mysql_real_escape_string($lastname)."' AND firstname='".mysql_real_escape_string($firstname)."'";
		$result = $this->doQuery($query, $this->link);
		$suggestions = array();
				
		if (mysql_num_rows($result) > 0) 					// An author_id has been found
		{
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
			$author_id = $row['author_id'];		
		}
		else // No match - potential new author
		{	
			$author_id = -1;
			$query = "SELECT author_id, lastname, firstname FROM authors WHERE lastname='".mysql_real_escape_string($lastname)."'";
			$result = $this->doQuery($query, $this->link);
				
			while($row = mysql_fetch_assoc($result))
        	{
				$suggestions[] = array($row['author_id'], $row['lastname'],$row['firstname']);
			}
		}	
		return array($author_id, $suggestions);
	}
	
	function newVerifiedAuthor($firstname, $lastname, $submitter, $owner)
	{
		$this->link = $this->connectDB();
		
		$firstname = trim($firstname);
		$lastname = trim($lastname);
		
		$query = "SELECT * FROM authors WHERE firstname='".mysql_real_escape_string($firstname)."' AND lastname='".mysql_real_escape_string($lastname)."'";
		$result = $this->doQuery($query, $this->link);
		if(mysql_num_rows($result) > 0)
		{
			$this->error .= 2; 
			return false;
		}
		else {
			$query = "INSERT INTO authors (lastname, firstname) VALUES ('".mysql_real_escape_string($lastname)."', '".mysql_real_escape_string($firstname)."')";
			$result = $this->doQuery($query, $this->link);
		
			if (!$result) { 
				$this->error .= 2; 
				return false;
			}
			else { 
				return (int)mysql_insert_id(); // New author id
			}
		}
	}
	
	function insert_into_deleted_citations_db($citation_id, $reason, $submitter, $owner)
	{
		$this->link = $this->connectDB();
		
		$error_found = false;
		$deleted_citation_array = array();

		// Get authors info from authors + author_of)
		$query = "SELECT authors.author_id, lastname, firstname, position_num FROM ( authors INNER JOIN author_of ON authors.author_id = author_of.author_id ) WHERE author_of.citation_id = $citation_id ORDER BY position_num ASC";
		$result = $this->doQuery($query, $this->link);
		if(!$result) $error_found = true;
		if(mysql_num_rows($result) > 0)
		{
			$result_arr = array();
			while(($result_arr[] = mysql_fetch_assoc($result)) || array_pop($result_arr));  // Copy result into an array.
			for($i = 0; $i < 6; $i++)
			{
				foreach($result_arr as $author_row)
				{
					if(intval($author_row['position_num']) == ($i + 1)) 	// Position num starts from 1	
					{
						$deleted_citation_array['author'.$i.'ln'] = $author_row['lastname'];
						$deleted_citation_array['author'.$i.'fn'] =	$author_row['firstname'];
					}
				}
			}
		}
		
		// Get authors info from authors_unverified
		$query = "SELECT * FROM authors_unverified WHERE authors_unverified.citation_id = $citation_id";
		$result = $this->doQuery($query, $this->link);
		if(!$result) $error_found = true;
		if(mysql_num_rows($result) > 0)
		{
			$result_arr = array();
			while(($result_arr[] = mysql_fetch_assoc($result)) || array_pop($result_arr));  // Copy result into an array.
			for($i = 0; $i < 6; $i++)
			{				
				$deleted_citation_array['author'.$i.'ln'] = $result_arr[0]['author'.$i.'ln'];
				$deleted_citation_array['author'.$i.'fn'] =	$result_arr[0]['author'.$i.'fn'];					
			}
		}
		
		// Get citation info from citations
		$query = "SELECT * FROM citations WHERE citation_id='$citation_id'";
		$result = $this->doQuery($query, $this->link);
		if(!$result) $error_found = true;
		if(mysql_num_rows($result) > 0)
		{
			$result_arr = array();
			while(($result_arr[] = mysql_fetch_assoc($result)) || array_pop($result_arr));  	// Copy result into an array.
			$deleted_citation_array = array_merge($deleted_citation_array, $result_arr[0]); 	// Add first element (one row) into $deleted_citation_array.
		}
		else  // Citation doesn't exist. Possibly that it has been deleted before.
		{
			return false;
		}
		
		// Get collection info from collections and member_of_collection
		$query = "SELECT collection_id FROM member_of_collection moc WHERE moc.citation_id='$citation_id' ORDER BY collection_id ASC";  //need collection_name, user_id, submitter?
		$result = $this->doQuery($query, $this->link);
		if(!$result) $error_found = true;
		if(mysql_num_rows($result) > 0)
		{
			$result_arr = array();
			while($row = mysql_fetch_assoc($result))  // Copy result into an array.
			{
				$result_arr[] = $row['collection_id'];
			}
			$deleted_citation_array['collections'] = implode(",", $result_arr); 			// Comma separated
		}
		
		// Get similar info from similar_to
		$query = "SELECT * FROM (SELECT citation_id1 FROM similar_to WHERE citation_id2='$citation_id' UNION SELECT citation_id2 FROM similar_to WHERE citation_id1='$citation_id') AS t1 ORDER BY 1 ASC"; //need lastname_ratio, year_ratio, title_ration?
		$result = $this->doQuery($query, $this->link);
		if(!$result) $error_found = true;
		if(mysql_num_rows($result) > 0)
		{
			$result_arr = array();
			while($row = mysql_fetch_row($result))  // Copy result into an array
			{
				$result_arr[] = $row[0]; // First row.
			}
			$deleted_citation_array['similar_to'] = implode(",", $result_arr); 			// Comma separated
		}
		
		$deleted_citation_array['reason'] = $reason; 		// Reason for deleting
		$deleted_citation_array['deleted_timestamp'] = time(); 	// Deleted Timestamp
		
		// Write all to DB - deleted_citations_db
		$query = "INSERT INTO deleted_citations ";
		$key_str = "(";
		$value_str = "(";
		foreach($deleted_citation_array as $key => $value)
		{
			$key_str .= $key.",";
			$value_str .= "'".mysql_real_escape_string($value)."',";
		}
		$key_str = substr($key_str, 0, -1);			// Remove last comma
		$value_str = substr($value_str, 0, -1);		// Remove last comma
		$query .= $key_str.") VALUES ".$value_str.")";
		$result = $this->doQuery($query, $this->link);
		if (!$result) { 
			$this->error .= 2; 
			$error_found = true;
		}
		else { 
			$deleted_record_id = (int)mysql_insert_id(); // Deleted citation record id.
			//echo "DELETED RECORD ID: ".$deleted_record_id."<br />";
		}

		//print_r($deleted_citation_array);
		return (!$error_found);
	}
	
	function delete($citation_id, $reason, $submitter, $owner)
	{
		if($this->insert_into_deleted_citations_db($citation_id, $reason, $submitter, $owner))
		{
			$this->link = $this->connectDB();
			$error_found = false;
			
			$query = "DELETE FROM citations WHERE citation_id=$citation_id ";
			$result = $this->doQuery($query, $this->link);
			if(!$result) $error_found = true;
	
			$query = "DELETE FROM author_of WHERE citation_id=$citation_id "; 
			$result = $this->doQuery($query, $this->link);
			if(!$result) $error_found = true;
	
			$query = "DELETE FROM member_of_collection WHERE citation_id=$citation_id "; 
			$result = $this->doQuery($query, $this->link);
			if(!$result) $error_found = true;
			
			$query = "DELETE FROM authors_unverified WHERE citation_id=$citation_id "; 
			$result = $this->doQuery($query, $this->link);
			if(!$result) $error_found = true;
			
			$query = "DELETE FROM similar_to WHERE citation_id1=$citation_id OR citation_id2=$citation_id"; 
			$result = $this->doQuery($query, $this->link);
			if(!$result) $error_found = true;
			
			return (!$error_found);
		}
		else return false;		
	}
	
	function delete_collecitons_table($citation_id, $submitter, $owner)
	{
		$query = "DELETE FROM collections_table WHERE citation_id='$citation_id'";
		
		$result = $this->doQuery($query, $this->link);
		if(!$result)
		{
			$this->error .= 2;
			return false;
		}
		else 
		{
			return true;
		}
	}
	
	function getCitations_byTimestamp_all($entryTime)
	{
		$this->link = $this->connectDB();

		$query = "SELECT * FROM $this->table WHERE entryTime='".mysql_real_escape_string($entryTime)."' ORDER BY citation_id ASC $this->limit";
		
		$citations = $this->getJSON($query);

		
		// Get similar citations. Returns empty array if none.
		$similar_citations_array = $this->getSimilarCitations($citations);
		
		return array($citations, $similar_citations_array);
	}
	
	function getCitations_byTimestamp_unverified($entryTime)
	{
		$this->link = $this->connectDB();

		$query = "SELECT * FROM $this->table WHERE entryTime='".mysql_real_escape_string($entryTime)."'  AND verified='0' ORDER BY citation_id ASC $this->limit";
		
		$result_arr = $this->getJSON($query);
		$this->sortCitations($result_arr);
		return $result_arr;
	}
	
	function getCitations_byFac_unverified($submitter, $owner)
	{
		$this->link = $this->connectDB();

		$query = "SELECT * FROM $this->table WHERE owner='".mysql_real_escape_string($owner)."' AND verified='0' ORDER BY citation_id ASC $this->limit";
		
		$result_arr = $this->getJSON($query);
		$this->sortCitations($result_arr);
		return $result_arr;
	}
	
/*	function getCitationByID($citation_id)
	{
		$this->link = $this->connectDB();

		$query = "SELECT * FROM $this->table WHERE citation_id = '$citation_id' ORDER BY citation_id ASC $this->limit";
		
		$result_arr = $this->getJSON($query);
		return $result_arr;
	}*/
	
	function getCitations_byFac_all($submitter, $owner)
	{
		$this->link = $this->connectDB();

	//	$query = "SELECT * FROM $this->table WHERE submitter='".$submitter."' ORDER BY citation_id ASC $this->limit";
		$query = "SELECT * FROM $this->table ORDER BY citation_id ASC $this->limit";
		
		$result_arr = $this->getJSON($query);
	//	print_r($result_arr);
		$sorted_arr = $this->sortCitations($result_arr);
		return $sorted_arr;
	}
	
	function getCitations_byFac_one($citation_id)
	{
		$this->link = $this->connectDB();

		$query = "SELECT * FROM $this->table WHERE citation_id = '$citation_id' ORDER BY citation_id ASC $this->limit";
		
		$result_arr = $this->getJSON($query);
		$this->sortCitations($result_arr);
		return $result_arr;
	}
	
	
	function getCitation_byID($submitter, $owner, $citation_id)
	{
		$this->link = $this->connectDB();

		$query = "SELECT * FROM $this->table WHERE citation_id='".$citation_id."'";
		
		$result_arr = $this->getJSON($query);
		return $result_arr;
	}

	function getCitations_byIDs($submitter, $owner, $citation_id_array)
	{		
		$this->link = $this->connectDB();
				
		if (count($citation_id_array) > 0)
		{
			$query = "SELECT * FROM $this->table WHERE (citation_id = '".mysql_real_escape_string($citation_id_array[0])."'"; 
																							
			if (count($citation_id_array) > 1)
			{
				for ($i=1; $i<count($citation_id_array); $i++)
				{
					$query.= " OR citation_id = '".mysql_real_escape_string($citation_id_array[$i])."'";
				}
			}
			$query.= ")";
						
			$result_arr = $this->getJSON($query);
			$result_arr = $this->sortCitations($result_arr);
		}
		else
		{
			$result_arr = array();
		}
		return $result_arr;
	}
			
	function getJSON($query) {

		$citations = array();
	
		$result = $this->doQuery($query, $this->link);
		
		while($row = mysql_fetch_assoc($result))
        {
			$citation = array();
            $keys = array_keys($row);
            for ($i=0; $i < count($keys); $i++) {
				if($keys[$i] == 'author')
				{			
					$citation_id = 	$row['citation_id'];
					
					// Unverified authors
					$query_authors_unverified = "SELECT * FROM authors_unverified WHERE citation_id='$citation_id'";
					$result_authors_unverified = $this->doQuery($query_authors_unverified, $this->link);
					$row_au_unv = mysql_fetch_assoc($result_authors_unverified);
					
					for($pos_num = 0;$pos_num < 6; $pos_num++)
					{
						$citation['author'.$pos_num.'ln'] = (empty($row_au_unv['author'.$pos_num.'ln']))?"":$row_au_unv['author'.$pos_num.'ln'];
						$citation['author'.$pos_num.'fn'] = (empty($row_au_unv['author'.$pos_num.'fn']))?"":$row_au_unv['author'.$pos_num.'fn'];
						$citation['author'.$pos_num.'id'] = "";
					}
					
					// Verified authors
					$query_author = "SELECT DISTINCT a.*, ao.position_num FROM authors a, author_of ao, citations c 
										WHERE c.citation_id = '$citation_id' AND ao.citation_id = '$citation_id' AND a.author_id = ao.author_id ORDER BY ao.position_num";
					
					$result_author = $this->doQuery($query_author, $this->link);
			
					$pos_num = 0;
					while($row_author = mysql_fetch_array($result_author, MYSQL_ASSOC))
					{
						$pos_num = $row_author['position_num']-1;
						
						$keys_author = array_keys($row_author);
						$citation['author'.$pos_num.'id'] = trim($row_author[$keys_author[0]]);
						$citation['author'.$pos_num.'ln'] = trim($row_author[$keys_author[1]]);
						$citation['author'.$pos_num.'fn'] = trim($row_author[$keys_author[2]]);
					}
				}
				else
				{
				//	$citation[] = array($keys[$i] => trim($row[$keys[$i]]));
					$citation[$keys[$i]] = trim($row[$keys[$i]]);
				}
            }
			$citations[] = $citation;
        }
		
		mysql_free_result($result);
		return $citations;
	}
	
	function get_keywords_array($keyword, $type)
	{
		$keywords_array = array();
		if (!empty($keyword))
		{
			// Remove common words. "the", "an", "a"
			$replace_pattern = '/(\bthe\b|\ban\b|\ba\b|\bto\b|\s)/';
			$keyword = preg_replace($replace_pattern,' ',$keyword); // Replace with a space for word separation
			
			// Split the keywords on "," , "and" and " "
			$keywords_array_temp = array();
			$keyword = trim($keyword);
			$pattern = '/[\s]*([,]|\band\b|\s)[\s]*/';
			$matches = preg_split($pattern, $keyword);
				
			// Clear empty matches with empty spaces.
			foreach($matches as $match)  
			{
				if(!empty($match)) $keywords_array_temp[] = $match;
			}
			
			//print_r($keywords_array_temp);
			
			// No valid keyword.
			if (count($keywords_array_temp) == 0)
			{
				return array();	
			}
			
			// Combined keywords.
			$imploded = ""; // Add % for SQL wildcard.
			foreach($keywords_array_temp as $key)
			{
				$imploded .= mysql_real_escape_string(trim($key))."%";  // SQL safe
			}
			$imploded = substr($imploded, 0, -1);
			
			if (($type == 'title') || ($type == 'journal') || ($type == 'author'))
			{
				$keywords_array[] = array($type,$imploded);
			}
			else if ($type == 'all')
			{
				$keywords_array[] = array('title',$imploded);
				$keywords_array[] = array('journal',$imploded);
				$keywords_array[] = array('author',$imploded);
			}
			
			// Individualized keywords. Only when there's more than 1 keyword.				
			if (count($keywords_array_temp) > 1)
			{
				for ($i=0; $i <count($keywords_array_temp); $i++)
				{
					$key = mysql_real_escape_string(trim($keywords_array_temp[$i]));  // SQL safe
					
					if (($type == 'title') || ($type == 'journal') || ($type == 'author'))
					{
						$keywords_array[] = array($type, $key);
					}
					else if ($type == 'all')
					{
						$keywords_array[] = array('title', $key);
						$keywords_array[] = array('journal', $key);
						$keywords_array[] = array('author', $key);
					}	
				}
			}
		}

		//print_r($keywords_array);
		return $keywords_array;
	}
	
	function get_search_keywords($keyword, $type)
	{
		if (!empty($keyword))
		{
			$keyword_str = "";
			
			$keyword_str = explode(" ",$keyword);
			
			return array($keyword_str, $type);	
		}
		else
		{
			return array("",$type);	
		}
	}
		
	function get_current_max_page($total_count, $citations_per_page)
	{
		// Check for last page deletion.
		$full_page = intVal($total_count / $citations_per_page);
		$extra_page = $total_count % $citations_per_page;
		
		if($full_page == 0 && $extra_page == 0) $max_page = 1; 	// Lowest page is 1
		else if($extra_page == 0) $max_page = $full_page;		// No extra citations for extra page 
		else $max_page = ($full_page + 1);						// Extra citations, so add one more page	
		
		return $max_page;
	}
	
	function get_current_page($counter, $citations_per_page)
	{
		// Use the same function / same algorithm / same result
		return $this->get_current_max_page($counter, $citations_per_page);
	}
		
	function get_citations_JSON($type, $page, $submitter, $owner, $citations_per_page, $keyword, $sort_order, $collection_id = 0, $citation_id = 0) 
	{		
		$this->link = $this->connectDB();
		
		$limit = $this->get_limit($page, $citations_per_page);
		$verified = true;
		
		// Set unverified
		if ($type == "getCitations_byFac_unverified") {
			$verified = false;
		}
		else if ($type == 'title' || $type == 'journal' || $type == 'author' || $type == 'all')
		{
			if(empty($keyword))	return array(0, array(), array());
		}
		else if($type == 'getCitations_byFac_all') // Prevents empty owner or submitter entries from showing up on guest page.
		{
			if(empty($submitter) || empty($owner))	return array(0, array(), array());	
		}
		
		// Get page if query is using citation_id		
		if($citation_id != 0)
		{
			$returned_array = $this->get_citations_JSON_query($limit, $verified, $keyword, "citation_id", $sort_order, $collection_id, $submitter, $owner, $citation_id, $page,$citations_per_page);
			$result = $returned_array[0];
			$counter = $returned_array[1];
			
			// Reset $page and recalculate $limit
			$page = $this->get_current_page($counter, $citations_per_page);
			$limit = $this->get_limit($page, $citations_per_page);
		}

		// Get query result	by type
		$returned_array = $this->get_citations_JSON_query($limit, $verified, $keyword, $type, $sort_order, $collection_id, $submitter, $owner, $citation_id, $page, $citations_per_page);
		$result = $returned_array[0];
		$total_count = $returned_array[1];
			
		// Get maximum page for results.
		$max_page = $this->get_current_max_page($total_count, $citations_per_page);
		
		if($page > $max_page)
		{
			// Get query result	by type again with new limit
			$limit = $this->get_limit($max_page, $citations_per_page);
			$returned_array = $this->get_citations_JSON_query($limit, $verified, $keyword, $type, $sort_order, $collection_id, $submitter, $owner, $citation_id, $page, $citations_per_page);
			$result = $returned_array[0];
			$total_count = $returned_array[1];	
		}
			
		// Return empty array if result is false.	
		if(empty($result)) {
			return array(0, array(), array(), 1); 
		}	
		else {	// Get citations
			$citations = $result;
		}
		
		// Get similar citations
		$similar_citations_array = $this->getSimilarCitations($citations);
		
		return array($total_count, $citations, $similar_citations_array, $page);
	}
	
	function getSimilarCitations($citations)
	{
		$similar_citations_array = array();
		foreach ($citations as $one_citation)
		{
			$similar_citations = $this->determineWhetherSimilarCitationsExist($one_citation['citation_id']);
			{
				if (count($similar_citations) > 0)
				{
					$temp_key = "".$one_citation['citation_id'];
					$similar_citations_array[$temp_key] = $similar_citations;
				}
			}
		}
		
		return $similar_citations_array;
	}
	
	// Accept sorted sql result
	function getJSON_by_result($result)
	{
		$citations = array();
		
		while($row = mysql_fetch_assoc($result))
        {
			$citation = array();
            
			$keys = array_keys($row);
			foreach($keys as $key)
			{
				$citation[$key] = trim($row[$key]);
			}
			$citations[] = $citation;
		}
		
		return $citations;
	}
	
	// Accept sorted sql result
	function getJSON_by_result_multi($result)
	{
		$citations = array();
		
		while($row = mysqli_fetch_assoc($result))
        {
			$citation = array();
            
			$keys = array_keys($row);
			
			foreach($keys as $key)
			{
				$citation[$key] = trim($row[$key]);
			}
			$citations[] = $citation;
		}
		
		//print_r($citations);
		
		return $citations;
	}
	
	function build_authors_table()
	{
		// Build authors and authors_unverified left join query.
		$q_left_join = "";	
		for($i = 0; $i < 6; $i++)  // Left join all the verified authors. (Change column names as verified0ln etc.).
		{
			$q_left_join .= "LEFT JOIN ( SELECT ao.citation_id, a.lastname AS verified".$i."ln, a.firstname AS verified".$i."fn FROM author_of ao, authors a WHERE ao.position_num =".($i+1)." AND a.author_id = ao.author_id ) a".$i." ON a".$i.".citation_id = c.citation_id "; // position_num starts at 1 (therefore $i + 1).
		}
		
		$q_left_join .= "LEFT JOIN authors_unverified au ON c.citation_id = au.citation_id ";  // Left join all unverified authors	
		
		return $q_left_join;
	}
	
	function build_search_match_clause($keyword, $type)
	{
		$author_clause = "mt.author0ln, mt.author0fn, mt.author1ln, mt.author1fn, mt.author2ln, mt.author2fn, mt.author3ln, mt.author3fn, mt.author4ln, mt.author4fn, mt.author5ln, mt.author5fn";

		if($type == "all") {
			$search_query = " MATCH ($author_clause, title, journal) ";
		}
		else if($type == "author") {
			$search_query = " MATCH ($author_clause) ";
		}
		else if($type == "title") {
			$search_query = " MATCH (title) ";
		}
		else if($type == "journal") {
			$search_query = " MATCH (journal) ";
		}
		else {
			$search_query = "";
			return "";
		}
	
		$search_query .= "AGAINST('".mysql_real_escape_string($keyword)."' IN BOOLEAN MODE)";
		return $search_query;
	}
	
	// getCitationsGivenCollectionID
	function get_citations_JSON_collections_table($page, $submitter, $owner, $citations_per_page, $sort_order, $collection_id)
	{
		$this->link = $this->connectDB();
		
		$limit = $this->get_limit($page, $citations_per_page);
		
		// Get page if query is using citation_id		
		/*if($citation_id != 0)
		{
			$returned_array = $this->get_citations_JSON_query($limit, $verified, $keyword, "citation_id", $sort_order, $collection_id, $submitter, $owner, $citation_id, $page,$citations_per_page);
			$result = $returned_array[0];
			$counter = $returned_array[1];
			
			// Reset $page and recalculate $limit
			$page = $this->get_current_page($counter, $citations_per_page);
			$limit = $this->get_limit($page, $citations_per_page);
		}*/

		// Get query result	by type
		$returned_array = $this->get_citations_JSON_collections_table_query($sort_order, $collection_id, $submitter, $owner, $page, $citations_per_page);
		$result = $returned_array[0];
		$total_count = $returned_array[1];
			
		// Get maximum page for results.
		$max_page = $this->get_current_max_page($total_count, $citations_per_page);
		
		if($page > $max_page)
		{
			// Get query result	by type again with new limit
			$limit = $this->get_limit($max_page, $citations_per_page);
			$returned_array = $this->get_citations_JSON_collections_table_query($sort_order, $collection_id, $submitter, $owner, $page, $citations_per_page);
			$result = $returned_array[0];
			$total_count = $returned_array[1];	
		}
			
		// Return empty array if result is false.	
		if(empty($result)) {
			return array(0, array(), array(), 1); 
		}	
		else {	// Get citations
			$citations = $result;
		}
		
		// Get similar citations
		$similar_citations_array = $this->getSimilarCitations($citations);
		
		return array($total_count, $citations, $similar_citations_array, $page);
	}
	
	function get_citations_JSON_collections_table_query($sort_order, $collection_id, $submitter, $owner, $page, $citations_per_page)
	{
		$this->link = $this->connectDB();
		
		#SQL Safe Processing.
		$collection_id = mysql_real_escape_string($collection_id); 
		$submitter = mysql_real_escape_string($submitter); 
		$owner = mysql_real_escape_string($owner);
		
		#Set order and limit
		$query_ORDER = $this->write_query_order($sort_order);	
		$limit = $this->get_limit($page, $citations_per_page);
		
		#Queries
		if ($collection_id == "all")
		{
			#$query = "SELECT DISTINCT SQL_CALC_FOUND_ROWS collections_table.* FROM collections_table, collections WHERE collections.owner = '".$owner."' AND collections_table.coll_id = collections.collection_id ".$query_ORDER." ".$limit."; ";
		//	$query = "SELECT SQL_NO_CACHE DISTINCT ct.author0fn, ct.author0ln, ct.author1fn, ct.author1ln, ct.author2fn, ct.author2ln, ct.author3fn, ct.author3ln, ct.author4fn, ct.author4ln, ct.author5fn, ct.author5ln, ct.citation_id, ct.user_id, ct.pubtype, ct.cit_key, ct.abstract, ct.keywords, ct.doi, ct.url, ct.address, ct.annote, ct.author, ct.booktitle, ct.chapter, ct.crossref, ct.edition, ct.editor, ct.translator, ct.howpublished, ct.institution, ct.journal, ct.bibtex_key, ct.month , ct.note, ct.number, ct.organization, ct.pages, ct.publisher, ct.location, ct.school, ct.series, ct.title, ct.type , ct.volume, ct.year, ct.raw, ct.verified, ct.format, ct.filename, ct.submitter, ct.owner, ct.entryTime, ct.last_modified, ct.date_retrieved FROM collections_table ct, collections WHERE collections.owner = '".$owner."' AND ct.coll_id = collections.collection_id ".$query_ORDER." ".$limit.";";
			
			// Set some MySQL System Variables
			//$this->doQuery("SET sort_buffer_size=2097144;", $this->link);
			//$this->doQuery("SET max_length_for_sort_data=128;", $this->link);
			//$this->doQuery("SET read_rnd_buffer_size=2097144;", $this->link);
			
			// Slow Query!!
			//$query = "SELECT SQL_CALC_FOUND_ROWS ct.* FROM collections_table ct, collections WHERE collections.owner = '".$owner."' AND ct.coll_id = collections.collection_id GROUP BY ct.citation_id ".$query_ORDER." ".$limit.";";
			
			// Disable ORDER BY for temporary fix
			$query = "SELECT SQL_CALC_FOUND_ROWS ct.* FROM collections_table ct, collections WHERE collections.owner = '".$owner."' AND ct.coll_id = collections.collection_id ".$limit.";"; //" GROUP BY ct.citation_id .$query_ORDER."
			
			// New faster query
			//$query = "SELECT SQL_CALC_FOUND_ROWS ct.* FROM collections_table ct, collections FORCE INDEX (owner10) WHERE collections.owner = '".$owner."' AND ct.coll_id = collections.collection_id ".$query_ORDER." ".$limit.";"
			
			//	print_r($query);
		}
		else
		{
			$query = "SELECT SQL_CALC_FOUND_ROWS * FROM collections_table WHERE coll_id = $collection_id ".$query_ORDER." ".$limit."; ";
		}
		$query_total_count = "SELECT FOUND_ROWS(); ";
		
		//echo $query;
		Logger::instance()->clear();
		Logger::instance()->log($query);
		
		#Initialize citations array and total count.
		$total_count = 0;
		$citations = array(); // Return empty array if result is false.	

		#Get all citations plus all authors sorted from temporary authors table.
		$result = $this->doQuery($query, $this->link);
	
		#Get total count.
		if($result) 
		{	
			$citations = $this->getJSON_by_result($result); 				// Grab result and save it in an array.
			$count_result = $this->doQuery($query_total_count, $this->link);
			$count = mysql_fetch_array($count_result);
			$total_count = $count[0];
		}
		else $return_val = false;
		
		if(!empty($citations)) {
			$return_val = array($citations, $total_count);
		}
		else { 
			$return_val = false;
		}
		
		return $return_val;
	}
	
	function get_citations_JSON_query($limit, $verified, $keyword, $type, $sort_order, $collection_id, $submitter, $owner, $citation_id = 0, $page = 0, $citations_per_page = 0)
	{
		//var_dump(debug_backtrace());
		
		//Logger::instance()->log('message');
		
		//$this->link = $this->connectDB_multi();
		$this->link = $this->connectDB();
		
		//print_r($keywords_array);	
		
		#SQL Safe Processing.
		$collection_id = mysql_real_escape_string($collection_id); 
		$submitter = mysql_real_escape_string($submitter); 
		$owner = mysql_real_escape_string($owner);

		#Unverified citations conditional
		$unverified_condition_str = ($verified) ? "" : "verified = 0";
		
		#Search conditionals
		$search_query = $this->build_search_match_clause($keyword, $type);

		#Main query WHERE part.
		if(!empty($unverified_condition_str)) // Unverified
		{
			$temp_WHERE = $unverified_condition_str;
			if(!empty($search_query)) $temp_WHERE .= " AND ".$search_query." ";
			else {}
		}
		else								 // Verified
		{
			$temp_WHERE = "";
			if(!empty($search_query)) $temp_WHERE .= $search_query." ";
			else {} //$temp_WHERE .= "";
		}
		
		if($collection_id != 0) { // Collection id condition exists
			$collection_str = "moc.collection_id = '".$collection_id."' AND moc.citation_id = mt.citation_id";
		}
		else {
			$collection_str = "";
		}

		if(trim($temp_WHERE) != "" && $collection_str != "") {
			$temp_WHERE .= " AND ".$collection_str." ";	
		}
		else {
			$temp_WHERE .= "".$collection_str." ";
		}
		
		// This query should be copied to [Collections.class.php]->getDefaultCollectionNamesAndIds()
		$query_in = "citation_id IN (SELECT moc.citation_id FROM member_of_collection moc, collections col WHERE moc.collection_id = col.collection_id AND col.owner = '".$owner."') ";

		// Set 'owner' condition on "all" or "unverified". Do not set 'owner' when doing searches!
		if(empty($search_query))
		{
			if ($collection_id == 0) 
			{
				if(trim($temp_WHERE) != "" && $owner != "") {
					$temp_WHERE .= " AND (owner='".$owner."' OR ".$query_in.") ";
				}
				else {
					$temp_WHERE .= "(owner='".$owner."' OR ".$query_in.") ";
					
				}
			}
		}
			
		if(trim($temp_WHERE) == "") $query_WHERE = "";  // is the only string left.
		else $query_WHERE = "WHERE ".$temp_WHERE;
		

		#Main query ORDER BY.
		if ($type == 'title' || $type == 'journal' || $type == 'author' || $type == 'all')
		{
			$query_ORDER = "ORDER BY relevance DESC";
		}
		else
		{
			$query_ORDER = $this->write_query_order($sort_order);
		}	
		
		#Main query SELECT.
		$query_SELECT = "SELECT "; 
		for($i = 0; $i < 6; $i++)
		{
			$query_SELECT .= "IFNULL(verified".$i."ln,author".$i."ln) AS author".$i."ln, ";
			$query_SELECT .= "IFNULL(verified".$i."fn,author".$i."fn) AS author".$i."fn, ";
		}
		
		$query_SELECT .= "'' AS coll_name, '' AS coll_id, c.* "; // Select all citations columns.
		
		//$query_SELECT .= " c.* "; // Select all citations columns.
		
		#Main Table query
		$main_table_query_FROM = "FROM ( citations c ".$this->build_authors_table()." ) ";
		$main_table = "(".$query_SELECT." ".$main_table_query_FROM.") AS mt"; // main_table
		
		#Main query FROM.
		if($collection_id == 0) { // Not selecting collections
			$query_FROM = "FROM ".$main_table;
		}
		else {
			$query_FROM = "FROM ".$main_table.", member_of_collection moc";
		}
			
		#Main query.
		//$query = "SELECT SQL_CALC_FOUND_ROWS mtt.* FROM (";
		$query = ""; 
		if ($type == 'title' || $type == 'journal' || $type == 'author' || $type == 'all')
		{
			$query = "SELECT SQL_CALC_FOUND_ROWS ".$search_query." AS relevance, mt.* ".$query_FROM." ".$query_WHERE." ".$query_ORDER." ".$limit.";";
		}
		else if($type == 'citation_id')
		{
			$query_CITATION_ID = "AND mt.citation_id = $citation_id";
			$query = "SELECT SQL_CALC_FOUND_ROWS mt.* ".$query_FROM." ".$query_WHERE." ".$query_CITATION_ID." ".$query_ORDER." ".$limit."; ";
		}
/*		else if($collection_id != 0) 
		{
			$query = "SELECT SQL_CALC_FOUND_ROWS * FROM collections_table WHERE coll_id = $collection_id ".$query_ORDER." ".$limit."; ";
		}*/
		else {
			$query = "SELECT SQL_CALC_FOUND_ROWS mt.* ".$query_FROM." ".$query_WHERE." ".$query_ORDER." ".$limit."; ";
		}
		
		#SQL User Defined Variable
		$user_defined_var = "";
		
		#Get total count.
		$query_total_count = "SELECT FOUND_ROWS(); ";
		
		//DEBUG
		//$this->debug['query'] = $query;
		//print_r($query);		
		
		#Initialize citations array and total count.
		$total_count = 0;
		$citations = array(); // Return empty array if result is false.	
		
		//echo "<br />$query<br /><br />";
		//$start = microtime();
	
		#Get all citations plus all authors sorted from temporary authors table.
		$result = $this->doQuery($query, $this->link);
	
		#Get total count.
		if($result) 
		{	
			$citations = $this->getJSON_by_result($result); 				// Grab result and save it in an array.
			$count_result = $this->doQuery($query_total_count, $this->link);
			$count = mysql_fetch_array($count_result);
			$total_count = $count[0];
			//$return_val = array($citations, $total_count);
		}
		else $return_val = false;
		
//		#Get all citations plus all authors sorted from temporary authors table.		
//		if(mysqli_multi_query($this->link, $query)) 
//		{
//			mysqli_next_result($this->link);  // Skip variable initialization result
//			if($result = mysqli_use_result($this->link)) 
//			{
//				$citations = $this->getJSON_by_result_multi($result);
//			}
//			mysqli_next_result($this->link);  // Get next result (total count)
//			if($result2 = mysqli_use_result($this->link)) {
//				$count = mysqli_fetch_row($result2);
//				$total_count = $count[0];
//			}
//		}
		
		//$end = microtime();
		//echo "Time: ".($end-$start)." ";	
		//echo "<br />$query<br /><br />";
		
		if(!empty($citations)) 
		{
			//for($i = 0; $i < sizeof($citations); $i++)
			//{
				//$coll_result = $this->get_collections_by_citation_id($citations[$i]['citation_id'], $owner);
				//$citations[$i]['coll_id'] = $coll_result[0];
				//$citations[$i]['coll_name'] = $coll_result[1];
			//}
			
			$return_val = array($citations, $total_count);
		}
		else $return_val = false;
		
		return $return_val;
	}
	
	function get_collections_by_citation_id($citation_id, $owner)
	{
		$this->link = $this->connectDB();
		
		$query = "SELECT moc.citation_id, col.collection_name, col.collection_id FROM member_of_collection moc, collections col WHERE moc.collection_id = col.collection_id AND col.owner = '$owner' AND moc.citation_id = $citation_id ORDER BY col.collection_name";		
		
		$result = $this->doQuery($query, $this->link);
		
		$coll_name_str = "";
		$coll_id_str = "";
		
		while($row = mysql_fetch_assoc($result))
        {
			$coll_name_str .= $row['collection_name']."; ";
			$coll_id_str .= $row['collection_id']."; ";
		}
		
		return array($coll_id_str, $coll_name_str);
	}
	
	function write_query_order($sort_order)
	{
		//$author_str = "author0ln, author0fn, author1ln, author1fn, author2ln, author2fn, author3ln, author3fn, author4ln, author4fn, author5ln, author5fn";
		
		$author_str = "author0ln";
		
		$query_ORDER = '';
		
		if ($sort_order == 'year_asc')
		{
			$query_ORDER = "ORDER BY year, ".$author_str.", title";
		}
		else if ($sort_order == 'year_desc')
		{
			$query_ORDER = "ORDER BY year DESC, ".$author_str.", title";
		}
		else if ($sort_order == 'author0ln')
		{
			$query_ORDER = "ORDER BY ".$author_str.", year, title";
		}
		
		return $query_ORDER;
	}
	
	function get_limit($page, $citations_per_page)
	{
		$this->citations_per_page = $citations_per_page;
		$start_citation = ($page - 1) * $this->citations_per_page;
		return "LIMIT ".mysql_real_escape_string($start_citation).", ".mysql_real_escape_string($this->citations_per_page);	
	}
	
	function authorNameSplitter($author_names)
	{
		$splitted_author_names = array();
		foreach($author_names as $author){
			$nameSplit = explode(',', $author, 2);  // Split first name and last name
			$lastname = $nameSplit[0];
			if(sizeof($nameSplit) < 2){   			// Name without comma or comma at the end     
				$firstname = "";
			}
			else{
				$firstname = $nameSplit[1];
			}
			
			// Remove comma and/or spaces at the end of firstname
			$firstname = preg_replace('/\s*[,]*$/', "", $firstname);
			
			// Remove extra spaces
			$firstname = trim($firstname);
			$lastname = trim($lastname);
			$splitted_author_names[] = array("fn" => $firstname, "ln" => $lastname);
		}
		return $splitted_author_names;
	}
	
	function sortCitations($citations)
	{	
		//usort($citations, array($this, 'compare_fullname'));
		usort($citations, array($this, 'compare_by_elements'));
		return($citations);	
	}
	
	// Sort alphabetically by elements in $map array.
	function compare_by_elements($a, $b)
	{
		$map = array('author0ln','author0fn','author1ln','author1fn','author2ln','author2fn','author3ln','author3fn','author4ln','author4fn','author5ln','author5fn','year','title');		
		foreach($map as $key)
		{
			$retval = strnatcmp($a[$key], $b[$key]);
			if($retval == 0) // If the values are the same, compare the next element.
			{
				// Skip. Keep on comparing the next element.
			}
			else
			{
				return $retval;
			}
		}
		return $retval;  // Will return here if all elements match.
	} 
	
	/*
	function compare_fullname($a, $b) { 
		$retval = strnatcmp($a['author0ln'], $b['author0ln']); 
		if($retval==0) return strnatcmp($a['author0fn'], $b['author0fn']); 
		return $retval; 
	}
		
	function compare_lastname($a, $b) { return strnatcmp($a['lastname'], $b['lastname']); } // Sort alphabetically by name 
	*/
	
	function getUserIdByUsername($username)
	{
		$this->link = $this->connectDB();
		$query = "SELECT id FROM users WHERE username='".mysql_real_escape_string($username)."'";
		$result = $this->doQuery($query, $this->link);
		if(!$result) {
			return false;
		}
		else {
			if(mysql_num_rows($result) == 1)
			{
				$row = mysql_fetch_assoc($result);
				return $row['id'];
			}
			else
			{
				return false;
			}
		}
	}
		
	function determineWhetherSimilarCitationsExist($citation_id)
	{
		$this->link = $this->connectDB();
		$similar_citation_ids = array();
		$similar_citations = array();
		$query = "SELECT citation_id1, citation_id2 FROM similar_to WHERE citation_id1='".$citation_id."' OR citation_id2='".$citation_id."'";
		$result = $this->doQuery($query, $this->link);
		if(!$result) {
			return false;
		}
		else {
			if(mysql_num_rows($result) > 0)
			{		
				while($row = mysql_fetch_assoc($result))
				{
					if ($row['citation_id1'] == $row['citation_id2'])
					{
						//skip ... shouldn't be there	
					}
					else if ($citation_id == $row['citation_id1'])
					{
						$similar_citation_ids[]	= $row['citation_id2'];
					}
					else if ($citation_id == $row['citation_id2'])
					{
						$similar_citation_ids[]	= $row['citation_id1'];
					}
				}
				$similar_citations = $this->getCitations_byIDs('', '', $similar_citation_ids);
				return $similar_citations;
			}
			else
			{
				return $similar_citations;
			}
		}
	}
	
	function updateSimilarToByTimestamp($timestamp)
	{
		$return_value = true;
		$cit_by_timestamp = $this->getCitations_byTimestamp_all($timestamp);
		
		$citations = $cit_by_timestamp[0]; // All the citations
		$similars = $cit_by_timestamp[1]; // All the similar citations for above citations. array['citation_id'] ( array("similar_citations") )
		
		foreach($citations as $cit)
		{
			// Year in citation is no longer an array. Parse is setup to only save the first year found.
			$fuzzy_args = array("lastname" => $cit['author0ln'], "year" => $cit['year'], "title" => $cit['title']);
				
			if(!empty($fuzzy_args))
			{
				$ratios_array = $this->doFuzzyMatch($fuzzy_args, $cit['citation_id']);
				if(!empty($ratios_array)) // Do fuzzy match on current citation id.
				{
					if(!$this->updateSimilarToDB($ratios_array))	// Update similar_to DB
					{
						$return_value = false;   	// If updateSimilarToDB returns false, return false since there is an error.
													// If it's true, just let it be since there might already a previous error.
					}
				}
				else {} // No fuzzy match for current citation_id. 
			}
			else $return_value = false;
		}
		
		return $return_value;
	}
	
	function doFuzzyMatch($fuzzy_args, $citation_id)
	{
		$this->link = $this->connectDB();

		$query = "SELECT c.citation_id, a.lastname, c.year, c.title FROM citations c, authors a, author_of ao WHERE a.author_id = ao.author_id and c.citation_id = ao.citation_id AND ao.position_num = 1 UNION SELECT c.citation_id, au.author0ln, c.year, c.title FROM citations c, authors_unverified au WHERE c.citation_id = au.citation_id";
		
		$result = $this->doQuery($query, $this->link);
		$ratios_array = array();
				
		if (mysql_num_rows($result) > 0){  
			while ($row = mysql_fetch_assoc($result))
			{	
				if ($citation_id != $row['citation_id'])
				{	
					// Do Fuzzy Match
					if (($ratio = fuzzy_match($fuzzy_args['lastname'], $row['lastname'], 2)) > FUZZY_MATCH_RATIO)
					{
						$lastname_ratio = $ratio;
						if (($ratio = fuzzy_match($fuzzy_args['year'], $row['year'], 1)) > FUZZY_MATCH_RATIO)
						{
							$year_ratio = $ratio;
							if (($ratio = fuzzy_match($fuzzy_args['title'], $row['title'], 1)) > FUZZY_MATCH_RATIO)
							{
								$title_ratio = $ratio;
								
								// Assign citation_id2 since all 3 criterias match.
								$citation_id2 = $row['citation_id'];  
								
								// Save all ratios in array. To be written into DB later.
								$ratios_array[] = array("citation_id1" => $citation_id, "citation_id2" => $citation_id2, "lastname_ratio" => $lastname_ratio, "year_ratio" => $year_ratio, "title_ratio" => $title_ratio);
							}
						}
					}		
				}
				else {} // Same citation_id. Do not fuzzy match.
			}
		}	
		
		return $ratios_array;   // Either empty array or ratios_array.
	}
	
	function updateSimilarToDB($ratios_array)
	{
		$this->link = $this->connectDB();
	
		// Insert or Delete duplicates in similar_to
		foreach($ratios_array as $ratios)
		{
			$citation_id1 = $ratios['citation_id1'];
			$citation_id2 = $ratios['citation_id2'];
		
			// Check if citation_id1 and citation_id2 pair exists.
			$query = "SELECT * FROM similar_to WHERE (citation_id1 = '$citation_id1' AND citation_id2 = '$citation_id2') OR (citation_id1 = '$citation_id2' AND citation_id2 = '$citation_id1'); ";
			$result = $this->doQuery($query, $this->link);
			
			// Should only have one value since similar_to's citation_id1 and citation_id2 are reflexive.
			if (mysql_num_rows($result) > 1)
			{  
				$count = 0;
				
				// Should delete all other pairs.
				while ($row = mysql_fetch_assoc($result))
				{	
					if($count > 0) // Skip first pair.
					{
						$query2 = "DELETE FROM similar_to WHERE id = '".$row['id']."'; ";
						$result2 = $this->doQuery($query2, $this->link);
					}
					$count++;
				}
			}

			else if(mysql_num_rows($result) == 1)   // Skip since only one value exist
			{
				
			}
			else // No match. mysql_num_rows = 0 or less.
			{
				$query2 = "INSERT INTO similar_to (citation_id1, citation_id2, lastname_ratio, year_ratio, title_ratio) VALUES ('".$ratios['citation_id1']."','".$ratios['citation_id2']."','".$ratios['lastname_ratio']."','".$ratios['year_ratio']."','".$ratios['title_ratio']."')";
				$result2 = $this->doQuery($query2, $this->link);
			}
		}
		
		
		// No longer similar?
		// Get all similar_to rows with either citation_id1 (Primary citation_id that is being saved/edit/update).
		// All reflexive rows with citation_id1 in it.		
		$citation_id1 = $ratios_array[0]['citation_id1']; // Primary citation_id.
		$query = "SELECT * FROM similar_to WHERE citation_id1 = '$citation_id1' OR citation_id2 = '$citation_id1'; ";
		$result = $this->doQuery($query, $this->link);

		// Compare with $ratios_array for missing values in queried table (means that the missing reflexive row(s) are no longer similar to citationd_id1)
		if (mysql_num_rows($result) > 0)
		{  		
			// Should delete missing row with citation_id2 that does not exist in $ratios_array 
			while ($row = mysql_fetch_assoc($result))
			{	
				$temp[] = $row;
				
				// Search in ratios_array
				$found = false;
				foreach($ratios_array as $ratios)
				{	
					$citation_id1 = $ratios['citation_id1'];
					$citation_id2 = $ratios['citation_id2'];
				
					if($citation_id2 == $row['citation_id2']) {	
						$found = true; // Still similar
					}
					else if($citation_id2 == $row['citation_id1']) {
						$found = true; // Still similar
					}
					else {
						// Would default to false if not found.
					}
				}
				
				if(!$found)
				{
					// No longer similar since citation_id2 does not exist in ratios_array. (Delete row).
					$query2 = "DELETE FROM similar_to WHERE id = '".$row['id']."'; ";
					$result2 = $this->doQuery($query2, $this->link);
				}
			}
		}
		
		return true;
	}
	
	function populateSimilarTo()
	{
		$this->link = $this->connectDB();
		$return_value = true;
		
		$query = "SELECT citation_id FROM citations ORDER BY citation_ID ASC";
		$result = $this->doQuery($query, $this->link);  
		$citation_ids = array(); 
	
		$query = "SELECT c.citation_id, a.lastname, c.year, c.title FROM citations c, authors a, author_of ao WHERE a.author_id = ao.author_id and c.citation_id = ao.citation_id AND ao.position_num = 1 UNION SELECT c.citation_id, au.author0ln, c.year, c.title FROM citations c, authors_unverified au WHERE c.citation_id = au.citation_id";
		
		$result2 = $this->doQuery($query, $this->link);
		while($row2 = mysql_fetch_assoc($result2)) { $citations[] = $row2; }
		
		while($row = mysql_fetch_assoc($result)) { $citation_ids[] = $row['citation_id']; }
		
		foreach($citation_ids as $citation_id)
		{
			print $citation_id."\n";
			
			$cit_zero = $this->getCitation_byID2($citation_id);
			$cit = $cit_zero[0];
			
			// Year in citation is no longer an array. Parse is setup to only save the first year found.
			$fuzzy_args = array("lastname" => $cit['author0ln'], "year" => $cit['year'], "title" => $cit['title']);
					
			if (!empty($fuzzy_args))
			{
				$ratios_array = $this->doFuzzyMatch2($fuzzy_args, $cit['citation_id'], $citations);
				if(!empty($ratios_array)) // Do fuzzy match on current citation id.
				{
					if(!$this->updateSimilarToDB2($ratios_array))	// Update similar_to DB
					{
						$return_value = false;   	// If updateSimilarToDB returns false, return false since there is an error.
													// If it's true, just let it be since there might already a previous error.
					}
				}
				else {} // No fuzzy match for current citation_id. 
			}
			else $return_value = false;
			
		}
		
		return $return_value;
	}
	
	function doFuzzyMatch2($fuzzy_args, $citation_id, $citations)
	{
		$ratios_array = array();
		$ctr = 0;
		if (count($citations) > 0)
		{  
			while ($ctr < count($citations))
			{	
				$row = $citations[$ctr];
				
				if ($citation_id < $row['citation_id'])
				{					
					// Do Fuzzy Match
					if (($ratio = fuzzy_match($fuzzy_args['lastname'], $row['lastname'], 2)) > FUZZY_MATCH_RATIO)
					{						
						$lastname_ratio = $ratio;
						if (($ratio = fuzzy_match($fuzzy_args['year'], $row['year'], 1)) > FUZZY_MATCH_RATIO)
						{
							$year_ratio = $ratio;
							if (($ratio = fuzzy_match($fuzzy_args['title'], $row['title'], 1)) > FUZZY_MATCH_RATIO)
							{
								$title_ratio = $ratio;
								
								// Assign citation_id2 since all 3 criterias match.
								$citation_id2 = $row['citation_id'];  
								
								// Save all ratios in array. To be written into DB later.
								$ratios_array[] = array("citation_id1" => $citation_id, "citation_id2" => $citation_id2, "lastname_ratio" => $lastname_ratio, "year_ratio" => $year_ratio, "title_ratio" => $title_ratio);
							}
						}
					}		
				}
				else {} // Same citation_id. Do not fuzzy match.
				$ctr++;
			}
		}	
		
		return $ratios_array;   // Either empty array or ratios_array.
	}
	
	function updateSimilarToDB2($ratios_array)
	{
		$this->link = $this->connectDB();
	
		// Insert or Delete duplicates in similar_to
		foreach($ratios_array as $ratios)
		{
			$citation_id1 = $ratios['citation_id1'];
			$citation_id2 = $ratios['citation_id2'];
		
			$query2 = "INSERT INTO similar_to_new (citation_id1, citation_id2, lastname_ratio, year_ratio, title_ratio) VALUES ('".$ratios['citation_id1']."','".$ratios['citation_id2']."','".$ratios['lastname_ratio']."','".$ratios['year_ratio']."','".$ratios['title_ratio']."')";
			$result2 = $this->doQuery($query2, $this->link);

		}
		
		return true;
	}
	
	function getCitation_byID2($citation_id)
	{
		$this->link = $this->connectDB();

		$query = "SELECT * FROM $this->table WHERE citation_id='".$citation_id."'";
		
		$result_arr = $this->getJSON($query);
		return $result_arr;
	}


}	// End of class



?>
