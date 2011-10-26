<?php 

error_reporting(E_ALL);
ini_set('display_errors', '1');


// Read from the database and output it as an XML document.
class Autocomplete
{
	var $ruth;
	var $doc;
	var $root;
	var $link;
	var $table;
	var $limit;
	var $msg;
	var $notify;

	function Autocomplete()
	{
		require_once('../lib/mysql_connect.php');
		$this->doc = new DOMDocument();
		$this->formatOutput = true;
		$this->root = array();
		$this->table = "citations";
		$this->limit = "LIMIT 0,200";
		$this->notify = "";
		$this->msg = "";
	}
	
	// Create connection to database.
	function connectDB()
	{
		$link;
		if (!$link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)) {
    		$this->msg .= 'Could not connect to mysql<br />';
			//exit;
		}
		else {
			$this->msg .= 'Connected<br>';
		}
			
		if (!mysql_select_db(DB_NAME, $link)) {
			$this->msg .= 'Could not select database<br />';
			//exit;
		}
		else {
			$this->msg .= 'db selected<br>';
		}
		return $link;
	}
	
	function debug()
	{
		if(empty($this->msg)){$this->msg .= "No Error.<br />";}
		$xml = "<debug>";
		$xml .= "<![CDATA[".$this->msg."]]>";
		$xml .= "</debug>";
		return $xml;
	}
	
	function notify()
	{
		//if(empty($this->notify)){$this->notify = "No";}
		$xml = "<notify><![CDATA[";
		$xml .= $this->notify;
		$xml .= "]]></notify>";
		return $xml;
	}
	
	function getXML($str, $field, $pubtype, $authorLNVal, $booktitle, $year, $owner)
	{
		$typeOfField = 0;  
		$this->link = $this->connectDB();
		
		$query = '';
		if (($field == 'author0ln') || ($field == 'author1ln') || ($field == 'author2ln') || ($field == 'author3ln')|| ($field == 'author4ln') || ($field == 'author5ln'))  
		{
			$query = "SELECT DISTINCT lastname, firstname, author_id FROM authors WHERE lastname LIKE '".$str."%' ORDER BY lastname ASC $this->limit";
			$typeOfField = 1; //author lastname
		}
		else if (($field == 'author0fn') || ($field == 'author1fn') || ($field == 'author2fn') || ($field == 'author3fn')|| ($field == 'author4fn') || ($field == 'author5fn'))  
		{
			$query = "SELECT DISTINCT firstname FROM authors WHERE authors.lastname LIKE '".$authorLNVal."' ORDER BY firstname ASC $this->limit";
			$typeOfField = 2; //author firstname
		}
		else if ($field == 'collection_name') 
		{
			$query = "SELECT DISTINCT collection_name FROM collections WHERE collections.collection_name LIKE '".$str."%' AND collections.owner='".$owner."' ORDER BY collection_name ASC $this->limit";
			$typeOfField = 3; //collection
		}
		else if ($field == 'editor') 
		{
			if (($booktitle != '') && ($year != ''))
			{
			
				// Combine all verified authors into a unified string based on booktitle and year of edited_books
				$query = "SELECT CONCAT(IFNULL(author0,''),IFNULL(author1,''),IFNULL(author2,''),";
				$query .= "IFNULL(author3,''),IFNULL(author4,''),IFNULL(author5,'')) FROM (";
				$query .= "SELECT * FROM ( citations c "; 
				for($i = 0; $i < 6; $i++) { 
					$query .= "LEFT JOIN ( SELECT ao.citation_id, CONCAT(a.lastname,',',a.firstname,'; ') AS author".$i." "; 
					$query .= "FROM author_of ao, authors a WHERE ao.position_num =".($i+1)." ";
					$query .= "AND a.author_id = ao.author_id ) ";
					$query .= "a".$i." ON a".$i.".citation_id = c.citation_id ";
				}
				$query .= ")) AS c ";
				$query .= "WHERE c.pubtype = 'edited_book' AND c.verified = 1 ";
				$query .= "AND c.title LIKE '%".$booktitle."%' AND c.year='".$year."' ";
			}
			
		}
		else if ($field == 'publisher') 
		{
			$query = "SELECT publisher FROM publishers WHERE publisher LIKE '".$str."%' ORDER BY publisher ASC $this->limit";		
		}
		else if($field == 'journal') 
		{
			$query = "SELECT name FROM journals WHERE name LIKE '".$str."%' ORDER BY name ASC $this->limit";	
		}
		else 
		{
			$query = "SELECT DISTINCT ".$field." FROM $this->table WHERE ".$field." LIKE '%".$str."%' ORDER BY ".$field." ASC $this->limit";
		}
		
		$xml = '<?xml version="1.0" encoding="ISO-8859-1" ?>';
		$xml .= '<citations>';
		
		if ($query == '')
		{
			return $xml.'</citations>';
		}
		$result = mysql_query($query, $this->link);
		$this->mysql_error_check($result);		
		
		
		while($row = mysql_fetch_array($result, MYSQL_ASSOC))
        {
			if  ($typeOfField == 1)
			{
				$author_id = $row['author_id'];
			}
			
            $xml .= '<citation>';
            $keys = array_keys($row);
				if ($typeOfField == 1)
				{
					$xml .= '<theMenuItem><![CDATA['.trim($row[$keys[0]]).', '.trim($row['firstname']).']]></theMenuItem>';
					$xml .= '<'.$field.'><![CDATA['.trim($row[$keys[0]]).']]></'.$field.'>';
					$xml .= '<author'.substr($field,6,1).'fn><![CDATA['.trim($row['firstname']).']]></author'.substr($field,6,1).'fn>';
					$xml .= '<author'.substr($field,6,1).'id><![CDATA['.trim($row['author_id']).']]></author'.substr($field,6,1).'id>';
				}
				else 
				{
					$xml .= '<theMenuItem><![CDATA['.trim($row[$keys[0]]).']]></theMenuItem>';
					$xml .= '<'.$field.'><![CDATA['.trim($row[$keys[0]]).']]></'.$field.'>';
			//		print_r($xml);
				}
			
			
	  		$xml .= '</citation>';
	  
			//*****************
			// Suggested citations
			
			if ($typeOfField == 1) 
			{
				$query_citation = "SELECT a.*, ao.position_num, c.* FROM authors a, author_of ao, citations c WHERE a.author_id = '$author_id' AND ao.author_id = '$author_id' AND c.citation_id = ao.citation_id ";
				if ($pubtype != "unspecified") 
				{
					$query_citation .= "AND c.pubtype = '$pubtype' ";
				}
				$query_citation .= "ORDER BY ao.position_num LIMIT 0,200";
				$result_citation = mysql_query($query_citation, $this->link);
				$this->mysql_error_check($result_citation);
			
			
				while($row2 = mysql_fetch_array($result_citation, MYSQL_ASSOC))
        		{
		        	$xml .= '<citation><theMenuItem><![CDATA[-----'.$row2['year'].', '.$row2['title'].']]></theMenuItem>';
	//			$xml .= '<author0ln>'.$row2['lastname'].'</author0ln>';
	//			$xml .= '<author0fn><![CDATA['.trim($row2['firstname']).']]></author0fn>';
            		$keys2 = array_keys($row2);
            		for ($i=0; $i < count($keys2); $i++) {
					/***/
					if($keys2[$i] == 'author')
					{			
						$citation_id = 	$row2['citation_id'];
						$query_author = "SELECT a.*, ao.position_num FROM authors a, author_of ao, citations c 
										WHERE c.citation_id = '$citation_id' AND ao.citation_id = '$citation_id' AND a.author_id = ao.author_id ORDER BY ao.position_num";
						$result_author = mysql_query($query_author, $this->link);
						$this->mysql_error_check($result_author);
					
						$temp_xml = array_fill(0, 6, '');
					
						while($row_author = mysql_fetch_array($result_author, MYSQL_ASSOC))
						{
							$pos_num = $row_author['position_num'] - 1;

							if($pos_num < 6)
							{
								$keys_author = array_keys($row_author);
	
								$temp_xml[$pos_num] .= '<author'.$pos_num.'id><![CDATA['.trim($row_author[$keys_author[0]]).']]></author'.$pos_num.'id>';
								$temp_xml[$pos_num] .= '<author'.$pos_num.'ln><![CDATA['.trim($row_author[$keys_author[1]]).']]></author'.$pos_num.'ln>';
								$temp_xml[$pos_num] .= '<author'.$pos_num.'fn><![CDATA['.trim($row_author[$keys_author[2]]).']]></author'.$pos_num.'fn>';
							}
						}
	
						for($j = 0; $j < 6; $j++)
						{
							if(empty($temp_xml[$j]))
							{
								$temp_xml[$j] .= '<author'.$j.'id><![CDATA[]]></author'.$j.'id>';
								$temp_xml[$j] .= '<author'.$j.'ln><![CDATA[]]></author'.$j.'ln>';
								$temp_xml[$j] .= '<author'.$j.'fn><![CDATA[]]></author'.$j.'fn>';
							}
						}
					
						for($j = 0; $j < 6; $j++)
						{
							$xml .= $temp_xml[$j];
						}
					
						
					}
					/***/
						$xml .= '<'.$keys2[$i].'><![CDATA['.trim($row2[$keys2[$i]]).']]></'.$keys2[$i].'>';;
							
				}
				$xml .= '</citation>';
			}
			
			mysql_free_result($result_citation);
			}
		
			//******************
        }
		
        $xml .= '</citations>';
	
		return $xml;
	}
	
	function mysql_error_check($result)
	{
		if (!$result) {
			$this->msg .= "DB Error, could not query the database<br />";
			$this->msg .= 'MySQL Error: ' . mysql_error() . '<br />';
			//exit;
		}
	}
}	

?>
