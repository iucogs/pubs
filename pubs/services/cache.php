<?php 

require_once('../classes/Citations.class.php');

$citations = new Citations();

$type = '';

if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) 
{
	$jsonObj = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
		
	if(isset($jsonObj->{'request'}->{'type'})){
		$type = $jsonObj->{'request'}->{'type'};
	}
	
	if(isset($jsonObj->{'request'}->{'page'})){
		$page = $jsonObj->{'request'}->{'page'};
	}
	
	if(isset($jsonObj->{'request'}->{'citations_per_page'})){
		$citations_per_page = $jsonObj->{'request'}->{'citations_per_page'};
	}

	if(isset($jsonObj->{'request'}->{'sort_order'})){ 
		$sort_order = $jsonObj->{'request'}->{'sort_order'};
	}
	
	if(isset($jsonObj->{'request'}->{'citations'})){ 
		$citationObj = $jsonObj->{'request'}->{'citations'};

		if(isset($citationObj->{'submitter'})){ 
			$submitter = $citationObj->{'submitter'};
		}
		if(isset($citationObj->{'owner'})){ 
			$owner = $citationObj->{'owner'};
		}
	}
}

if($type == "cache_all")
{	
	foreach ($owner as $key)
	{
	//	$result = $citations->getCitations_byFac_all($submitter, $owner, 'all', '', '',$sort_order, $citations_per_page,$page);

	}
}
?>
