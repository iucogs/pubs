<?php  
$q = $_GET['query'];
$field = $_GET['field'];
$pubtype = "";
$authorLNVal = "";
$booktitle = "";
$year = "";
$owner = "";

if (isset($_GET['pubtype'])) 			$pubtype = $_GET['pubtype'];
if (isset($_GET['authorLNVal']))		$authorLNVal = $_GET['authorLNVal'];
if (isset($_GET['year'])) 				$year = $_GET['year']; 
if (isset($_GET['booktitle'])) 			$booktitle = $_GET['booktitle'];
if (isset($_GET['owner']))				$owner = $_GET['owner'];

header("Content-Type: text/xml");  

require_once('../classes/autocomplete.class.php');
$autocomplete = new Autocomplete();

echo $autocomplete->getXML($q, $field, $pubtype, $authorLNVal, $booktitle, $year, $owner);

?>  
