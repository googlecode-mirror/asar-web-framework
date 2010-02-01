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
 * @copyright Copyright (c) 2007-2009, Wayne Duran <wayne@asartalo.org>.
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
 * Asar
 *
 * Asar arch-class provides static methods used throughout
 * the framework.
 *
 * @package Asar-Core
 **/
class Asar {
  
  const MODE_PRODUCTION  = 0;
  const MODE_DEVELOPMENT = 1;
  const MODE_DEBUG       = 2;
  
  static private $mode;
  static private $debug = array();                           
  static private $interpreter;
  static private $_tstart;
  
  static function setInterpreter($i) {
    self::$interpreter = $i;
  }
  
  /**
   * Bootstraps the Application for a single Request 
   *
   * @param string application_name name of the application to start
   */
  static function start($application_name) {
    // $application_name must be found by ('ApplicationName_Application');
    // using PEAR naming convention
    if (!self::$interpreter) {
      self::$interpreter = new Asar_Interpreter;
    }
    self::_prepare();
    self::$interpreter->interpretFor(
      self::instantiate($application_name.'_Application')
    );
    self::_cleanUp();
  }
  
  static private function _prepare($value='') {
    if (self::$mode == self::MODE_DEBUG) {
      ob_start();
      self::_setUpDebugMessages();
    }
  }
  
  public function _cleanUp() {
    if (self::$mode == self::MODE_DEBUG) {
      self::_fillDebugMessages();
      echo str_replace(
        '</body>', self::debugOutputHtml() . '</body>',
        ob_get_clean()
      );
    }
  }
  
  static private function _setUpDebugMessages() {
    self::debug('Execution Time', null);
    self::debug('Memory Used', null);
    self::$_tstart = microtime();
  }
  
  static function _fillDebugMessages() {
    self::debug(
      'Execution Time', number_format(microtime() - self::$_tstart, 4) . 'ms'
    );
    self::debug('Memory Used', self::getMemoryUsed());
  }
  
  static function getMemoryUsed() {
    $mem_usage = memory_get_usage(true);
    if ($mem_usage < 1000)
      return $mem_usage."bytes";
    elseif ($mem_usage < 1000)
      return round($mem_usage/1000,2)."KB";
    else
      return round($mem_usage/1000000,2)."MB";
  }
  
  static function setMode($mode) {
    if ( in_array($mode, array(1,2,3), true) ) {
      self::$mode = $mode;
    } else {
      self::$mode = self::MODE_PRODUCTION;
    }
  }
  
  static function getMode() {
    return self::$mode;
  }
  
  static function debug($key, $value) {
    self::$debug[$key] = $value;
  }
  
  static function getDebugMessages() {
    return self::$debug;
  }
  
  static function clearDebugMessages() {
    self::$debug = array();
  }
  
  static function debugOutputHtml() {
    $rows = '';
    foreach (self::$debug as $name => $value) {
      if (is_array($value)) {
        if (is_int(key($value))) {
          $list = '<ul>';
          foreach($value as $val) {
            $list .= "<li>$val</li>";
          };
          $value = "$list</ul>";
        } else {
          $list = '<dl>';
          foreach($value as $key => $val) {
            $list .= "<dt>$key</dt><dd>$val</dd>";
          };
          $value = "$list</dl>";
        }
      }
      
      $rows .= "<tr><th scope=\"row\">$name</th><td>$value</td></tr>";
    }
    return '<table id="asarwf_debug_info">' . 
      '<thead><tr><th scope="col" colspan="2">Debugging Info</th></tr></thead>' .
      "<tbody>$rows</tbody></table>";
  }
  
  /**
   * Asar Class Loader
   * 
   * The class name must follow the PEAR class naming convention:
   * the class named Foo_Bar_Class should be found on the file
   * somewhere in one of the include_paths as 'Foo/Bar/Class.php'
   *
   * @param string classname The name of the class
   */
  static function loadClass($class) {
    if (self::_loadClass($class)) {
      return true;
    } else {
      return false;
    }
  }
  
  private static function _loadClass($class) {
    if (class_exists($class, false)) {
      return true;
    }
    $file = str_replace(
      array('__', '_', '|'), 
      array('_|',DIRECTORY_SEPARATOR, '_'), $class
    ) . '.php';
    if (!self::fileExists($file)) {
      return false;
    } else {
      include_once($file);
      return true;
    }
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
  static function getClassPrefix($obj) {
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
   * @param string class_name the class name of the class to instantiate
   * @param array arguments the arguments needed
   * @return stdclass an instance of the class class_name
   */ 
  static function instantiate($class_name, array $arguments = array()) {
    if (!is_string($class_name)) {
      return null;
    }
    try {
      $reflector = new ReflectionClass($class_name);
    } catch (ReflectionException $e) {
      self::exception('Asar', "Class definition file for the class $class_name does not exist.");
    }
    if ($reflector->isInstantiable()) {
      if (count($arguments)) {
        $obj = $reflector->newInstanceArgs($arguments);
      } else {
        $obj = $reflector->newInstance();
      }
    } elseif ($reflector->hasMethod('instance')) {
      // We're assuming this is Singleton by convention
      // @todo what if the class didn't follow that convention?
      $instanceMethod = $reflector->getMethod('instance');
      $obj = $instanceMethod->invoke(null);
    } else {
      self::exception('Asar', "Asar::instantiate failed. Trying to instantiate the uninstantiable class '$class_name'.");
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
  
  static function getFilePath($file) {
    return self::fileExists($file);
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
  
  static function constructPath() {
    $args = func_get_args();
    array_walk($args, array('self', 'stripEndSlashes'));
    // Join everything and replace occurences of double slashes ('//') with '/'
    return str_replace(
      DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR,
      implode(DIRECTORY_SEPARATOR, $args)
    );
  }
  
  static private function stripEndSlashes(&$string) {
    $string = rtrim($string, '\\/');
  }
  
  static function constructRealPath() {
    $args = func_get_args();
    return realpath(
      call_user_func_array(array('self', 'constructPath'), $args)
    );
  }
  
  static function getFrameworkPath() {
    return realpath(self::constructPath(dirname(__FILE__), '..'));
  }
  
  static function getFrameworkCorePath() {
    return self::constructPath(self::getFrameworkPath(), 'core');
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
  
  static function getVersion() {
    return '0.3';
  }
  
  
}
