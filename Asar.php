<?php

spl_autoload_register(array('Asar', 'loadClass'));

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
  static function loadClass($class) {
    $file = str_replace('_', '/', $class) . '.php';
    print $file;
    if (!self::fileExists($file)) {
      self::exception('Asar', "Class definition file for the class $class does not exist.");
      return false;
    } else {
      include_once($file);
      return true;
    }
  }
  
  static function fileExists($file) {
      // no file requested?
      $file = trim($file);
        if (! $file) {
            return false;
        }
        
        // using an absolute path for the file?
        // dual check for Unix '/' and Windows '\',
        // or Windows drive letter and a ':'.
        $abs = ($file[0] == '/' || $file[0] == '\\' || $file[1] == ':');
        if ($abs && file_exists($file)) {
            return $file;
        }
        
        // using a relative path on the file
        $path = explode(PATH_SEPARATOR, ini_get('include_path'));
        foreach ($path as $base) {
            // strip Unix '/' and Windows '\'
            $target = rtrim($base, '\\/') . DIRECTORY_SEPARATOR . $file;
            if (file_exists($target)) {
                return $target;
            }
        }
        
        // never found it
        return false;
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
    if (!is_object($obj) && is_string($obj)) {
      $classname = $obj;
    } else {
      $classname = get_class($obj);
    }
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

class Asar_Exception extends Exception {}
?>