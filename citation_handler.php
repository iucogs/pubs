<?php

/*****************************
 IU COGS PUBS API Citation Worker
 Written by: pjcraig@indiana.edu
 Maintained by: pjcraig@indiana.edu
 Created/refactored: Feb 27, 2013
 Notes: This worker is supposed to handle everything that comes in from the
 controller. The arguments work like this:
 argv[1]: function to be called
 argv[2]: ID/raw citation string
 
 Changelog: 
 2.4.2013 / pjcraig : Testing the new parser.
 2.11.2013 / pjcraig: New parser's working well, though  not getting get or
 post requests. Need to hand that over to the bossman script.
 2.27.2013 / pjcraig: Split handler into citation_handler and collection_handler
 *****************************/

// Includes
require_once('/home/patrick/Sites/pubs/classes/Citations.class.php');
require_once('/home/patrick/Sites/pubs/classes/Collections.class.php');
require_once('/home/patrick/Sites/parse/NewParse.class.php');

// Variables
$citations;             // For storing citations class wrt GET
$parse;                 // For storing parser class wrt POST
$function = $argv[1];   // The function in the API to be called
$input = $argv[2];      // Either a raw citation string for parsing or a citation ID to get

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
 GET /citation?IDs={citation IDs}
 input: Number of existing citations, delimited with commas if more than one
 output: JSON of the citation's entry in the database
 *************************/
function citation_GET($ID) {
  global $citations;
  $citation_json = json_encode($citations->getCitation_byID2($ID));
  echo $citation_json;
}

// If we're posting, set the parser up, otherwise set up citations object for
// GET.

if (strpos($function, 'POST') != false) {
  $timestamp = time();                                         
  $parse = new NewParse();
  $parse->parse();
  $parse->connectDB(DB_NAME, DB_USER, DB_PASSWORD, DB_HOST); 
} else {
  $citations = new Citations();
}


switch ($function) {
  case "citation_POST":
    citation_post($input);
    break;

  case "parse_POST":
    parse_POST($input);    
    break;

  case "match_POST":
    match_POST($input);
    break;

  case "citation_GET":
    citation_GET($input);
    break;
}


?>


