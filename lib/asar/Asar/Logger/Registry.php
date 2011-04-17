<?php
namespace Asar\Logger;

use Asar\Logger\Registry\Exception;
use Asar\Logger\Registry\Exception\UnregisteredNamespace;
use Asar\Logger\DefaultLogger;
use Asar\File;

/**
 * Registers loggers that implement the Asar_Logger_Interface
 *
 * @todo Modify. This looks more like a factory than a logger
 * @package Asar
 * @subpackage core
 */
class Registry {

  static
    $registry = array(),
    $loggers  = array();
  
  public static function register($namespace, $log_file_path) {
    if (!file_exists(dirname($log_file_path))) {
      $dir = dirname($log_file_path);
      throw new Exception(
        "Unable to register logger for 'Namespace1' with log file " .
        "'$log_file_path'. The directory '$dir' does not exist."
      );
    }
    self::$registry[$namespace] = $log_file_path;
    return true;
  }

  public static function getLogger($identifier) {
    $namespace = self::getNamespace($identifier);
    if (!isset(self::$registry[$namespace])) {
      throw new UnregisteredNamespace(
        "The identifier '$namespace' was not found in " .
        "the logger registry."
      );
    }
    if (!isset(self::$loggers[$namespace])) {
      self::$loggers[$namespace] = new DefaultLogger(
        new File(self::$registry[$namespace])
      );
    }
    return self::$loggers[$namespace];
  }
  
  public static function unRegister($namespace) {
    unset(self::$registry[$namespace]);
    unset(self::$loggers[$namespace]);
  }
  
  private static function getNamespace($identifier) {
    if (is_string($identifier)) {
      return $identifier;
    }
    $class_name = explode('_', get_class($identifier));
    return $class_name[0];
  }

}
