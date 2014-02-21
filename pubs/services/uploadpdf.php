<?php

require_once('../lib/constants.php');	// Definition for PDF_DIRECTORY
// Edit upload location here
$destination_path = PDF_DIRECTORY."/".PUBS_VERSION."/temp/"; 
//$filename = tempnam(sys_get_temp_dir(),''); // Create unique temp file in OS temp folder.

// Clear temporary directory of files older than 1 day.
// TO-DO: Clear deleted directory as well.
clear_directory($destination_path);

$result = 0;
$filename = '';

$citation_suffix = $_POST["upload_citation_suffix"];
$citation_id = $_POST["upload_citation_id".$citation_suffix];
$myfile = "myfile".$citation_suffix;

// Generate JSON elements to be sent back.
$elements = array("upload_div" => "upload_div".$citation_suffix, "citation_id" => $citation_id, "citation_suffix" => $citation_suffix);
$elements_json = json_encode($elements);

// For security
str_replace('.', '', $_FILES[$myfile]['name']);
str_replace('/', '', $_FILES[$myfile]['name']);

if (($_FILES[$myfile]["type"] == "application/pdf") && ($_FILES[$myfile]["size"] < 10000000)) // 10,000 kb ~ 9.5 MB 
{
	$uploaded_filename = basename($_FILES[$myfile]["name"], ".pdf"); // Take .pdf off
	
	// Check / Limit filename length
	$uploaded_filename = substr($uploaded_filename, 0, 100);
	$filename = $citation_id."_".$uploaded_filename.".pdf";	// Reinsert file extension.
	
	$target_path = $destination_path . $filename;
	echo $target_path;
	if(@move_uploaded_file($_FILES[$myfile]['tmp_name'], $target_path)) {
		$result = 1;
	}
}
else 
{
	if ($_FILES[$myfile]["type"] != "application/pdf") 
	{
		$result = 2; //  invalid file type.
	}
	else if ($_FILES[$myfile]["size"] >= 10000000) 
	{
		$result = 3;  //File is too big
	}
}

sleep(1);

?>

<script language="javascript" type="text/javascript">window.top.window.stopUpload2(<?php echo $result; ?>,'<?php echo $filename; ?>','<?php echo $elements_json; ?>');</script> 

<?php 

// Functions - Clear temporary directory of files older than 1 day.
function clear_directory($destination_path)
{
	if ($handle = opendir($destination_path)) {
		// The correct way to loop over the directory.
		while (false !== ($cur_temp_file = readdir($handle))) 
		{
			$full_path_name = $destination_path.$cur_temp_file;	
	
			if($cur_temp_file == '.' or $cur_temp_file == '..'){} 	// Skip current folder and previous directory links.
			else if (file_exists($full_path_name)) 
			{
				$diff = time() - filemtime($full_path_name); 		// Difference between now and file modification.
				$mins = floor($diff / 60);
				$hours = floor($diff / 60 / 60);
				$days = $hours / 24; 
			
				if($days > 1)  // Delete any files older than one day.
				{
					unlink($full_path_name);	// Remove temporary file
				}
			}
		}
		closedir($handle);
		return true;
	}
	else return false;
}

?>  