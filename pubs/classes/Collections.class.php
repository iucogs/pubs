<?php 

class Collections
{
	var $doc;
	var $root;
	var $link;
	var $table;
	var $limit;
	var $error;

	function Collections()
	{
		require_once('../lib/mysql_connect.php');
		$this->doc = new DOMDocument();
		$this->formatOutput = true;
		$this->root = array();
		$this->table = "collections";
		$this->limit = "LIMIT 0,200";
		$this->error = 0;
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
	
	
	function doQuery($query, $link) {  //added for json function
	
		if (!$result = mysql_query($query, $link)) {
			$this->error .= 1;
		}
		return $result;	
	}
	
	// FORCE_CREATE = force unique collection name creation. (used by TI in parser.php)
	function createAndAddCollection($collection_name, $citation_ids, $submitter, $owner, $FORCE_CREATE = false)  
	{
		$result_arr;
		if(($result_arr=$this->return_collection_id_and_create_collection_if_nonexistent($collection_name, $submitter, $owner)) != false)
		{
			$collection_status = $result_arr[0];
			$new_or_existing_collection_id = $result_arr[1];
		
			if($collection_status == "exists")// Collection exists.
			{
				if($FORCE_CREATE) 		// Force collection creation and add citations!
				{
					// Get unique name.	
					$unique_name = $this->getUniqueCollectionName($collection_name);

					// Tail recursion using unique name.
					return $this->createAndAddCollection($unique_name, $citation_ids, $submitter, $owner, $FORCE_CREATE);
				}
				else 					// Do not add citations!
				{
					return array("exists", $new_or_existing_collection_id, 0, 0);  
				}
			}
			else {
				$insert_result = $this->insert_member_of_collection($new_or_existing_collection_id, $citation_ids, $submitter, $owner);
				if($insert_result[0]) {					
					return array("new_inserted", $new_or_existing_collection_id, $insert_result[1], $insert_result[2]);  // Collection does not exists.
				}
				else {						
					$this->error .= 1;
					return false;			// DB insert error
				}	
			}
		}
		else 
		{
			$this->error .= 1;
			return false;  		// DB create error
		}
	}
	
	function checkCollection($collection_name, $submitter, $owner)
	{
		$this->link = $this->connectDB();
		$collection_name = trim($collection_name);
		$query = "SELECT * FROM collections WHERE collection_name='".mysql_real_escape_string($collection_name)."' AND owner='".mysql_real_escape_string($owner)."'";
		
		$result = $this->doQuery($query, $this->link);
		if(mysql_num_rows($result) > 0) 
		{
			$row = mysql_fetch_array($result, MYSQL_ASSOC);
		//	print_r($row);
			$collection_id = $row['collection_id'];

			return $collection_id;   // Collection exists.
		}	
		else 
		{
			return false;
		}			
	}
	
	function return_collection_id_and_create_collection_if_nonexistent($collection_name, $submitter, $owner)
	{
		$this->link = $this->connectDB();

		$collection_name = trim($collection_name);
		
		if(empty($collection_name))  // Check for empty collection name. Return false
		{
			return false;
		}
			
		if(($collection_id = $this->checkCollection($collection_name, $submitter, $owner)) != false)
		{
		//	return array("-1", $collection_id);   // Collection exists.
			return array("exists", $collection_id);   // Collection exists.
		}
		
		$query = "INSERT INTO collections (collection_name, user_id, submitter, owner) VALUES ('".mysql_real_escape_string($collection_name)."', 0, '".$submitter."', '".$owner."')";

		$result = $this->doQuery($query, $this->link);
		if(!$result) {
			return false;
		}
		else {
			$new_collection_id = (int)mysql_insert_id();
			return array("1", $new_collection_id);
		}			
	}	
	
	function insert_member_of_collection($collection_id, $citation_ids, $submitter, $owner) // FAC PUBS TO-DO: Check if collection belongs to submitter before insertion?
	{
		$this->link = $this->connectDB();
		
		$insert_count = 0;
		$duplicates = 0;
		$insert_error = false;
			
		foreach($citation_ids as $citation_id)
		{
			$query = "SELECT * FROM member_of_collection WHERE collection_id='$collection_id' AND citation_id='$citation_id'";
			$result = $this->doQuery($query, $this->link);
			if(mysql_num_rows($result) > 0) 
			{	
				// Duplicates
				$duplicates++;
			}
			else
			{
				$query = "INSERT INTO member_of_collection (collection_id, citation_id) VALUES ($collection_id, $citation_id)";
				$result = $this->doQuery($query, $this->link);
				if(!$result)
				{
					$insert_error = true;
					$this->error .= 2;
				}
				else
				{
					$insert_count++;
				}
			}
		}
		
		if($insert_error) return -1;
		else return array($collection_id, $insert_count, $duplicates);
	}
	
	function setAccessToCollection($user_id, $coll_id)
	{
		$this->link = $this->connectDB();
		$query = "INSERT INTO access_to (user_id, collection_id) VALUES ($user_id, $coll_id)";
		$result = $this->doQuery($query, $this->link);
		if(!$result)
		{
			$this->error .= 2;
		}
	}
	
	function deleteCollection($collection_id)
	{
		$this->link = $this->connectDB();
		
		$query_collection = "DELETE FROM collections WHERE collection_id='$collection_id'";
		$result = $this->doQuery($query_collection, $this->link);
		$query_coll_list = "DELETE FROM member_of_collection WHERE collection_id='$collection_id'";
		$result = $this->doQuery($query_coll_list, $this->link);
		$query_access_to = "DELETE FROM access_to WHERE collection_id='$collection_id'";  // Should we delete others fav list?
		$result = $this->doQuery($query_access_to, $this->link);
		
		if($result) return $collection_id;
		else return false;
	}
	
	function deleteCollections($collection_ids)
	{
		$result = array();
		
		foreach($collection_ids as $collection_id)
		{
			if(($result_id = $this->deleteCollection($collection_id)) != false)
			{
				$result[] = array($collection_id => $result_id);
			}
			else
			{
				$result[] = array($collection_id => 'error');
			}
		}
		// Look for delete 'error' key in $result
		if(array_search('error', $result) === false) {
			return true;
		}
		else {
			$this->error .= 2;
			return false;
		}
	}
	
	function mergeCollections($collection_id, $collection_ids, $submitter, $owner)
	{
		$this->link = $this->connectDB();
		
		// Get special collections ids. Misc, My representative pubs and My CV pubs.
		$query = "SELECT collection_id FROM collections WHERE owner = '$owner' AND (collection_name = 'misc' OR collection_name = 'My CV Publications' OR collection_name = 'My Representative Publications');";
		$result = $this->doQuery($query, $this->link);
		$special_coll_arr = array();
		if(mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_assoc($result)) {
				$special_coll_arr[] = $row['collection_id'];  // Copy result into an array
			}
		}
		
		// New collection comes in as the string name. Create new collection.
		if (!is_numeric($collection_id))
		{
			$new_name = $collection_id; // Make a copy of the new name.
			
			foreach($collection_ids as $coll_id) {
				if(!in_array($coll_id, $special_coll_arr)) { // Make sure the collection is not a special collection
					$collection_id = $coll_id;	// Use one of current collection as the new collection.
					break;
				}
			}
			$collection_rename_result = $this->renameCollection($collection_id, $new_name, $submitter, $owner);
		}
		
		$collection_ids_to_be_deleted = array();
		
		// Select all citation_ids from member_of_collection and selected_collection as collection_id
		$select_ids = "SELECT DISTINCT ".mysql_real_escape_string($collection_id)." AS collection_id, moc.citation_id FROM member_of_collection moc WHERE ";
		// Where the citation_id does not exists in selected collection.
		$select_ids .= "NOT EXISTS (SELECT * FROM member_of_collection WHERE collection_id='".mysql_real_escape_string($collection_id)."' AND moc.citation_id = citation_id) AND ";
		foreach($collection_ids as $coll_id) // Loop through the collections to be merged
		{
			// Select all citation_ids except the one we're inserting into.
			if($coll_id != $collection_id) 
			{ 	
				$select_ids .= "collection_id='".mysql_real_escape_string($coll_id)."' OR ";
				// if coll_id is not a special collection, then proceed to delete.
				if(!in_array($coll_id, $special_coll_arr)) $collection_ids_to_be_deleted[] = $coll_id; 
			}
		}
		$select_ids = substr($select_ids, 0, -3);	 // Take out last "OR"
			
		// Insert into existing collection_id record
		$query = "INSERT INTO member_of_collection ".$select_ids." ";

		$result = $this->doQuery($query, $this->link);
		if($result)
		{
			// Delete old merged collections
			$delete_result = $this->deleteCollections($collection_ids_to_be_deleted);  // return [true|false]
			if($delete_result != false) {
				
				// Update collections_table
				require_once('Citations.class.php');
				$citation_obj = new Citations();
				foreach($collection_ids as $coll_id) {
					$citation_obj->createAndUpdateOneCollectionInCollectionsTable($coll_id, $submitter, $owner);
				}
				return $collection_id;
			}
			else {
				$this->error .= 2;
				return -1;
			}
		}
		else
		{
			$this->error .= 2;
			return -1; // Insert error.
		}
	}
	
