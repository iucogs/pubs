<?php 

function parseAuthorSearch($str)		// DB Search of author's firstname and lastname.
{
	$words = str_word_count($str, 1);

	if(sizeof($words) >= 2)  // Consists of firstname and lastname (two words)
	{
		if(findExactAuthorMatch($words[0],$words[1]))
		{
			return array("firstname" => $words[0], "lastname" => $words[1]);
		}
		else if(findExactAuthorMatch($words[1],$words[0]))
		{
			return array("firstname" => $words[1], "lastname" => $words[0]);	
		}
		else{}
		
		// Determine first word is firstname or lastname.
		$f_count = countFirstnameLastnameInDB($words[0]);
		$l_count = countFirstnameLastnameInDB($words[1]);
		//echo "<br><br>"; print_r($f_count); print_r($l_count); echo "<br><br>";
		
		if($f_count['lastname'] > $l_count['lastname']) 						// If 1st word has more count in lastname
		{
			return array("firstname" => $words[1], "lastname" => $words[0]); 	// Set 2nd word as firstname
		}
		else if($f_count['lastname'] < $l_count['lastname'])					// If 2nd word has more count in lastname
		{
			return array("firstname" => $words[0], "lastname" => $words[1]); 	// Set 1st word as firstname	
		}
		else
		{
			return false;	
		}
	}
	else
	{
		// Unable to determine author name.
		return false;
	}
}

function countFirstnameLastnameInDB($firstOrLast)
{
	// Determine first word is firstname or lastname.
	$query_firstname = "SELECT COUNT(*) as count FROM authors WHERE firstname='".mysql_real_escape_string($firstOrLast)."'";
	$query_lastname = "SELECT COUNT(*) as count FROM authors WHERE lastname='".mysql_real_escape_string($firstOrLast)."'";
	
	$result1 = mysql_query($query_firstname);
	$count1 = mysql_fetch_assoc($result1);
	$result2 = mysql_query($query_lastname);
	$count2 = mysql_fetch_assoc($result2);
	
	return array("firstname" => $count1['count'], "lastname" => $count2['count']);
}

function findExactAuthorMatch($firstname, $lastname)
{
	$query = "SELECT COUNT(*) FROM authors WHERE lastname='".$lastname."' AND firstname='".$firstname."'";	
	$result = mysql_query($query);
	$count = mysql_fetch_assoc($result);
	if($count == 0) return false;
	else return true;
}

?>