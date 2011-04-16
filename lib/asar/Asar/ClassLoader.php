<?php
/**
 * @package Asar
 * @subpackage core
 */
class Asar_ClassLoader {
  
  private $searcher, $includer;
  
  function __construct(
    Asar_FileSearcher_Interface $searcher,
    Asar_FileIncludeManager_Interface $includer
  ) {
    $this->searcher = $searcher;
    $this->includer = $includer;
  }
  
  function getSearcher() {
    return $this->searcher;
  }
  
  function getIncludeManager() {
    return $this->includer;
  }
  
  function load($name) {
    $path = $this->searcher->find(
      str_replace('_', '/', $name) . '.php'
    );
    if (!is_string($path)) {
      return false;
    }
    $this->includer->requireFileOnce($path);
    return true;
  }
}