	// Get a unique name for an existing collection_id. 
	function getUniqueCollectionName($collection_rename, $EXCEPTION_ID = 0) // $collection_id,  
	{
		$this->link = $this->connectDB();
		
		// Trim spaces that can cause problems
		$collection_rename = trim($collection_rename);
		$current_name = $collection_rename;
		
		$end_loop = false;
		$copy_extension = "";
		
		for($i = 1; $end_loop == false; $i++)
		{
			$query = "SELECT * FROM collections WHERE collection_name='".mysql_real_escape_string($current_name)."'";
			$result = $this->doQuery($query, $this->link);

			if(mysql_num_rows($result) > 0)
			{
				$row = mysql_fetch_assoc($result);
				if($EXCEPTION_ID == 0)					// No collection_id for exception
				{
					$copy_extension = "-".$i."";
					$current_name = $collection_rename.$copy_extension;
				}
				else {									// Have collection_id for exception
					if($row['collection_id'] != $EXCEPTION_ID)
					{
						$copy_extension = "-".$i."";
						$current_name = $collection_rename.$copy_extension;
					}
					else // Changing collection_name to the same current collection_name.
					{
						$end_loop = true;	
					} 
				}
			}
			else
			{
				$end_loop = true;			
			}
		}
		
		return $current_name;
	}
	
