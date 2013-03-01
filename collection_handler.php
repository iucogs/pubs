<?php

/************************
 IU COGS PUBS API Collections Worker
 Written by: pjcraig@indiana.edu
 Maintained by: pjcraig@indiana.edu
 Created/refactored: Feb 27, 2013
 Notes: This worker should handle everything that comes in from the controller
 wrt collections. Args work like this:
 argv[1]: (string) function to be called
 argv[2]: (int) collection ID for GET or for PUTing citations to a collection
 OR (string) the collection name
 argv[3]: (int) citation ID to be posted to collection ID from argv2 OR (string)
 the submitter of the collection (by default, APIUser)
 argv[4]: (string) owner of the new collection

 Changelog: 
 2.4.2013 / pjcraig : Testing the new parser.
 2.11.2013 / pjcraig: New parser's working well, though  not getting get or 
 requests. Need to hand that over to the bossman script.
 2.27.2012 / pjcraig: Split handler into citation_handler and collection_handler
 ************************/


// Include
require_once('/home/patrick/Sites/pubs/classes/Collections.class.php');

// Variables
$collections = new Collections;
$input = $argv[1];

/*************************
 GET /collection?IDs={collection IDs}
 input: Number of an existing collection, via query string
 output: JSON of the collection's entry in the database along with the IDs that
 comprise it
 *************************/
function collection_GET($ID) {
  global $collections;
  $collection_json = json_encode($collections->getCollectionByID($ID));
  echo $collection_json;
}

/*************************
 POST /collection/
 input: (string) Name of new collection, (string) submitter (by default,
 "API User"), (string) owner
 output: JSON: if collection exists, it returns "exists" and the collection ID.
 If not, it returns an array with 1 at [0] and the new collection ID at [1].
 *************************/

function newCollection_POST($collection_name, $submitter, $owner) {
 global $collections;
 $response_json = json_encode($collections->createCollection($collection_name, $submitter, $owner));
}

// We're posting/putting/getting; set up variables accordingly
if (strpos($function, 'POST')) {
  $collection_name = $argv[2];
  if ($argv[3] != 0)
    $submitter = $argv[3];
  else
    $submitter = "API User";
  $owner = $argv[4];
} else {
  $collection_ID = $argv[2];
  $citation_ID = $argv[3];
} 

switch ($function) {
  case "collection_GET":
    collection_get($collection_ID); 
    break;

  case "newCollection_POST":
    newCollection_POST($collection_name, $submitter, $owner);
    break;

}
?>
