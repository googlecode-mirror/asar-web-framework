<?php

require_once 'FileIncludeManager/Interface.php';

class Asar_FileIncludeManager implements Asar_FileIncludeManager_Interface {
  
  function requireFileOnce($file) {
    return require_once $file;
  }
  
  function includeFile($file) {
    return include $file;
  }
  
}