	function renameCollection($collection_id, $collection_rename, $submitter, $owner)
	{
		$this->link = $this->connectDB();
		
		// Check for empty input.
		if(empty($collection_rename)) return array($collection_id, $collection_rename);
		
		// Check if there is collection with similar name. Add copy (1) or (2) and so on.
		$collection_rename = trim($collection_rename);
		
		// Get unique name with collection_id as the exception.
	//	$unique_name = $this->getUniqueCollectionName($collection_rename, $collection_id); 
	
		$return_val = $this->checkCollection($collection_rename, $submitter, $owner);
		
	//	echo 'collection_id: '.$collection_id;
		if ($return_val == false)
		{
			$query = "UPDATE collections SET collection_name='".mysql_real_escape_string($collection_rename)."' WHERE collection_id='$collection_id' AND owner='$owner'";
			$result = $this->doQuery($query, $this->link);
			
			if($result) return array($collection_id, $collection_rename);
			else return false;
		}
		else
		{
			return array(-1, $collection_rename);
		}
	}
	
	function getDefaultCollectionNamesAndIds($submitter, $owner)
	{
		$this->link = $this->connectDB();
		
		// This query should be the same query from [Citations.class.php]->get_citations_JSON_query()
		$query_in = "citation_id IN (SELECT moc.citation_id FROM member_of_collection moc, collections col WHERE moc.collection_id = col.collection_id AND col.owner = '".$owner."') ";	
		
		$all_count_query = "COUNT(*) as count FROM citations c WHERE (owner ='".$owner."' OR ".$query_in.")";	
		$unverified_count_query = "COUNT(*) as count FROM citations c WHERE verified = 0 AND (owner ='".$owner."' OR ".$query_in.")";	
		
		$query = "SELECT 'all' AS collection_id, 'All My Citations' AS collection_name, ".$all_count_query." ";
		$query .= " UNION ";
		$query .= "SELECT 'unverified' AS collection_id, 'All My Unverified Citations' AS collection_name, ".$unverified_count_query." ";

		$result = $this->doQuery($query, $this->link);

		if(mysql_num_rows($result) > 0)
		{
			$result_arr = array();
			while(($result_arr[] = mysql_fetch_assoc($result)) || array_pop($result_arr));  // Copy result into an array
			return $result_arr;
		}
		else
		{
			return false;
		}
	}
	
