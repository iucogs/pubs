<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">-->
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" ></meta>
<!--<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1'>-->
<!--<meta http-equiv="Content-Type" content=" application/xhtml+xml; charset=UTF-8" />-->
<title>Parse.php-DEVELOPMENT</title>
</head>
<body>
<?php 

require_once("Parse.class.php");
$parse = new Parse();
$parse->setOptions(array('html' => true, 'db' => false));
if(isset($_POST['citations']) && !empty($_POST['citations'])){
	$data = stripslashes($_POST['citations']);
	$filename = writeTempFile($data);
	printForm($data);
	$parse->printOptions();
	$parse->execute($filename,"","",time()); 
}
else if(!empty($_GET['file'])){
	printForm();
	$parse->printOptions();
	$parse->execute("samples/".$_GET['file'],"","",time());  			// There's an error in test.txt (preg_match warning)
}
else {
	printForm();
	$parse->printOptions();
	//$parse->execute("samples/default.txt","","",time());  			// There's an error in test.txt (preg_match warning)
	$parse->execute("samples.txt","","",time());  						// There's an error in test.txt (preg_match warning)
}

function writeTempFile($data)
{
	//$filename = tempnam(sys_get_temp_dir(),''); 						// Create unique temp file in OS temp folder
	$filename = "temp.txt";												//$filename = getcwd().DIRECTORY_SEPARATOR.$filename;
	$handle = fopen($filename, 'w') or exit("Unable to open file!"); 	// Check if file exist and open it
	fwrite($handle, $data);
	fclose($handle);
	return $filename;
}

function printForm($citations = "")
{
?>
	<center>
	<form name="web_form" action="parse.php" method="post">
	<p>Insert citation(s) below:</p>
	<p><textarea name="citations" rows="10" cols="100"><?php echo $citations; ?></textarea></p>
	<p><input type="button" onclick="document.forms[0].elements['citations'].value=''" value="Clear"/>
	&nbsp;&nbsp;&nbsp;<input type="submit" value="Parse"/></p>
	<p>&nbsp;</p>
	<hr />
	</form>	
	</center>
<?php
}
?>

</body>
</html>
