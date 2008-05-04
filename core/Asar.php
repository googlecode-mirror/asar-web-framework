<?php

spl_autoload_register(array('Asar', 'loadClass'));

if (!class_exists('Asar_Base', false)) {
	require_once 'Asar/Base.php';
}

	
class Asar {
	private static $version	= '0.0.1pa';
	private static $asarpath = null;
	private static $instance = null;
	private static $apps     = array();
	private static $mode     = 0;
	private static $debug    = null;
	
	const MODE_PRODUCTION    = 0;
	const MODE_DEVELOPMENT   = 1;
	const MODE_TEST          = 2;
	
	
	/*<a href="/Users/Shared/Work/Newsletter/Aside/March 2008/The-Scholar-Ship-Newsletter-Mar08.html" id="" title="The-Scholar-Ship-Newsletter-Mar08">The-Scholar-Ship-Newsletter-Mar08</a>
	function register ($app_name, $client_name = null) {
		$this->apps[$application_name] = self::instantiate($application_name.'_Application');
		
		if (!$client_name && !is_string($client_name)) {
			// Generate a random key if no client_name is given
			$client_name	= time() . substr(md5(microtime()), 0, rand(5, 12));
			$client_class = 'Asar';
		}
		$this->clients[$client_name] = self::instantiate($client_name.'_Client');
		$this->clients_to_apps[$client_name] = $application_name;
	}
	*/
	
	static function reset() {
		self::$apps = array();
		self::clearDebugMessages();
	}
	
	static function getVersion() {
		return self::$version;
	}
	
	static function setAsarPath($path) {
		self::$asarpath = $path;
		set_include_path(get_include_path() . PATH_SEPARATOR . self::$asarpath);
	}
	
	static function setMode($mode) {
		switch ($mode) {
			case self::MODE_DEVELOPMENT:
			case self::MODE_TEST:
				self::$mode = $mode;
				break;
			default:
				self::$mode = self::MODE_PRODUCTION;
		}
		
	}
	
	static function getMode() {
		return self::$mode;
	}
	
	static function debug($name, $message) {
	    if (self::$mode == self::MODE_DEVELOPMENT) {
		    self::$debug[$name] = $message;
	    }
	}
	
	static function getDebugMessages() {
		return self::$debug;
	}
	
	static function clearDebugMessages() {
		self::$debug = null;
	}
	
	/**
	 * Asar Loader
	 * argument must follow naming convention
	 */
	static function loadClass($class) {
		if (class_exists($class, false)) {
			return true;
		}
		$file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
		if (!self::fileExists($file)) {
			self::exception('Asar', "Class definition file for the class $class does not exist.");
			return false;
		} else {
			include_once($file);
			return true;
		}
	}
  
  
  static function start($application_name, Asar_Client $client = null) {
    /**
     * @todo: Remove dependency on existing classes
     */
    // $application_name must be found by ('ApplicationName_Application');
    // using naming convention
    self::$apps[$application_name] = self::instantiate($application_name.'_Application');
    if (!$client) {
    	$client = new Asar_Client_Default;
    	$client->createRequest();
    }
    $client->sendRequestTo(self::$apps[$application_name]);
	
	//echo $req->sendTo(self::$apps[$application_name]);
	//return self::$apps[$application_name];
  }


	/**
	 * Returns the class prefix used for an object.
	 * 
	 * For example, an object instantiated from the class named 'Test_Class_Name'
	 * when passed to this method will return 'Test';
	 *
	 * @return string class prefix
	 * @param object $obj an instance of a PHP class
	 **/
	public static function getClassPrefix($obj)
	{
		$classname = get_class($obj);
		return substr($classname, 0, strpos($classname, '_'));
	}
  
  
  static function instantiate($class_name, array $arguments = array()) {
    $reflector = new ReflectionClass($class_name);
    if ($reflector->isInstantiable()) {
      if (count($arguments)) {
        $obj = $reflector->newInstanceArgs($arguments);
      } else {
        $obj = $reflector->newInstance();
      }
    } elseif ($reflector->hasMethod('instance')) {
      // We're assuming this is Singleton by convention
      // @todo: what if we class didn't follow that convention?
      $instanceMethod = $reflector->getMethod('instance');
      $obj = $instanceMethod->invoke(null);
    } else {
      self::exception('Asar', 'Trying to instantiate the uninstantiable class '.$class_name);
    }
    return $obj;
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
              // @todo add caching for successful searches
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

	static function underscore($str)
	{
	    return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $str));
    }

    static function dash($str)
    {
        return strtolower(preg_replace('/(?<=\\w)([A-Z])/', '-\\1', $str));
    }

    static function camelCase($str)
    {
        return str_replace(' ', '', ucwords(str_replace(array('-', '_'), ' ', $str)));
    }

    static function lowerCamelCase($str)
    {
        $str = self::camelCase($str);
        $str[0] = strtolower($str[0]);
        return $str;
    }
  
  
}

class Asar_Exception extends Exception {}