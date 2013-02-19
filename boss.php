<?php

/****************************
 IU COGS PUBS API CONTROLLER
 Written by: Patrick Craig/pjcraig
 Maintained by:
 Created: Feb 13, 2013
 Changelog:

 ****************************/

// Variables
$citations = Array();
$IDs = Array();
$method;
$result;
$resultAccumulator = Array();
$cmd = "php handler.php ";
$redirect = " > /dev/null 2>&1 &";
$debug;


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $method = "POST ".$_POST['method']." ";
  $citations = explode("\n", $_POST['citations']);
  foreach ($citations as $citation) {
    array_push($resultAccumulator, exec($cmd.$method.'"'.$citation.'"'));  
   } 
  } else {
  if (strpos($_SERVER['REQUEST_URI'], "citation") != false)
    $method = "GET citation ";
  else
    $method = "GET collection ";
  
  $IDs = explode(",", $_GET['IDs']);
  foreach ($IDs as $ID) {
    array_push($resultAccumulator, exec($cmd.$method.$ID));
  }
}
echo stripslashes(json_encode($resultAccumulator));
?>
