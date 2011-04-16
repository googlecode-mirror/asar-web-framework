<?php
/**
 * @package Asar
 * @subpackage core
 */
require_once 'IncludePathManager.php';

class Asar_Toolset {
  
  private $include_path_manager;
  
  function getIncludePathManager() {
    if (!$this->include_path_manager) {
      $this->include_path_manager = new Asar_IncludePathManager;
    }
    return $this->include_path_manager;
  }
  
}
