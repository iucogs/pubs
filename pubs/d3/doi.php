<?php
/*$response = file_get_contents("http://doi.crossref.org/servlet/query?usr=reberle@indiana.edu&pwd=&format=unixref&qdata=|Proc.%20Natl%20Acad.%20Sci.%20USA|Zhou|94|24|13215|1997|||");


 print_r($response);*/
 
/* $response = file_get_contents("http://doi.crossref.org/search/dois?q=renear+palmer&year=2012");


 print_r($response);*/

 
$temp_array = array('"M. Henrion, D. J. Mortlock, D. J. Hand, and A. Gandy, "A Bayesian approach to star-galaxy classification," Monthly Notices of the Royal Astronomical Society, vol. 412, no. 4, pp. 2286-2302, Apr. 2011.', 'Renear 2012');

$data_string = json_encode($temp_array);
					
                                                                                 
 
$ch = curl_init('http://search.crossref.org/links');                                                                    
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);                                                                  
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
curl_setopt($ch, CURLOPT_HTTPHEADER, array(                                                                          
    'Content-Type: application/json',                                                                                
    'Content-Length: ' . strlen($data_string))                                                                        
);                                                                                                                   
 
$result = curl_exec($ch);
print_r($result);

?>





