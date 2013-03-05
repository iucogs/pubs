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
$function = $argv[1];

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

function newCollection_POST($collectionName, $submitter, $owner) {
 global $collections;
 echo $response_json = json_encode($collections->createCollection($collection_name, $submitter, $owner));
}

function collection_POST($collectionID, $citationID, $submitter, $owner){
  global $collections;
  $citationIDs[0] = $citationID;
  echo $response_json = stripslashes(json_encode($collections->insert_member_of_collection($collectionID, $citationIDs, $submitter, $owner)));
}

switch ($function) {
  case "collection_GET":
    $collectionID = $argv[2];
    collection_get($collectionID); 
    break;

  case "newCollection_POST":
    $collectionName = $argv[2];
    $submitter = $argv[3];
    $owner = $argv[4];
    newCollection_POST($collectionName, $submitter, $owner);
    break;

  case "collection_POST":
    $collectionID = $argv[2];
    $citationID = $argv[3];
    if (strlen($argv[4]))
      $submitter = $argv[4];
    else
      $submitter = "API user";
    $owner = $argv[4];
    collection_POST($collectionID, $citationID, $submitter, $owner);
    break;

}
?>
