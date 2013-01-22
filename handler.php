<?php 

/************************
 IU COGS PUBS API Handler
 Written by: Patrick Craig/pjcraig
 Maintained by:
 Refactored Jan 22, 2013
 ************************/

require_once('/home/patrick/Sites/pubs/classes/Citations.class.php');
require_once('/home/patrick/Sites/pubs/classes/Collections.class.php');
 
$citations = new Citations();
$collections = new Collections();
$path = $_SERVER['REQUEST_URI'];
$raw = $_POST['raw'];

/*************************
 POST /citation/parse
 input: raw citation string
 output: JSON string containing citation info
 *************************/
function parse_POST($raw) {
    
}

/*************************
 POST /citation
 input: raw citation string
 output: ID of newly created citation
 *************************/
function citation_POST($raw) {

}    

/*************************
 POST /citation/match/
 input: raw citation string
 output: JSON of potentially matching citations
 *************************/
function match_POST($raw) {

}

/*************************
 GET /citation/{citationnumber.json}
 input: Number of an existing citation with .json appended
 output: JSON of the citation's entry in the database
 *************************/
function citation_GET($path) {
    global $citations;
    $citation_id = substr($path, 10, -5);	
    $citation_json = json_encode($citations->getCitation_byID2($citation_id)); 
	echo $citation_json;
}

/*************************
 GET /citation/{citationnumber.json}
 input: Number of an existing citation with .json appended
 output: JSON of the citation's entry in the database
 *************************/
function collection_GET($path) {
    global $collections;
    $collection_id = substr($path, 12, -5); 
    $collection_json = json_encode($collections->getCollectionByID($collection_id));
	echo $collection_json;
}

// Actual handling happens here.

if ($_SERVER['REQUEST_METHOD'] === 'GET') { 
  if (strpos($path, 'citation') != false)
      citation_GET($path); 
  else if (strpos($path, 'collection' ) != false)
      collection_GET($path);
  else 
      ;
} else {
  if (strpos($path, 'parse') != false) 
      parse($_POST['raw']);
  else if (strpos($path, 'match') != false)
      match($_POST['raw']);
  else
      citation_post($_POST['raw']);



?>


