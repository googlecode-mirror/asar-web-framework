<?php

/**
 * TODO: Exception handling
 */
class Asar_Base {

  /*
  protected static $attr_reader   = NULL;
  protected static $attr_writer   = array();
  protected static $attr_accessor = array();
  
  function __call($name, $args) {
    
    if (strpos($name, 'get') === 0) {
      // reader
      
      $attr = self::_underscore(substr($name, 3, strlen($name) - 1));
      // TODO: better array search
      if (in_array($attr, self::$attr_reader) || in_array($attr, self::$attr_accessor)) {
        print ("\n$attr");
        return $this->$attr;
      }
    } elseif (strpos($name, 'set') === 0) {
      // writer
      $attr = self::_underscore(substr($name, 3, strlen($name) - 1));
      // TODO: better array search
      if (in_array($attr, self::$attr_writer) || in_array($attr, self::$attr_accessor)) {
        $this->$attr = $args[0];
      }
    }
  }
  */
  
  
  static function underscore($str) {
    // @todo: Improve regular expression
    return strtolower(preg_replace('/(.)([A-Z])/e', "'\\1'.'_'.strtolower('\\2')", $str));
  }
  
  static function camelCase($str) {
    // @todo: Needs Improvement
    return str_replace(' ', '', ucwords(str_replace(array('-', '_'), ' ', $str)));
    // str_replace('_', '-', $str);
  }
  
  static function lowerCamelCase($str) {
    $str = self::camelCase($str);
    $str[0] = strtolower($str[0]);
    return $str;
  }
  
  function exception($msg) {
    Asar::exception($this, $msg);
  }

}

class Asar_BaseException extends Exception {}

?>