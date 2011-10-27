<?php
session_start();

$currentURL = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

// create a new curl resource
$ch = curl_init();
//Script #2 in process
//This code works running the following:
//Apache/1.3.33 (Unix) PHP/4.3.10 mod_ssl/2.8.22 OpenSSL/0.9.7e
?>
<html> 
<head>
<script language="javascript">
function redirect(){
    window.document.location = 'https://cas.iu.edu/cas/login?cassvc=ANY&casurl=<?php echo $currentURL;?>';
}
</script>
<title>Publications</title>
</head>

<?php
	if (isset($_GET["casticket"])) {
		//set up validation URL to ask CAS if ticket is good
		$_url = 'https://cas.iu.edu/cas/validate';
		$cassvc = 'ANY';  //search kb.indiana.edu for "cas application code" to determine code to use here in place of "appCode"//Allow any type of account to login
		$casurl = $currentURL; //same base URL sent in authentication request in homePage.php//Send back to the login page

		$params = "cassvc=$cassvc&casticket=$_GET[casticket]&casurl=$casurl";
		$urlNew = "$_url?$params";

		// set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $urlNew);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//echo $urlNew;	
		//curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
		//curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, TRUE); 
		curl_setopt ($ch, CURLOPT_CAINFO, "/etc/certificates/cacert.pem");
		// grab URL and pass it to the browser
		$handle = curl_exec($ch);
		if ($handle !== false) {/*echo $handle;*/} else echo curl_error($ch);
	
		// close curl resource, and free up system resources
		curl_close($ch);

		//CAS sending response on 2 lines.  First line contains "yes" or "no".  If "yes", second line contains username (otherwise, it is empty).
		$retArray = explode("\n", $handle);
		$access = trim($retArray[0]);
		$user = trim($retArray[1]);
		//echo "Redirecting...";
		//print_r($retArray);
			
		//send user back to homePage.php with validated username
		if ($access == "yes") 
		{ 
			$_SESSION['username'] = $user;
			if ((isset($_GET['owner'])) && (isset($_GET['currentCollection'])))
			{
				$_SESSION['owner'] = $_GET['owner'];
				$_SESSION['currentCollection'] = $_GET['currentCollection'];
			}
			?>
			<body>
			<form action="<?php echo substr($currentURL,0,strripos($currentURL,"/"))."/index.php";?>" method="post"	name="the_form">
			<input type=hidden name=user VALUE=<?php echo $_SESSION['owner']; ?>>
			</form>
			<script>
				the_form.submit();
			</script>
			</body>  	
			<?php 
		}
	}
	else {
		?>
		<body onLoad="redirect();">
		</body>
		<?php
	}
?>


</html>



