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
    if (strpos($cmd, 'citation'))
      $cmd .= 'citation_POST ';
    else
      $cmd .= 'newCollection_POST ';


    if($debug){var_dump($_POST);} 
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
        if($debug){echo $cmd."\n"};
        array_push($resultAccumulator, exec($cmd.'"'.$collectionNames[$i].'" "'.$submitters[$i].'" "'.$owners[$i].'"'));
      }  
    }
 
    break;

  // for now we're just adding citations to collection
  case 'PUT':
    // Parsing PUT data... seems like get_file_contents wasn't working, possibly
    // due to enctype.
    $putData = '';
    $fp = fopen('php://input', 'r');
    while (!feof($fp)) {
      $s = fread($fp, 64);
      $putData .= $s;
    }
    fclose($fp);
    parse_str($putData, $putVars);
    
    
    $cmd .= 'addCitationToCollection_PUT ';
    $collectionID =  $putVars['collectionID'];
    $citationIDs = explode(',', $putVars['citationIDs']);
    $submitter = $putVars['submitter'];
    $owner = $putVars['owner'];

    for ($i = 0; $i < sizeof($citationIDs); $i++) {
      if($debug){ echo $cmd; }
      array_push($resultAccumulator, exec($cmd.' '.$collectionID.' '.$citationIDs[$i].' '.$owner.' '.$submitter));  
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
