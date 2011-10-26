<?php 
session_start();
session_destroy();

$exp_arr = explode(".", $_SERVER["HTTP_HOST"]);
$pubs_version = $exp_arr[0];
 
$user = '';
//header( 'Location: https://cas.iu.edu/cas/logout' ); 
echo '<center><h3>Thank you for using Publications.<br></h3>';
echo '<p>In order to complete the logout process and to prevent other users from accessing your portal settings, you must close your browser window or <a class="blacklink" href="javascript:window.close();"> click  here</a>.</p>';

echo '<p>To login to Publications again, <a href="http://'.$pubs_version.'.cogs.indiana.edu/index.php">Click here</a>.<br></p>';	
                  
echo '<p>If you would like to completely log out of the Central Authentication System, click below:</p>';
echo '<p align="center"><a href="https://cas.iu.edu/cas/logout" title="CAS Logout"><img src="images/cas-buttons-logout.gif" alt="cas logout" width="78" height="24" border="0"></a></p></center>';
               
?>