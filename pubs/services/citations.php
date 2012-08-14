	<?php 

//header("Content-Type: application/xml; charset=UTF-8"); //commented for JSON
require_once('../classes/Citations.class.php');
require_once('../classes/Logger.class.php');

$citations = new Citations();

// Functions 
function echoJSONstr($result)
{
	global $citations;
	$jsonString = '{"error11111": "'.$citations->error.'", "citations":'.json_encode($result).'}';
//	$jsonString = '{"error": "'.$citations->error.'", "total_citations": "'.$result[0].'", "citations":'.json_encode($result[1]).'}';
	echo $jsonString; 
}

function get_and_return_citations($current_get_type, $submitter, $owner, $page, $citations_per_page, $sort_order, $entryTime, $citation_id_page = 0)
{
	global $citations;
	if($current_get_type == "getCitations_byTimestamp_all")
	{
		$result = $citations->getCitations_byTimestamp_all($entryTime);
		$jsonString = '{"error22": "'.$citations->error.'", "citations":'.json_encode($result[0]).', "similar_citations_array": '.json_encode($result[1]).'}';
	}
	else if($current_get_type == "getCitations_byFac_all")
	{
		// Ruth 4/12 need to replace
	//	$result = $citations->get_citations_JSON_collections_table($page, $submitter, $owner, $citations_per_page, $sort_order, 'all');
		$jsonString = '{"error": "'.$citations->error.'", "page": "'.$result[3].'", "total_count": "'.$result[0].'", "citations":'.json_encode($result[1]).', "similar_citations_array": '.json_encode($result[2]).'}'; 		
		echo $jsonString;
	}
	else if($current_get_type == "getCitations_byFac_unverified")
	{
		$result = $citations->get_citations_JSON($current_get_type, $page, $submitter, $owner, $citations_per_page, "", $sort_order, 0, $citation_id_page); 
		$jsonString = '{"error": "'.$citations->error.'", "page": "'.$result[3].'", "total_count": "'.$result[0].'", "citations":'.json_encode($result[1]).', "similar_citations_array": '.json_encode($result[2]).', "debug":'.json_encode($citations->debug).'}'; 
	}
	else {
		$jsonString = '{"error": "'.$citations->error.'"}';
	}
	
	echo $jsonString;
}

