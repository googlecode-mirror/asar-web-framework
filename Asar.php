<?php

spl_autoload_register(array('Asar', 'loadClass'));

if (!class_exists('Asar_Base', false)) {
  require_once 'Asar/Base.php';
}


class Asar {
  private static $version  = '0.0.1pa';
  private static $asarpath = NULL;
  private static $instance = NULL;
  private static $apps            = array();
  private static $clients         = array();
  private static $clients_to_apps = array();
  private static $last_client = NULL;
  
  
  /*
  function register ($app_name, $client_name = NULL) {
    $this->apps[$application_name] = self::instantiate($application_name.'_Application');
    
    if (!$client_name && !is_string($client_name)) {
      // Generate a random key if no client_name is given
      $client_name  = time() . substr(md5(microtime()), 0, rand(5, 12));
      $client_class = 'Asar';
    }
    $this->clients[$client_name] = self::instantiate($client_name.'_Client');
    $this->clients_to_apps[$client_name] = $application_name;
  }
  */
  
  static function reset() {
    self::$apps            = array();
    self::$clients         = array();
    self::$clients_to_apps = array();
  }
  
  static function getVersion() {
    return self::$version;
  }
  
  static function setAsarPath($path) {
    self::$asarpath = $path;
    set_include_path(get_include_path() . PATH_SEPARATOR . self::$asarpath);
  }
  /**
   * Asar Loader
   * argument must follow naming convention
   */
  static function loadClass($class) {
    if (class_exists($class, false)) {
      return true;
    }
    $file = str_replace('_', '/', $class) . '.php';
    if (!self::fileExists($file)) {
      self::exception('Asar', "Class definition file for the class $class does not exist.");
      return false;
    } else {
      include_once($file);
      return true;
    }
  }
  
  static function start($application_name, Asar_Client $client = NULL) {
    /**
      * @todo: Remove dependency on existing classes
      */
    // $application_name must be found by ('ApplicationName_Application');
    // using naming convention
    self::$apps[$application_name] = self::instantiate($application_name.'_Application');
    
    if (!$client) {
      // Create a default client if no client is provided
      $client = self::instantiate('Asar_Client');
    }
    
    try {
      $client_name = $client->getName();
    } catch (Asar_Client_Exception $e) {
      $client->setName(time() . substr(md5(microtime()), 0, rand(5, 12)));
      $client_name = $client->getName();
    }
    self::$clients[$client_name] = $client;
    self::$last_client = $client;
    self::$clients_to_apps[$client_name] = $application_name;
  }
  
  static function getLastClientLoaded() {
    if (is_null(self::$last_client)) {
      self::exception('Asar', 'No client was loaded');
    } else {
      return self::$last_client;
    }
  }
  
  static function getClient($client_name) {
    if (self::isClientExists($client_name)) {
      return self::$clients[$client_name];
    } else {
      self::exception('Asar', "The client name $client_name passed to Asar::getClient() does not exist");
    }
  }
  
  static function getAppWithClient($client_name) {
    if (self::isClientExists($client_name)) {
      return self::$apps[self::$clients_to_apps[$client_name]];
    } else {
      self::exception('Asar', "The client name $client_name passed to Asar::getAppWithClient() does not exist");
    }
  }
  
  private static function isClientExists($client_name) {
    if (array_key_exists($client_name, self::$clients)) {
      return true;
    } else {
      return false;
    }
  }
  
  static function instantiate($class_name, array $arguments = array()) {
    $reflector = new ReflectionClass($class_name);
    if ($reflector->isInstantiable()) {
      if (count($arguments)) {
        $obj = $reflector->newInstanceArgs($arguments);
      } else {
        $obj = $reflector->newInstance();
      }
      return $obj;
    } else {
      self::exception('Asar', 'Trying to instantiate the uninstantiable class '.$class_name);
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
  	if (class_exists($exception, false)) {
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