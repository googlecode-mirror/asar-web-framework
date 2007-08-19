<?php

if (!class_exists('Asar_Base', false)) {
    require_once 'Asar/Base.php';
}


class Asar {
  private static $version  = '0.0.1pa';
  private static $asarpath = NULL;
  
  static function getVersion() {
    return self::$version;
  }
  
  static function setAsarPath($path) {
    self::$asarpath = $path;
    set_include_path(self::$asarpath);
  }
  /**
   * Asar Loader
   * argument must follow naming convention
   */
  static function load($class) {
    
  }
  
  static function createException($classname, $msg) {
  	$exception = $classname.'_Exception';
  	if (class_exists($exception)) {
  	  $ref = new ReflectionClass($exception);
  	  throw $ref->newInstance($msg);
  	} else {
  	  return FALSE;
  	}
  }
  
  static function exception($obj, $msg) {
    $classname = get_class($obj);
    while (FALSE === self::createException($classname, $msg)) {
      $classname = get_parent_class($classname);
      if (!$classname) {
      	break;
      }
    }
    // Resort to throwing Exception by default
    throw new Exception ($msg);
  }
  
  
}
?>