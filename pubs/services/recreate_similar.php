<?php 

require_once('../classes/Citations.class.php');
$citations = new Citations();

$result = $citations->populateSimilarTo();

print_r($result);
?>
 