	// Grab Misc, My Representative Pubs and My CV Pubs
	function getSpecialCollectionNamesAndIds($submitter, $owner)
	{
		$this->link = $this->connectDB();
			
		// Put Misc, Rep Pubs and CV Pubs at the top
		// Misc
		$query = "";
		$query .= "(SELECT c.collection_id, c.collection_name, (SELECT Count( * ) FROM member_of_collection moc WHERE moc.collection_id = c.collection_id) as count FROM collections c WHERE owner='".$owner."' AND (c.collection_name = 'misc'))";
		
		// My Representative Publications
		$query .= " UNION ";
		$query .= "(SELECT c.collection_id, c.collection_name, (SELECT Count( * ) FROM member_of_collection moc WHERE moc.collection_id = c.collection_id) as count FROM collections c WHERE owner='".$owner."' AND (c.collection_name = 'My Representative Publications'))";
		
		// My CV Publications
		$query .= " UNION ";
		$query .= "(SELECT c.collection_id, c.collection_name, (SELECT Count( * ) FROM member_of_collection moc WHERE moc.collection_id = c.collection_id) as count FROM collections c WHERE owner='".$owner."' AND (c.collection_name = 'My CV Publications'))";
						
		$result = $this->doQuery($query, $this->link);

		if(mysql_num_rows($result) > 0)
		{
			$result_arr = array();
			while(($result_arr[] = mysql_fetch_assoc($result)) || array_pop($result_arr));  // Copy result into an array
			return $result_arr;
		}
		else
		{
			return false;
		}
	}
	
	function getCollectionNamesAndIds($submitter, $owner)
	{
		$this->link = $this->connectDB();
		
		$WHERE_clause = "";
		if ($owner != "")
		{
			$WHERE_clause = "WHERE owner='".$owner."' ";
			// Do not include misc, My Representative Publications and My CV Publications since it will be included in defaultCollections
			$WHERE_clause .= "AND c.collection_name != 'misc' AND c.collection_name != 'My Representative Publications' AND c.collection_name != 'My CV Publications'";
		}
		
		// Put Misc, Rep Pubs and CV Pubs at the top
		// Misc
		$query = "";
		/*$query .= "(SELECT c.collection_id, c.collection_name, (SELECT Count( * ) FROM member_of_collection moc WHERE moc.collection_id = c.collection_id) as count FROM collections c WHERE owner='".$owner."' AND (c.collection_name = 'misc'))";
		
		// My Representative Publications
		$query .= " UNION ";
		$query .= "(SELECT c.collection_id, c.collection_name, (SELECT Count( * ) FROM member_of_collection moc WHERE moc.collection_id = c.collection_id) as count FROM collections c WHERE owner='".$owner."' AND (c.collection_name = 'My Representative Publications'))";
		
		// My CV Publications
		$query .= " UNION ";
		$query .= "(SELECT c.collection_id, c.collection_name, (SELECT Count( * ) FROM member_of_collection moc WHERE moc.collection_id = c.collection_id) as count FROM collections c WHERE owner='".$owner."' AND (c.collection_name = 'My CV Publications'))";*/
		
		// The rest
		//$query .= " UNION ";
		$query .= "(SELECT c.collection_id, c.collection_name, (SELECT Count( * ) FROM member_of_collection moc WHERE moc.collection_id = c.collection_id) as count FROM collections c ".$WHERE_clause." ORDER BY LTRIM(c.collection_name))";
				
		$result = $this->doQuery($query, $this->link);

		if(mysql_num_rows($result) > 0)
		{
			$result_arr = array();
			while(($result_arr[] = mysql_fetch_assoc($result)) || array_pop($result_arr));  // Copy result into an array
			return $result_arr;
		}
		else
		{
			return false;
		}
	}
	
