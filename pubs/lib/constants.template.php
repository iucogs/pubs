<?php 

DEFINE ('PARSE_VERSION','parse');		// Which version (folder name) of parse to use. parsetest | parsedev | parse .
DEFINE ('DB_USER', '');
DEFINE ('DB_PASSWORD', '');
DEFINE ('DB_HOST', 'localhost');			// Local, if remote add :3306
DEFINE ('DB_NAME', 'pubs');
DEFINE ('DB_FLAGS', 'MYSQL_CLIENT_SSL');	// Use this flag for remote connection
DEFINE ('FUZZY_MATCH_RATIO', 0.5);
DEFINE ('PDF_DIRECTORY', $_SERVER["DOCUMENT_ROOT"].'/pubspdf');

$currentDirectory = explode("/", str_replace($_SERVER["DOCUMENT_ROOT"]."/", '', getcwd()));
DEFINE ('PUBS_VERSION', $currentDirectory[0]);

DEFINE ('EADS_ACCOUNT_SUFFIX', '@eads.iu.edu');
DEFINE ('EADS_BASE_DN', 'OU=Accounts,DC=eads,DC=iu,DC=edu');
DEFINE ('EADS_DOMAIN_CONTROLLERS', 'eads.iu.edu');
DEFINE ('EADS_AD_USERNAME', ''); // ID Number
DEFINE ('EADS_AD_PASSWORD', '');

DEFINE ('ADS_ACCOUNT_SUFFIX', '@ads.iu.edu');
DEFINE ('ADS_BASE_DN', 'OU=Accounts,DC=ads,DC=iu,DC=edu');
DEFINE ('ADS_DOMAIN_CONTROLLERS', 'ads.iu.edu');
DEFINE ('ADS_AD_USERNAME', ''); // IU username 
DEFINE ('ADS_AD_PASSWORD', ''); // IU passphrase

?>
