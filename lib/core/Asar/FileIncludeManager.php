<?php

require_once 'FileIncludeManager/Interface.php';

class Asar_FileIncludeManager implements Asar_FileIncludeManager_Interface {
  
  private $required_once = array();
  
  function requireFileOnce($file) {
    // TODO: see if this performs better than require_once();
    if (in_array($file, $this->required_once)) {
      return true;
    }
    $this->required_once[] = $file;
    return require $file;
  }
  
  function includeFile($file) {
    return include $file;
  }
  
}