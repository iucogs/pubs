<?php 

require_once('../classes/Citations.class.php');
$citations = new Citations();

if (isset($GLOBALS['HTTP_RAW_POST_DATA']))
{
	$jsonObj = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
		
	if(isset($jsonObj->{'request'}->{'type'})){
		$type = $jsonObj->{'request'}->{'type'};
	}
	
	if($type == "populate_all") {
		$result = $citations->updateEveryCollectionsTable();
		$jsonString = '{"error": "'.$citations->error.'", "type":"'.$type.'", "result":'.json_encode($result).'}';
		echo $jsonString;
	}
	else if($type == "truncate") {
		$result = $citations->truncateCollectionsTable();
		$jsonString = '{"error": "'.$citations->error.'", "type":"'.$type.'", "result":'.json_encode($result).'}';
		echo $jsonString;
	}
	else if($type == "create_misc") {
		//$result = $citations->createMiscCollectionForOneOwner('colallen');
		$result = $citations->createMiscCollectionForAllOwners();
		$jsonString = '{"error": "'.$citations->error.'", "type":"'.$type.'", "result":'.json_encode($result).'}';
		echo $jsonString;
	}
	else echo '{"error": "no type found"}';
}
else if(isset($_POST['timestamp']) && !empty($_POST['timestamp']))
{	
	// Print form
	printForm();
	
	// Get citation ids by timestamp
	$result_arr = $citations->getCitations_byTimestamp_all($_POST['timestamp']);
	
	$citations_array = $result_arr[0];
	$deleted_count = 0;
	$error_count = 0;
	
	echo "Total citation(s) to be deleted: ".sizeof($citations_array)."<br />";
	
	foreach($citations_array as $one_citation)
	{
		if($citations->delete($one_citation['citation_id'], "DELETE", "", ""))
		{
			$deleted_count++;
			//echo "Deleted: ".$one_citation['citation_id']."<br />";	
		}
		else
		{
			$error_count++;	
			echo "ERROR: ".$one_citation['citation_id']."<br />";
		}
	}
	
	echo "Total citation(s) deleted: ".$deleted_count."<br />";
	echo "Total deletion error(s): ".$error_count."<br />";
	
}
else
{
	printForm();
}

function printForm($citations = "")
{
?>
	<center>
	<form name="web_form" action="admin.php" method="post">
	<p>Delete by timestamp: <input name="timestamp" type="text"/></p>
	<p><input type="button" onclick="document.forms[0].elements['timestamp'].value=''" value="Clear"/>&nbsp;&nbsp;&nbsp;<input type="submit" value="Delete"/></p>
	<p>&nbsp;</p>
	<hr />
	</form>	
	</center>
<?php
}

?>