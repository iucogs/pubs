<?php 

require_once('/home/patrick/Sites/pubs/classes/Citations.class.php');
require_once('/home/patrick/Sites/pubs/classes/Collections.class.php');
 
$citations = new Citations();
$collections = new Collections();
$path = $_SERVER['REQUEST_URI'];



// Takes the path, substrings the citation ID out, writes it to a file.

function citation_handler($path) {
    global $citations;
    $citation_id = substr($path, 10, -5);	
    $citation_json = json_encode($citations->getCitation_byID2($citation_id)); 
    $json_file = fopen("/home/patrick/Sites/citation/".$citation_id.".json", "w");
    fwrite($json_file, $citation_json);
    fclose($json_file);

    return $json_file;
}

// Pretty much the exact same thing

function collection_handler($path) {
    global $collections;
    $collection_id = substr($path, 12, -5); 
    $collection_json = print_r($collections->getCollectionByID($collection_id));
    $json_file = fopen("/home/patrick/Sites/collection/".$collection_id.".json", "w");
    fwrite($json_file, $collection_json);
    fclose($json_file);
   
    return $json_file;

}

 
if (strpos($path, 'citation') != false)
    citation_handler($path); 
else if (strpos($path, 'collection') != false)
    collection_handler($path);
else 
    ;




?>


