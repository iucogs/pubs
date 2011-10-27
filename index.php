<?php 
session_start(); 
$document_root = 'pubs/';
require_once($document_root.'lib/constants.php');
require_once($document_root.'lib/mysql_connect.php'); 
require_once($document_root.'lib/adLDAP.php');
?> 

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" ></meta>
<title>Publications<?php if(DB_NAME != "pubs") echo " [".DB_NAME."]";?></title>

<?php
// Variable declarations
$user = "";  // Default username to empty.
$options=array("account_suffix" => EADS_ACCOUNT_SUFFIX,
			   "base_dn" => EADS_BASE_DN,
			   "domain_controllers" => array (EADS_DOMAIN_CONTROLLERS),
			   "ad_username" => EADS_AD_USERNAME,
			   "ad_password" => EADS_AD_PASSWORD
			   );

$ldap=new adLDAP($options);

// Decide CAS authentication type. Windows (HTTP_USER) or Unix ($_SESSION['username'])
if ($_SERVER['SERVER_NAME'] == "cogs.indiana.edu") {
	if(isset($_SERVER['HTTP_USER'])) {
		$user = $_SERVER['HTTP_USER'];
		$_SESSION['username'] = $user;
		$_SESSION['valid'] = true;
	}
	else {
		$user = "";
		$_SESSION['valid'] = false;
	}
}
else {
	if(isset($_SESSION['username']))  // Session "username" has been set in cas.php
	{
		$user = $_SESSION['username'];  // User will be check with DB further below 
	}
	else {
		$user = "";
	}
}

if ($user == "") 
{
	LoggedIn('', $document_root); 
}
else if(is_numeric($user)) // Guest user check. Username returned as number by CAS.
{
	$result=$ldap->user_info($user,array("givenname","sn","samaccountname","mail"));

	if(!empty($result[0]['mail'][0])) 
	{
		$user = $result[0]['mail'][0];
		query_user($user, $document_root);
	}
}
else //Check if user is in our database
{
	query_user($user, $document_root);
}

function query_user($user, $document_root)
{
	$query = "SELECT * FROM users WHERE username='".mysql_real_escape_string($user)."'";
	$result = mysql_query($query);
	if (mysql_num_rows($result) == 1)
	{
		$user_info = mysql_fetch_array($result, MYSQL_ASSOC);
		$user_info['username'] = $user;
		LoggedIn($user_info, $document_root);
	}
	else
	{
		LoggedIn('', $document_root); 
	}	
}

