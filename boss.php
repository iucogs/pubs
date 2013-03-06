<?php

/****************************
 IU COGS PUBS API CONTROLLER
 Written by: Patrick Craig/pjcraig
 Maintained by:
 Created: Feb 13, 2013
 Changelog:

 ****************************/

// General variables
$cmd = "php ";              
$debug;                      // Debug flag
$handler;                    // Tells us which handler to use
$function;                   // Function to call in the respective handler
$resultAccumulator = Array();          

// POST variables
$citations = Array();
$collectionNames = Array();
$submitters = Array();
$owners = Array();

// PUT variables
$putVars = Array();
$collectionID;
$citationIDs;

// GET variables
$IDs = Array();
$ID;

// Detects citations request vs collections request
if (strpos($_SERVER['REQUEST_URI'], 'citation'))
  $cmd .= "citation_handler.php ";
else 
  $cmd .= "collection_handler.php ";

// Build exec command, execute, populate resultAccumulator
switch ($_SERVER['REQUEST_METHOD']) {
  case 'POST': 
    $cmd .= $_POST['function']." ";
    
    // We either are posting citations or collections
    if (strlen($_POST['citations'] > 0)) {
      $citations = explode("\n", $_POST['citations']);
      foreach ($citations as $citation) {
        array_push($resultAccumulator, exec($cmd.'"'.$citation.'"'));  
      } 
    } else {
      // Separate out data
      $collectionNames = explode("\n", $_POST['collectionNames']);
      $submitters = explode("\n", $_POST['submitters']);
      $owners = explode("\n", $_POST['owners']);
      
      for ($i = 0; $i < sizeof($collectionNames); $i++) { 
        array_push($resultAccumulator, exec($cmd.'"'.$collectionNames[$i].'" "'.$submitters[$i].'" "'.$owners[$i].'"'));
      }  
    }
 
    break;

  // for now we're just adding citations to collection
  case 'PUT':
    $putVars = parse_str(file_get_contents("php://input"),$putVars);
    $cmd .= $putVars['function']." ";
    $collectionID =  $putVars['collectionID'];
    $citationIDs = explode(',', $putVars['citationIDs']);

    for ($i = 0; $i < sizeof($citationIDs); $i++) {
      array_push($resultAccumulator, exec($cmd.'"'.$collectionID.'" "'.$citationIDs[$i].'"'));  
    }
    
    break;

  case 'GET':
    if (strpos($cmd, 'citation')) 
        $cmd .= 'citation_GET ';
    else
        $cmd .= 'collection_GET ';

    $IDs = explode(",", $_GET['IDs']);
    foreach ($IDs as $ID) {
      if($debug){ echo $cmd.' '.$ID; } 
      array_push($resultAccumulator, exec($cmd.' '.$ID));
    }
    break;
}
echo stripslashes(json_encode($resultAccumulator));
?>
