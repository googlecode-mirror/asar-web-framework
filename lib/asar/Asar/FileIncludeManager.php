<?php
namespace Asar;

use \Asar\FileIncludeManager\FileIncludeManagerInterface;

/**
 * @package Asar
 * @subpackage core
 */

class FileIncludeManager implements FileIncludeManagerInterface {
  
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
