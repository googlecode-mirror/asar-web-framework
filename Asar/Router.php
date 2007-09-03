<?php
/**
 * Created on Aug 20, 2007
 * 
 * @author     Wayne Duran
 */
require_once 'Asar.php';

class Asar_Router extends Asar_Base {
  
  private $rules = array();
  private $default_instruction = array('controller', 'action');
  private static $instance = NULL; // For Singleton
  
  static function instance() {
    if (is_null(self::$instance)) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  private function __clone() {}
  
  private function __construct() {}
  
  /**
   * @todo: Better way to do this
   */
  private function parse($instruction, $raw_req_str) {
    $result = array();
    
    // Get the query string and remove the the part after '?'
    $q_start = strrpos($raw_req_str, '?');
    if ($q_start > -1) {
      $raw_req_str = substr($raw_req_str, 0, $q_start);
    }
    
    // Get the 'file extension' (.html, .xml, .rss, .js)
    $type_start = strrpos($raw_req_str, '.');
    if ($type_start > 0) {
      $result['type'] = substr($raw_req_str, $type_start + 1);
      $raw_req_str = substr($raw_req_str, 0, $type_start);
    }
    
    
    $req_items = explode('/', $raw_req_str);
    $length = count($req_items);
    $params = array();
    for ($i = 0; $i < $length; $i++) {
      if (isset($instruction[$i])) {
        switch ($instruction[$i]) {
          case 'controller':
            $result['controller'] = $req_items[$i];
            break;
          case 'action':
            $result['action'] = $req_items[$i];
            break;
        }
      } else {
      	 if (isset($req_items[$i + 1])) {
      	   $params[$req_items[$i]] =  $req_items[$i + 1];
      	   ++$i;
      	 } else {
      	   $params[$req_items[$i]] = NULL;
      	 }
      }
    }
    
    $result['params'] = $params;
    return $result;
  }
  
  function translate($raw_req_str) {
    $result = NULL;
    foreach($this->rules as $signature => $parse_seq) {
  	  if (preg_match($signature, $raw_req_str)) {
  	  	$result = $this->parse($parse_seq, $raw_req_str);
  	  }
    }
    
    if (is_null($result)) {
      // Resort to default rule
      $result = $this->parse($this->default_instruction, $raw_req_str);
    }
    
    return $result;
  }
  
  function importRequest($raw_req_str) {
    $this->translate($_SERVER['REQUEST_URI']);
  }
  
}

class Asar_Router_Exception extends Asar_Base_Exception {}
?>
