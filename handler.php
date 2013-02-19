<?php 

/************************
 IU COGS PUBS API Worker
 Written by: Patrick Craig/pjcraig
 Maintained by:
 Refactored Jan 22, 2013
 Changelog: 
 2.4.2013 / pjcraig : Testing the new parser.
 2.11.2013 / pjcraig: New parser's working well, though  not getting get or post
                      requests. Need to hand that over to the bossman script.
 ************************/

// Includes
require_once('/home/patrick/Sites/pubs/classes/Citations.class.php');
require_once('/home/patrick/Sites/pubs/classes/Collections.class.php');
require_once('/home/patrick/Sites/parse/NewParse.class.php');

// Variables
$citations;
$collections;
$parse; 
$raw; 
$ID; 
$method; 
$type = null;

/*************************
 POST /citation
 input: JSON string of citation inserted
 output: ID of newly created citation
 *************************/
function citation_POST($raw) {
  global $parse;
  global $timestamp;
  $json;
  $citation_ID;

  $json = $parse->execute($raw, "", "", $timestamp);
  $citation_ID = $parse->parseJSONToDB($json);
  echo $citation_ID;
}    

/*************************
 POST /citation/parse
 input: raw citation string
 output: JSON string containing citation info
 *************************/
function parse_POST($raw) {
  global $parse;
  global $timestamp;
  $json;

  $json = stripslashes(json_encode($parse->execute($raw, "", "", $timestamp)));
  echo $json;
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
function citation_GET($ID) {
  global $citations;
  $citation_json = json_encode($citations->getCitation_byID2($ID)); 
  echo $citation_json;
}

/*************************
 GET /citation/{citationnumber.json}
 input: Number of an existing citation with .json appended
 output: JSON of the citation's entry in the database
 *************************/
function collection_GET($ID) {
  global $collections;
  $collection_json = json_encode($collections->getCollectionByID($ID));
  echo $collection_json;
}

// Actual handling happens here.

// Posting or getting? 
if ($argv[1] == 'POST') { 
  $method = $argv[2];
  $raw = $argv[3];
  $parse = new NewParse();
  $parse->parse();
  $parse->connectDB(DB_NAME, DB_USER, DB_PASSWORD, DB_HOST);
  $timestamp = time();
} else {
  $type = $argv[2];
  $ID = $argv[3];
  $citations = new Citations();
  $collections = new Collections();
}

// If we magically don't get a type, just assume citation. 
// If type isn't assigned, we're posting. 
if ($type) { 
  if ($type == 'collection')
      collection_GET($ID); 
  else 
      citation_GET($ID);
} else {
  if ($method == 'post') 
      citation_POST($raw);
  else if ($method == 'match')
      match_POST($raw);
  else
      parse_POST($raw);
}

?>