function LoggedIn($user_info, $document_root) 
{
?>


<link href="<?php echo $document_root;?>css/layout.css" rel="stylesheet" type="text/css" />
<!-- CSS for Menu-->
<link rel="stylesheet" type="text/css" href="/yahooapi/2.8.0r4/build/menu/assets/skins/sam/menu.css">
<link rel="stylesheet" type="text/css" href="/yahooapi/2.8.0r4/build/fonts/fonts-min.css" />
<link rel="stylesheet" type="text/css" href="/yahooapi/2.8.0r4/build/autocomplete/assets/skins/sam/autocomplete.css"> 
<link rel="stylesheet" type="text/css" href="/yahooapi/2.8.0r4/build/container/assets/skins/sam/container.css"> 
<link rel="stylesheet" type="text/css" href="/yahooapi/2.8.0r4/build/tabview/assets/skins/sam/tabview.css" />

<style type="text/css">
 
/* Tooltip styles */
.yui-skin-sam .yui-tt .bd {
    position: relative;
    top: 0;
    left: 0;
    z-index: 1;
    color: #000;
    padding: 2px 5px;
    border-color: #D4C237 #A6982B #A6982B #A6982B;
    border-width: 1px;
    border-style: solid;
    background-color: #EEEEEE;
}
</style>


<script type="text/javascript" src="<?php echo $document_root;?>js/validate.js"></script>
<script type="text/javascript" src="<?php echo $document_root;?>js/Ajax.js"></script>
<script type="text/javascript" src="<?php echo $document_root;?>js/Page.js"></script>
<script type="text/javascript" src="<?php echo $document_root;?>js/UserManagement.js"></script>
<script type="text/javascript" src="<?php echo $document_root;?>js/Collections.js"></script>
<script type="text/javascript" src="<?php echo $document_root;?>js/PrintFormats.js"></script>
<script type="text/javascript" src="<?php echo $document_root;?>js/EditLayout.js"></script>
<script type="text/javascript" src="<?php echo $document_root;?>js/SimilarCitations.js"></script>
<script type="text/javascript" src="<?php echo $document_root;?>js/Progress.js"></script>
<script type="text/javascript" src="<?php echo $document_root;?>js/Feedback.js"></script>
<script type="text/javascript" src="<?php echo $document_root;?>js/debug.js"></script>
<!-- Menu source file -->
<!-- Dependencies --> 
<script type="text/javascript" src="/yahooapi/2.8.0r4/build/yahoo-dom-event/yahoo-dom-event.js"></script> 
<script type="text/javascript" src="/yahooapi/2.8.0r4/build/datasource/datasource-min.js"></script>
<script type="text/javascript" src="/yahooapi/2.8.0r4/build/yahoo/yahoo-min.js"></script>  
<!-- Dependency source files -->
<script type="text/javascript" src="/yahooapi/2.8.0r4/build/container/container_core.js"></script>
<script type="text/javascript" src="/yahooapi/2.8.0r4/build/menu/menu.js"></script>
<script type="text/javascript" src="/yahooapi/2.8.0r4/build/dragdrop/dragdrop-min.js"></script> 
<script type="text/javascript" src="/yahooapi/2.8.0r4/build/container/container-min.js"></script> 
<!-- OPTIONAL: Get (required only if using ScriptNodeDataSource) --> 
<script type="text/javascript" src="/yahooapi/2.8.0r4/build/get/get-min.js"></script> 
<!-- OPTIONAL: Connection (required only if using XHRDataSource) --> 
<script type="text/javascript" src="/yahooapi/2.8.0r4/build/connection/connection-min.js"></script> 
<!-- OPTIONAL: Animation (required only if enabling animation) --> 
<script type="text/javascript" src="/yahooapi/2.8.0r4/build/animation/animation-min.js"></script> 
<!-- OPTIONAL: JSON (enables JSON validation) --> 
<script type="text/javascript" src="/yahooapi/2.8.0r4/build/json/json-min.js"></script> 
<!-- Source file --> 
<script type="text/javascript" src="/yahooapi/2.8.0r4/build/autocomplete/autocomplete-min.js"></script> 


<!-- TO-DO FOR PRODUCTION: --> 
<!-- Change all the *.js (easier to debug) file into production version (*-min.js) --> 
<script type="text/javascript" src="/yahooapi/2.8.0r4/build/element/element.js"></script>
<script type="text/javascript" src="/yahooapi/2.8.0r4/build/tabview/tabview.js"></script>

<!--<script type="text/javascript" src="/yahooapi/2.8.0r4/build/element/element-min.js"></script>
<script type="text/javascript" src="/yahooapi/2.8.0r4/build/tabview/tabview-min.js"></script>-->

<script type="text/javascript" src="<?php echo $document_root;?>js/js.js"></script>
<script type="text/javascript" src="<?php echo $document_root;?>js/Cache.js"></script>

<script type="text/javascript">
<?php 
$owner = '';
if (isset($_SESSION['owner']))
{
	$owner = $_SESSION['owner'];
}

$currentCollection = '';
if (isset($_SESSION['currentCollection']))
{
	$currentCollection = $_SESSION['currentCollection'];
}
	
if ($user_info == '')
{
	echo "Page.setSubmitter('','".$document_root."','".$owner."','".$currentCollection."');";
}
else 
{
	echo "Page.setSubmitter('".$user_info['username']."','".$document_root."','".$owner."','".$currentCollection."');";
}

?>
</script>



</head>

<body class="yui-skin-sam" onLoad="Page.initializePanel(); Page.inputMethod(9);">
<div id="welcome"></div>
<div id="layout">
	<div id="top" style="display:none; text-align:left">
    <table style="width:100%"><tr>
    
      <td>
    
    
    <?php
	echo "<table><tr>";
	echo "<td class='pointerhand' onclick='Page.get_faculty_request();'><b>Home</b></td>";	
	echo "</tr></table>";
	?>
     
     </td>
    
    <td  align="right">
	<form name="searchForm">		
	<table><tr>
    <td><b>Search:</b>&nbsp;</td>
    <td>
	<input text="text" size="30" id="search_keywords" name="search_keywords">
	<input type="submit" value="All" onclick="Page.searchCitations_request('all');return false;">
	</td>
    <td>
	<input type="submit" value="Author" onclick="Page.searchCitations_request('author');return false;"> 
	</td>
    <td>
	<input type="submit" value="Title" onclick="Page.searchCitations_request('title'); return false;">
	</td>
    <td>
	<input type="submit" value="Journal" onclick="Page.searchCitations_request('journal'); return false;">
	</td></tr>
	</table>		
	</form>
    </td>
    
   
     
     <td  align="right">    
	 <?php
	echo "<table><tr>";
	
	if ($user_info == "") 
	{
		echo "<td class='pointerhand' onclick='Page.register();'><b>Register</b></td>";
		echo '<td>::</td>';
    	echo "<td> <form action='cas.php'><input type='hidden' id='owner' name='owner' value='' /><input type='hidden' id='currentCollection' name='currentCollection' value='' /><input type='submit' value='Login' onclick='document.getElementById(\"owner\").value=Page.owner;document.getElementById(\"currentCollection\").value=Page.currentCollection;' /></form></td>";		 
	}
	else
	{
		if ($user_info['admin'] == 1)
		{
			echo "<td class='pointerhand' onclick='Page.adminPage();'><b>Admin</b></td>";
			echo '<td>::</td>';
		}
		echo "<td class='pointerhand' onclick='Page.myAccount();'><b>My Account</b></td>";
		echo '<td>::</td>';
		echo '<td><a style="color:black" href="'.$document_root.'logout.php" ><b>Logout</b></a></td>';
	}
	echo "</tr></table>";
	?>
    </td>
     
    
     
     </tr></table>
     
     

    </div>
    
    <div id="home">
 
    </div>
    
    <!-- All div styles in layout.css -->
    <div id="insert"></div>   <!-- Used solely by User Management Page -->
    <div id="options"></div> <!-- Search bar menu and etc. -->
    
    <div id="right_col"></div>   <!-- Right menu -->

    <div id="citations"></div> <!-- Citations listing --> 
    <div id="secondary"></div> <!-- Upload Citations, Paste Citations, Export Citations & Manage Collections listing -->
    
    <div id="panel1"></div> <!-- Panel for error messages -->
    <div id="panel2"></div> <!-- Panel for saving citation -->
 	<div id="panel3"></div> <!-- Panel for similar citations -->
    <div id="panel4"></div> <!-- Panel for loading message -->
    <div id="panel5"></div> <!-- Maybe no longer used -->
</div> <!-- End of layout div -->

</body>
</html>
<?php
}

?>

