<?php
namespace Asar;

/**
 * @package Asar
 * @subpackage core
 */
require_once 'IncludePathManager.php';

class Toolset {
  
  private $include_path_manager;
  
  function getIncludePathManager() {
    if (!$this->include_path_manager) {
      $this->include_path_manager = new IncludePathManager;
    }
    return $this->include_path_manager;
  }
  
}
