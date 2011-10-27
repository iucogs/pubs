<?php 

require_once("../classes/Citations.class.php");
$citations = new Citations();

// Functions
function sendResponse($responseObj) 
{
	$jsonString = json_encode($responseObj);
	echo $jsonString;
}

if (isset($GLOBALS['HTTP_RAW_POST_DATA']))
{
	$jsonObj = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
	
	if(isset($jsonObj->{'request'}->{'type'}))
	{
		$type = $jsonObj->{'request'}->{'type'};
		$keyword = $jsonObj->{'request'}->{'keyword'};
		$submitter = $jsonObj->{'request'}->{'submitter'};
		$owner = $jsonObj->{'request'}->{'owner'};
		$page = $jsonObj->{'request'}->{'page'};
		$citations_per_page = $jsonObj->{'request'}->{'citations_per_page'};
		$sort_order = $jsonObj->{'request'}->{'sort_order'};
			
		if(empty($keyword))
		{
			$responseObj = array("error" => 0, "total_count" => 0, "citations" => "", "similar_citations_array" => "");
			sendResponse($responseObj);
		}
		else if (($type == "title") || ($type == "journal") || ($type == "author") || ($type == "all"))
		{
			$result = $citations->get_citations_JSON($type, $page, $submitter, $owner, $citations_per_page, $keyword, $sort_order, 0); 
			$responseObj = array("error" => $citations->error, "total_count" => $result[0], "citations" => $result[1], "similar_citations_exist_array" => $result[2]);
			//$responseObj = array("error" => $citations->error, "total_count" => $result[0], "citations" => $result[1], "similar_citations_array" => $result[2], "debug" => $citations->debug);
			sendResponse($responseObj);
		}
		else{ 
			// Error
			$responseObj = array("error" => 0, "total_count" => 0, "citations" => "", "similar_citations_array" => "");
			sendResponse($responseObj);
		}	
	}
	else{ 
		// Error
		$responseObj = array("error" => 0, "total_count" => 0, "citations" => "", "similar_citations_array" => "");
		sendResponse($responseObj);
	}
}


?>