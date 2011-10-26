<?php 
header("Content-Type: application/xml; charset=UTF-8");
require_once("../classes/Faculty.class.php");
echo '<?xml version="1.0" encoding="ISO-8859-1" ?>';
$faculty = new Faculty();
$cdatastart = "<![CDATA[";
$cdataend = "]]>";

function return_xml($faculty, $value)
{
	global $cdatastart;
	global $cdataend;
	
	// General Infos
	$facinfo = $faculty->getFacultyInfo($value);
	
	//echo "facinfo: <br /><pre>";
	//print_r($facinfo);
	//echo "</pre>";
	
	echo "<response>";
		if(!empty($faculty->notify)) echo "<notify>".$cdatastart.$faculty->notify.$cdataend."</notify>";
		if(!empty($faculty->msg)) echo "<debug>".$cdatastart.$faculty->msg.$cdataend."</debug>";
		
		// Faculty Page Info
		echo "<facultyinfo>";
		echo $cdatastart.$value.$cdataend;
		echo "</facultyinfo>";
	
		$key_arr = array("title1", "title2", "title3", "office", "phone", "email", "link1", "link1_title", "education", "professional_experience", "research_interests");
	
		foreach($key_arr as $key)
		{
			echo "<$key>";
			if(!empty($facinfo[0][$key])){
				echo $cdatastart.$facinfo[0][$key].$cdataend;
			}
			echo "</$key>";
		}
		
		// Representative Publications
		echo "<rep_pubs>";
		$result = $faculty->getRepresentativePublications($value);
		for($i = 0; $i < sizeof($result); $i++)
		{
			echo "<entry>";
			echo "<![CDATA[".trim($result[$i]['raw'])."]]>";
			echo "</entry>";
		}
		echo "</rep_pubs>";
	
	echo "</response>";
}

if (isset($GLOBALS['HTTP_RAW_POST_DATA']))
{
	$doc = new DOMDocument();
	$doc->loadXML($GLOBALS['HTTP_RAW_POST_DATA']);
	
	if(($root = $doc->getElementsByTagName("root")->item(0)))
	{	
		if(($value = $doc->getElementsByTagName("faculty")->item(0)))
		{
			//$value = "colallen";
			$value = $doc->getElementsByTagName("faculty")->item(0)->nodeValue;	
			
			return_xml($faculty,$value, false);	
		}
		else if(($value = $doc->getElementsByTagName("save")->item(0)))
		{
			//$value = "colallen";
			$value = $doc->getElementsByTagName("username")->item(0)->nodeValue;
			
			$valid_arr = array("title1","title2","title3","phone","office","email","link1","link1_title","education","professional_experience","research_interests");
			
			$arr = array();
			foreach($valid_arr as $element)
			{
				if($doc->getElementsByTagName($element)->item(0))
					$arr[$element] = $doc->getElementsByTagName($element)->item(0)->nodeValue;
			}
			
			//print_r($arr);
			
			$saved = $faculty->saveFacultyInfo($value, $arr);
			if($saved)
			{
				return_xml($faculty,$value);
			}
			else
			{
				echo "<response><notify>".$cdatastart."Error! $faculty->notify".$cdataend."</notify><debug>".$cdatastart."$faculty->msg".$cdataend."</debug></response>";
			}
		}
		else
		{
			echo "<response></response>";
		}
	}
	else
	{
		echo "<response></response>";
	}
}
else
{
	echo "<response></response>";
}
