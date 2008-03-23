<?php

/**
 * @todo: This class may be following the anti-pattern "BaseBean". Refactor to move methods in different classes?
 * 
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
  
  
  function exception($msg) {
    Asar::exception($this, $msg);
  }

}

class Asar_Base_Exception extends Exception {}
