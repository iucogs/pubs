<?php 

// To call from another php file:
//
// require_once('Logger.class.php');
// Logger::instance()->log('message');

final class Logger {

  private static $_instance;

  private function __construct() {

  }

  static function instance() {
    if (!isset(self::$_instance)) {
      $c = __CLASS__;
      self::$_instance = new $c;
    }
    return self::$_instance;
  }

  // Prevent users to clone the instance
  public function __clone() {
    throw new Exception('Cannot clone the logger object.');
  }
   
  public function log($message = '') {
    //$filename = BASE_PATH.'/pubs/logs/log.txt';
	$today = date("M j G:i:s - ");   
	$message = $today.$message;
	
	$filename = $_SERVER["DOCUMENT_ROOT"].'/logs/log.txt';
    $file = fopen($filename, 'a+');
    fwrite($file, $message."\r\n");
    fclose($file);
  }
  
  public function clear() {
	$filename = $_SERVER["DOCUMENT_ROOT"].'/logs/log.txt';
	$file = fopen($filename, 'w');
    fwrite($file, "");
    fclose($file);
  }
  
} 

?>