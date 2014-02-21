<?php 

require_once('../classes/Collections.class.php');
require_once('../classes/Citations.class.php');
$collection = new Collections();
$citation = new Citations();

// Functions
function sendResponse($responseObj) 
{
	$jsonString = json_encode($responseObj);
	echo $jsonString;
}

if (isset($GLOBALS['HTTP_RAW_POST_DATA']))
{
	$jsonObj = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
	
	if(isset($jsonObj->{'request'}->{'type'})){
		$type = $jsonObj->{'request'}->{'type'};
	}
	if(isset($jsonObj->{'request'}->{'submitter'})){ 
		$submitter = $jsonObj->{'request'}->{'submitter'};
	}
	if(isset($jsonObj->{'request'}->{'owner'})){ 
		$owner = $jsonObj->{'request'}->{'owner'};
	}
	if(isset($jsonObj->{'request'}->{'sort_order'})){ 
		$sort_order = $jsonObj->{'request'}->{'sort_order'};
	}
	if(isset($jsonObj->{'request'}->{'collection_name'})){ 
		$collection_name = $jsonObj->{'request'}->{'collection_name'};
	}
	if(isset($jsonObj->{'request'}->{'collection_rename'})){ 
		$collection_rename = $jsonObj->{'request'}->{'collection_rename'};
	}
	if(isset($jsonObj->{'request'}->{'citation_id'})){ 
		$citation_id = $jsonObj->{'request'}->{'citation_id'};
	}
	if(isset($jsonObj->{'request'}->{'citation_ids'})){ 
		$citation_ids = $jsonObj->{'request'}->{'citation_ids'};
	}
	if(isset($jsonObj->{'request'}->{'collection_id'})){ 
		$collection_id = $jsonObj->{'request'}->{'collection_id'};
	}
	if(isset($jsonObj->{'request'}->{'collection_ids'})){ 
		$collection_ids = $jsonObj->{'request'}->{'collection_ids'};
	}
	if(isset($jsonObj->{'request'}->{'page'})){
		$page = $jsonObj->{'request'}->{'page'};
	}
	if(isset($jsonObj->{'request'}->{'citations_per_page'})){
		$citations_per_page = $jsonObj->{'request'}->{'citations_per_page'};
	}
	
	if ($type == "new") {
		if(($result = $collection->createCollection($collection_name, $submitter, $owner)) != false) {
			list($collection_status, $collection_id) = $result;
			$collection_array = $collection->getCollectionByID($collection_id); 
			$collection_name = $collection_array['collection_name'];
			$responseObj = array("error" => $collection->error, "collection_status" => $collection_status, "collection_id" => $collection_id, "collection_name" => $collection_name);
			sendResponse($responseObj);
		}
	}
	else if ($type == "new_and_add") {
		if(($result = $collection->createAndAddCollection($collection_name, $citation_ids, $submitter, $owner)) != false) {
			list($collection_status, $collection_id, $insert_count, $duplicates) = $result;
			
			// Update collections_table
			//$citation->createAndUpdateCollectionsTable($collection_id, $submitter, $owner);
			
			$collection_array = $collection->getCollectionByID($collection_id);
			$responseObj = array("error" => $collection->error, "collection_status" => $collection_status, "collection_id" => $collection_id, 
							"collection_name" => $collection_array['collection_name'], "insert_count" => $insert_count, 
							"duplicates" => $duplicates);
			sendResponse($responseObj);
		}							 
	}
	else if ($type == "addTo") {
		if(($result = $collection->insert_member_of_collection($collection_id, $citation_ids, $submitter, $owner)) != false) {
			list($collection_id, $insert_count, $duplicates) = $result;
			
			// Update collections_table
			//$citation->createAndUpdateCollectionsTable($collection_id, $submitter, $owner);
			
			$collection_array = $collection->getCollectionByID($collection_id);
			$collection_name = $collection_array['collection_name'];
			// collection_exists = 0 means that insertion into existing collection successful
			$responseObj = array("error" => $collection->error, "collection_status" => "exists_inserted", "collection_id" => $collection_id, 
							"collection_name" => $collection_name, "insert_count" => $insert_count, 
							"duplicates" => $duplicates);
			sendResponse($responseObj);
		}
	}
	else if($type == "delete")
	{
		if($collection->deleteCollections($collection_ids))
		{	
			$responseObj = array("error" => $collection->error, "collections_deleted" => 1);
			sendResponse($responseObj);
		} else {
            // failure for some reason
          $responseObj = array("error" => $collection->error, "collections_deleted" => 0);
          sendResponse($responseObj);
        }
	}
	else if($type == "merge")
	{
		$result = $collection->mergeCollections($collection_id, $collection_ids, $submitter, $owner);	
		$jsonString = '{"error": "'.$collection->error.'", "collection_id":'.json_encode($result).'}';
		echo $jsonString;
	}
	else if($type == "rename")
	{
		// TO-DO: Update collections_table?
		$collection_result = $collection->renameCollection($collection_id, $collection_rename, $submitter, $owner);
		$collection_id = $collection_result[0];
		$collection_name = $collection_result[1];
		$jsonString = '{"error": "'.$collection->error.'", "collection_id": "'.$collection_id.'", "collection_name": "'.$collection_name.'"}';
		echo $jsonString;
	}
	else if($type == "getCollectionNamesAndIds")
	{
		$result = $collection->getCollectionNamesAndIds($submitter, $owner);
		$result2 = $collection->getDefaultCollectionNamesAndIds($submitter, $owner);	//all and unverified by submitter
		$jsonString = '{"error": "'.$collection->error.'", "collections":'.json_encode($result).', "default_collections":'.json_encode($result2).'}';
		echo $jsonString;
	}
	else if($type == "getCollectionsGivenCitationID")
	{
		$result = $collection->getCollectionsGivenCitationID($citation_id, $submitter, $owner);
		$jsonString = '{"error": "'.$collection->error.'", "collections":'.json_encode($result).'}';
		echo $jsonString;
	}
	else {
		$responseObj = array("error" => $collection->error);
		sendResponse($responseObj);
	}
	
	
	
}

?>
