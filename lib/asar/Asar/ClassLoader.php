<?php
namespace Asar;

use \Asar\FileSearcher\FileSearcherInterface;
use \Asar\FileIncludeManager\FileIncludeManagerInterface;

/**
 * @package Asar
 * @subpackage core
 */
class ClassLoader {
  
  private $searcher, $includer;
  
  function __construct(
    FileSearcherInterface $searcher,
    FileIncludeManagerInterface $includer
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
