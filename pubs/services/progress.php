<?php 

session_start();
if(!isset($_SESSION['progress'])) $_SESSION['progress'] = array("", 0, 0);

// Process session array here.
$session_arr = $_SESSION['progress'];
$html = "";
if($session_arr[0] == "parse")
{
	$html = "&gt; Parsing citation ".$session_arr[1]." of ".$session_arr[2].".";
	$html .= "<br />Searching for similar citations.";
	$html .= "<br />Updating collections.";
	echo $html;
}
else if($session_arr[0] == "similar_to")
{
	$html = "Parsing: Completed.";
	$html .= "<br /> &gt; Searching for similar citations.";
	$html .= "<br />Updating collections.";
	echo $html;
}
else if($session_arr[0] == "update_collection")
{
	$html = "Parsing: Completed.";
	$html .= "<br />Searching for similar citations: Completed.";
	$html .= "<br /> &gt; Updating collections.";
	echo $html;
}
else {
	echo "SESSION: ".$_SESSION['progress'];
}

?>