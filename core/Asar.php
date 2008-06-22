<?php
/**
 * Asar arch-class definition - Asar Web Framework Core
 * 
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to wayne@asartalo.org so we can send you a copy immediately.
 * 
 * @package   Asar-Core
 * @copyright Copyright (c) 2007-2008, Wayne Duran <wayne@asartalo.org>.
 * @since     0.1
 * @version   $Id$
 * @license   http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link      http://code.google.com/p/asar-web-framework
 */
/**
 * Register Asar::loadClass() as autoloader
 *
 */
spl_autoload_register(array('Asar', 'loadClass'));

/**
 * Load the base class
 */
if (!class_exists('Asar_Base', false)) {
	require_once 'Asar/Base.php';
}

/**
 * Asar
 *
 * Asar arch-class provides static methods used throughout
 * the framework.
 *
 * @package Asar-Core
 **/
class Asar {
	/**
	 * Asar Web Framework Version
	 */
	private static $version	= '0.1';
	
	/**
	 * A collection of Applications that the framework
	 * currently handles
	 */
	private static $apps     = array();
	
	/**
	 * The environment mode on which the framework is
	 * currently running. There are 3 possible values:
	 * self::MODE_PRODUCTION, self::MODE_DEVELOPMENT, 
	 * and self::MODE_TEST
	 */
	private static $mode     = 0;
	
	/**
	 * Holds the collection of debug messages passed
	 * throughout the request that will be used later
	 * usually for display.
	 */
	private static $debug    = null;
	
	/**
	 * Production Mode
	 */
	const MODE_PRODUCTION    = 0;
	
	/**
	 * Development Mode. When the environment is set to
	 * Development Mode, the framework allows the display
	 * of debugging data and possibly, in the future,
	 * a lot more...
	 */
	const MODE_DEVELOPMENT = 1;
	
	/**
	 * Testing Mode (borrowed from Rails but isn't special.
	 * Currently, this mode doesn't provide any additional
	 * features.
	 */ 
	const MODE_TEST = 2;
	
	/**
	 * Resets Asar arch Class State
	 *
	 * This method is for testing only and resets all
	 * application and debug messages collections to 
	 * the original state
	 *
	 * @return void
	*/
	static function reset() {
		self::$apps = array();
		self::clearDebugMessages();
	}
	
	/**
	 * Returns the version of the framework
	 * 
	 * @return string the current version of the framework
	 */
	static function getVersion() {
		return self::$version;
	}
	
	/**
	 * Set the Environment Mode
	 *
	 * Sets the environment mode to either Development, 
	 * Production, and Test Mode
	 *
	 * @param int mode the environment mode (Asar::MODE_DEVELOPMENT, Asar::MODE_TEST, or Asar::MODE_PRODUCTION)
	 * @return void
	 */
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
	
	/**
	 * Get the Environment Mode
	 *
	 * @return int the environment mode (Asar::MODE_DEVELOPMENT, Asar::MODE_TEST, or Asar::MODE_PRODUCTION)
	 */
	static function getMode() {
		return self::$mode;
	}
	
	/**
	 * Add a Debug Message
	 *
	 * You can add your debugging messages through this
	 * method. The framework uses this method to add its
	 * own debug messages. When the environment is set
	 * to development mode, these messages are then 
	 * included in the  response and sometimes are displayed
	 * on the page especially for HTML response types.
	 * 
	 * @param string name the debug message name used to identify this message
	 * @param string message the debug message contents
	 * @return void
	 * @todo Move this to a separate class?
	 */
	static function debug($name, $message) {
	    if (self::$mode == self::MODE_DEVELOPMENT) {
		    self::$debug[$name] = $message;
	    }
	}
	
	/**
	 * Get debug Messages
	 * 
	 * @return array An array of debug messages sent through Asar::debug()
	 * @see Asar::debug()
	 */
	static function getDebugMessages() {
		return self::$debug;
	}
	
	/**
	 * Clear the debug messages array
	 *
	 * All debug messages added through Asar::debug before this function
	 * was invoked will be gone.
	 *
	 * @return void
	 * @see Asar::debug()
	 * @see Asar::getDebugMessages()
	 */
	static function clearDebugMessages() {
		self::$debug = null;
	}
	
	/**
	 * Asar Class Loader
	 * 
	 * The class name must follow the Pear class name convention:
	 * the class is named Foo_Bar_Class can be found on the file
	 * somewhere in one of the include_paths as 'Foo/Bar/Class.php'
	 *
	 * @param string classname The class must follow the Pear class naming convention
	 */
	static function loadClass($class) {
		if (self::_loadClass($class)) {
			return true;
		} else {
			self::exception('Asar', "Class definition file for the class $class does not exist.");
			return false;
		}
	}
	
	private static function _loadClass($class) {
		if (class_exists($class, false)) {
			return true;
		}
		$file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
		if (!self::fileExists($file)) {
			return false;
		} else {
			include_once($file);
			return true;
		}
	}
  
	
	/**
	 * Bootstraps the Application for a single Request 
	 *
	 * @param string application_name name of the application to start
	 * @param Asar_Client client client the client to use; will default to Asar_Client_Default
	 * @todo: Remove dependency on existing classes
	 */
	static function start($application_name, Asar_Client $client = null) {
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
	 * An object instantiated from the class named 'Test_Class_Name'
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
  
    /**
	 * Instantiates a class with or without arguments
	 *
	 * This method accepts a class_name and attempts to instantiate
	 * an object of that class with optional arguments. It can also
	 * return an object from a Singleton class (though that class
	 * should have a method named 'instance' that will return the
	 * singleton object). If a class is uninstantiable, this method
	 * will throw an exception.
	 * 
	 * @param string class_name the class name of the class you want to instantiate
	 * @param array arguments the arguments needed
	 * @return stdclass an instance of the class class_name
	 */ 
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
  
  	/**
	 * Checks to see if the file exists on the include path
	 *
	 * @param string file path to file
	 * @return bool wether the file was found on one of the include paths or not
	 */
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

		// did not find it
		return false;
	}
  
	/**
	 * A wrapper method for throwing exceptions
	 *
	 * This method accepts a class_name and a message. When called,
	 * the method attempts to throw an exception with the class name
	 * of that exception being the concatination of the class_name
	 * passed to the method and the string '_Exception'. For example,
	 * if we pass the string 'Foo_Bar' as the first argument, the
	 * method will attempt to throw an exception named
	 * 'Foo_Bar_Exception' if the class exists. If not, it will
	 * return false.
	 *
	 * @param string class_name a name of a php class
	 * @param string message the message for the exception
	 * @return void|bool will throw exception or FALSE if the exception class definition does not exist
	 */
	static function createException($class_name, $message) {
		$exception = $class_name.'_Exception';
		if (self::_loadClass($exception)) {
			throw new $exception($message);
		} else {
			return false;
		}
	}

	static function exception($obj, $msg) {
		if (!is_object($obj) && is_string($obj)) {
			$classname = $obj;
		} else {
			$classname = get_class($obj);
		}
		while (false === self::createException($classname, $msg)) {
			$classname = get_parent_class($classname);
			if (!$classname) {
				break;
			}
		}
		// Resort to throwing Exception by default
		throw new Exception ($msg);
	}
  
}