	function getCollectionByID($collection_id)
	{
		$this->link = $this->connectDB();
		$query = "SELECT c.*, cl.citation_id FROM collections c, member_of_collection cl WHERE c.collection_id='$collection_id'";
		$result = $this->doQuery($query, $this->link);
		
		if(mysql_num_rows($result) > 0)
		{
			return mysql_fetch_array($result);
		}
		else
		{
			return false;
		}
		
	}
	
	function getCollectionsGivenCitationID($citation_id, $submitter, $owner)
	{
		$this->link = $this->connectDB();
					
		$query = "SELECT moc.citation_id, col.collection_name, col.collection_id FROM member_of_collection moc, collections col WHERE moc.collection_id = col.collection_id AND col.owner = '$owner' AND moc.citation_id = $citation_id ORDER BY col.collection_name";		
		
		$result = $this->doQuery($query, $this->link);
		
		$result_arr = array();
		
		while($row = mysql_fetch_assoc($result))
        {
			$result_arr[] = array($row['collection_id'],$row['collection_name']);
		}
		
		return $result_arr;	
	}
	
	function getCollectionIDGivenCollectionName($collection_name, $owner)
	{
		$this->link = $this->connectDB();
					
		$query = "SELECT * FROM collections col WHERE col.owner = '$owner' AND col.collection_name = '$collection_name'";		
		$result = $this->doQuery($query, $this->link);
		$row = mysql_fetch_assoc($result);
		return $row['collection_id'];	
	}
	
	function getCollectionsByCitationID($citation_id)
	{
		$this->link = $this->connectDB();
		$temp = array();
		$query = "SELECT moc.collection_id FROM member_of_collection moc WHERE moc.citation_id='$citation_id'";
		$result_collection_ids = $this->doQuery($query, $this->link);
		while($row = mysql_fetch_assoc($result_collection_ids))
        {				
			$temp[] = $row['collection_id'];
		}
		return $temp;		
	}
	
	
	function deleteCitationByCollectionId($citation_id, $collection_id)
	{
		$this->link = $this->connectDB();
		
		$query = "DELETE FROM member_of_collection WHERE collection_id='$collection_id' AND citation_id='$citation_id'";
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
	
	// Functions for collections_table
	function deleteCitationByCollectionId_collecitons_table($citation_id, $collection_id, $submitter, $owner)
	{
		$query = "DELETE FROM collections_table WHERE coll_id='$collection_id' AND citation_id='$citation_id'";
		
		$result = $this->doQuery($query, $this->link);
		if(!$result)
		{
			$this->error .= 2;
			return false;
		}
		else 
		{
			// Check if the citation_id does not exist in collections_table for a particular owner.
			$query = "SELECT * FROM collections_table WHERE citation_id='$citation_id' AND owner='$owner'";
			$result = $this->doQuery($query, $this->link);
			$row = mysql_fetch_assoc($result);
			
			if(mysql_num_rows($result) == 0) {
				require_once('../classes/Citations.class.php');
				$citations = new Citations();
				$coll_id = $this->getCollectionIDGivenCollectionName("misc", $owner);
				
				// Move the citation to misc collection
				return $citations->saveOneCitationToCollectionsTable($citation_id, $coll_id, $submitter, $owner);
			}
			
			return true;
		}
	}
	
	
	
}	// End of class



?>