if (isset($GLOBALS['HTTP_RAW_POST_DATA']))
{
	$jsonObj = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
		
	if(isset($jsonObj->{'request'}->{'type'})){
		$type = $jsonObj->{'request'}->{'type'};
	}
	
	//abhinav
	if(isset($jsonObj->{'request'}->{'verified'})){
		$verified = $jsonObj->{'request'}->{'verified'};
	}

	
	if(isset($jsonObj->{'request'}->{'current_get_type'})){
		$current_get_type = $jsonObj->{'request'}->{'current_get_type'};
	}
	
	if(isset($jsonObj->{'request'}->{'page'})){
		$page = $jsonObj->{'request'}->{'page'};
	}
	
	if(isset($jsonObj->{'request'}->{'citation_id_page'})){
		$citation_id_page = $jsonObj->{'request'}->{'citation_id_page'};
	}
	
	if(isset($jsonObj->{'request'}->{'citations_per_page'})){
		$citations_per_page = $jsonObj->{'request'}->{'citations_per_page'};
	}
	
	if(isset($jsonObj->{'request'}->{'collection_id'})){ 
		$collection_id = $jsonObj->{'request'}->{'collection_id'};
	}
	
	if(isset($jsonObj->{'request'}->{'keyword'})){ 
		$keyword = $jsonObj->{'request'}->{'keyword'};
	}

	if(isset($jsonObj->{'request'}->{'sort_order'})){ 
		$sort_order = $jsonObj->{'request'}->{'sort_order'};
	}
	
	if(isset($jsonObj->{'request'}->{'pre_merge_id1'})){ 
		$pre_merge_id1 = $jsonObj->{'request'}->{'pre_merge_id1'};
	}
	
	if(isset($jsonObj->{'request'}->{'pre_merge_id2'})){ 
		$pre_merge_id2 = $jsonObj->{'request'}->{'pre_merge_id2'};
	}
	
	if(isset($jsonObj->{'request'}->{'coll_id'})){ 
		$coll_id = $jsonObj->{'request'}->{'coll_id'};
	}

	// Variables inside of citations object
	if(isset($jsonObj->{'request'}->{'citations'})){ 
		$citationObj = $jsonObj->{'request'}->{'citations'};
		if(isset($citationObj->{'collection_id'})){ 
			$collection_id = $citationObj->{'collection_id'};
		}
		if(isset($citationObj->{'submitter'})){ 
			$submitter = $citationObj->{'submitter'};
		}
		if(isset($citationObj->{'owner'})){ 
			$owner = $citationObj->{'owner'};
		}
		if(isset($citationObj->{'entryTime'})){ 
			$entryTime = $citationObj->{'entryTime'};
			if($entryTime == "undefined"){ $entryTime = ""; }
		}
		else
		{
			$entryTime = "";
		}
		if(isset($citationObj->{'citation_id'})){ 
			$citation_id = $citationObj->{'citation_id'};
			if($citation_id == "undefined"){ $citation_id = ""; }
		}
		// new author lastname and firstname
		if(isset($citationObj->{'lastname'})){ 
			$lastname = $citationObj->{'lastname'};
			if($lastname == "undefined"){ $lastname = ""; }
		}
		if(isset($citationObj->{'firstname'})){ 
			$firstname = $citationObj->{'firstname'};
			if($firstname == "undefined"){ $firstname = ""; }
		}
		if(isset($jsonObj->{'request'}->{'authorsArray'})){ 
			$authorsArray = $jsonObj->{'request'}->{'authorsArray'};
		}
	}
	
	if(isset($jsonObj->{'request'}->{'returnType'})){
		$returnType = $jsonObj->{'request'}->{'returnType'};
	}
	
	$element_array = array("citation_id","user_id","pubtype","cit_key","abstract","keywords","doi","url","address","annote","author","booktitle","chapter","crossref","edition","editor","translator","howpublished","institution","journal","bibtex_key","month","note","number","organization","pages","publisher","location","school","series","title","type","volume","year","raw","verified","format","filename","submitter","owner","entryTime","date_retrieved");

	$authors = array("author0id","author0ln","author0fn","author1id","author1ln","author1fn","author2id","author2ln","author2fn","author3id","author3ln","author3fn","author4id","author4ln","author4fn","author5id","author5ln","author5fn");
	
	$args = array();
	$args_authors = array();
	// Loop through element_array
	foreach ($element_array as $key)
	{
		if(isset($citationObj->{$key}))
		{
			$value = $citationObj->{$key};
			if(!empty($value)){
				$args[$key] = $value;
			}
			else
			{
				$args[$key] = "";
			}
		}
	}
	
	foreach ($authors as $key)
	{
		if(isset($citationObj->{$key}))
		{
			$value = $citationObj->{$key};
			if(!empty($value)) {
				$args_authors[$key] = $value;
			}
			else
			{
				$args_authors[$key] = "";
			}
		}
	}	

	if($type == "getCitations_byTimestamp_all")
	{
		$result = $citations->getCitations_byTimestamp_all($entryTime);
		$jsonString = '{"error33": "'.$citations->error.'", "citations":'.json_encode($result[0]).', "similar_citations_array": '.json_encode($result[1]).'}';
		echo $jsonString;
	}
	else if($type == "getCitations_byFac_all")
	{	
	#	$result = $citations->get_citations_JSON($type, $page, $submitter, $owner, $citations_per_page, "", $sort_order, 0, $citation_id_page); 
	#	$jsonString = '{"error": "'.$citations->error.'", "page": "'.$result[3].'", "citation_id_page": "'.$citation_id_page.'", "total_count": "'.$result[0].'", "citations":'.json_encode($result[1]).', "similar_citations_array": '.json_encode($result[2]).'}';
		
		// Ruth 4/12
		//$result = $citations->get_citations_JSON_collections_table($page, $submitter, $owner, $citations_per_page, $sort_order, 'all');
		$result = $citations->getCitations_byFac_all($submitter, $owner);

		$jsonString = '{"error11": "'.$citations->error.'", "pageeeeeee": "'.$submitter.'","pageeeeeee": "'.$owner.'","page": "'.$result[3].'", "total_count": "'.$result[0].'", "citations":'.json_encode($result[1]).', "similar_citations_array": '.json_encode($result[2]).'}';
		//Logger::instance()->clear();
		//Logger::instance()->log($jsonString);
		echo $jsonString;
		}
	else if($type == "getCitations_byFac_unverified")
	{	
		$result = $citations->get_citations_JSON($type, $page, $submitter, $owner, $citations_per_page, "", $sort_order, 0, $citation_id_page); 
		$jsonString = '{"error": "'.$citations->error.'", "page": "'.$result[3].'", "citation_id_page": "'.$citation_id_page.'", "total_count": "'.$result[0].'", "citations":'.json_encode($result[1]).', "similar_citations_array": '.json_encode($result[2]).'}';
	
		echo $jsonString;
	}
	else if($type == "getCitationsGivenCollectionID")
	{	
	
	// Ruth 4/12
		//$result = $citations->get_citations_JSON_collections_table($page, $submitter, $owner, $citations_per_page, $sort_order, $collection_id);
		$result = $citations->getCitationsGivenCollectionID($collection_id, $page, $citations_per_page, $submitter, $owner);
		$jsonString = '{"error22": "'.$citations->error.'", "page": "'.$result[3].'", "total_count": "'.$result[1].'", "citations":'.json_encode($result[0]).', "similar_citations_array": '.json_encode($result[2]).'}'; 		
		echo $jsonString;
	}
	else if($type == "delete")
	{
		// Do the deletetion
		if($current_get_type == 'getCollection') 	// Delete from collection only
		{
			require_once('../classes/Collections.class.php');
			$collection = new Collections();
			$deleted = $collection->deleteCitationByCollectionId($citation_id, $collection_id);
			$coll_table_deleted = $collection->deleteCitationByCollectionId_collecitons_table($citation_id, $collection_id);
		}
		else										// Delete permanently
		{
			$deleted = $citations->delete($citation_id, "DELETE", $submitter, $owner);
			$coll_table_deleted = $citations->delete_collecitons_table($citation_id, $submitter, $owner);
		}
		
		// If deletetion is successful then proceed with response type
		if($deleted != false)
		{
			if (($current_get_type == 'getCitations_byFac_all') || ($current_get_type == 'getCitations_byFac_unverified') || ($current_get_type == 'getCitations_byTimestamp_all'))
			{
				get_and_return_citations($current_get_type, $submitter, $owner, $page, $citations_per_page, $sort_order, $entryTime, 0);
			}
			else if (($current_get_type == 'journal') || ($current_get_type == 'title') )
			{
				$result = $citations->get_citations_JSON($current_get_type, $page, $submitter, $owner, $citations_per_page, $keyword, $sort_order, 0, 0); 
				$jsonString = '{"error33": "'.$citations->error.'", "page": "'.$result[3].'", "total_count": "'.$result[0].'", "citations":'.json_encode($result[1]).', "similar_citations_array": '.json_encode($result[2]).'}';
				echo $jsonString;
			}
			else if ($current_get_type == 'getCollection')
			{
				//$result = $citations->get_citations_JSON($type, $page, $submitter, $owner, $citations_per_page, "", $sort_order, $collection_id, 0);
				// Ruth 4/12 need to replace
			//	$result = $citations->get_citations_JSON_collections_table($page, $submitter, $owner, $citations_per_page, $sort_order, $collection_id);
				$jsonString = '{"error44": "'.$citations->error.'", "page": "'.$result[3].'", "total_count": "'.$result[0].'", "citations":'.json_encode($result[1]).', "similar_citations_array": '.json_encode($result[2]).'}'; 
				echo $jsonString;
			}
		}
		else
		{
		    $jsonString = '{"error": "'.$citations->error.'"}';
			echo $jsonString;
		}
	}
	else if($type == "new_author")  //maybe not used anymore
	{	
		$new_author = $citations->addNewAuthor($firstname, $lastname, $submitter, $owner);
		if($new_author != false)
		{
			$jsonString = '{"error": "'.$citations->error.'", "new_author_id": "'.$new_author.'", "firstname": "'.$firstname.'", "lastname":"'.$lastname.'"}';
			echo $jsonString;
		}
		else
		{
		    $jsonString = '{"error": "'.$citations->error.'", "new_author_id": "false"}';
			echo $jsonString;
		} 
	}
	else if ($type == "check_authors")
	{	
		$result = $citations->checkAuthors($args, $args_authors, $coll_id);
		
		if (isset($result[0][0]))  // Returning author array not citation array. Unverified Authors
		{
			if (!empty($pre_merge_id1) && !empty($pre_merge_id2)) {
				$jsonString = '{"error": "'.$citations->error.'", "pre_merge_id1": "'.$pre_merge_id1.'", "pre_merge_id2": "'.$pre_merge_id2.'", "citations":'.json_encode($result).'}';
			}
			else {
				$jsonString = '{"error44": "'.$citations->error.'", "citations":'.json_encode($result).'}';
			}
		}
		else if (!empty($pre_merge_id1) && !empty($pre_merge_id2))  // Save merged citations
		{
			// get collection ids of pre_merge_id1 and pre_merge_id2
			require_once('../classes/Collections.class.php');
			$collection = new Collections();
			$collection_ids_array1 = $collection->getCollectionsByCitationID($pre_merge_id1);
			$collection_ids_array2 = $collection->getCollectionsByCitationID($pre_merge_id2);
			$collection_ids_array = array_merge($collection_ids_array1, $collection_ids_array2);
			foreach ($collection_ids_array as $collection_id)
			{
				$insert_result = $collection->insert_member_of_collection($collection_id, array($result[0]['citation_id']), $submitter, $owner);
			}
			// to do maybe: return collections added to
			
			// Delete merged citations.
			$reason = "MERGED > ".$result[0]['citation_id'];
	//		$merge1_deleted = $citations->delete($pre_merge_id1, $reason, $submitter, $owner);
			$merge2_deleted = $citations->delete($pre_merge_id2, $reason, $submitter, $owner);
			
			if($merge2_deleted == false)
			{
				$citations->error .= 2;  // Error
			}
			$jsonString = '{"error55": "'.$citations->error.'", "citations":'.json_encode($result).'}';
		}
		else if ((!empty($collection_id) && ($result[0]['citation_id'] != -1)))  // Save
		{
			require_once('../classes/Collections.class.php');
			$collection = new Collections();
			$citation_ids = array($result[0]['citation_id']);
			$insert_result = $collection->insert_member_of_collection($collection_id, $citation_ids, $submitter, $owner);
			$collection_id = $insert_result[0];
			$jsonString = '{"error66": "'.$citations->error.'", "citations":'.json_encode($result).'}';
		}
		else $jsonString = '{"error77": "'.$citations->error.'", "citations":'.json_encode($result).'}'; // Default
		
		echo $jsonString;
	}
	else if ($type == "create_authors")
	{
		// Ruth 080712 - createNewAuthorsAndSave changed to saveOrUpdateACitation
		$result = $citations->saveOrUpdateACitation($args, $args_authors, $coll_id,$verified);
		if (!empty($pre_merge_id1) && !empty($pre_merge_id2))   // Save merged citations and create verified authors.
		{
			// get collection ids of pre_merge_id1 and pre_merge_id2
			require_once('../classes/Collections.class.php');
			$collection = new Collections();
			$collection_ids_array1 = $collection->getCollectionsByCitationID($pre_merge_id1);
			$collection_ids_array2 = $collection->getCollectionsByCitationID($pre_merge_id2);
			$collection_ids_array = array_merge($collection_ids_array1, $collection_ids_array2);
			foreach ($collection_ids_array as $collection_id)
			{
				$insert_result = $collection->insert_member_of_collection($collection_id, array($result[0]['citation_id']),$submitter, $owner);
			}
			// to do maybe: return collections added to
			
			// Delete merged citations.
			$reason = "MERGED > ".$result[0]['citation_id'];
		//	$merge1_deleted = $citations->delete($pre_merge_id1, $reason, $submitter, $owner);
			$merge2_deleted = $citations->delete($pre_merge_id2, $reason, $submitter, $owner);
			
			if($merge2_deleted == false)
			{
				$citations->error .= 2;  // Error
			}
		}
		else if (!empty($collection_id))
		{
			require_once('../classes/Collections.class.php');
			$collection = new Collections();
			$citation_ids = array($result[0]['citation_id']);
			$insert_result = $collection->insert_member_of_collection($collection_id, $citation_ids,$submitter, $owner);
			$collection_id = $insert_result[0];
		}
		echoJSONstr($result);
	}
	else if($type == "blankXML")
	{
		$result = array();
		echoJSONstr($result);
	}
	else
	{
		$result = array();
		echoJSONstr($result);
	}
}





?>