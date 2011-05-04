<?php
namespace Asar\Utility\Cli;

/**
 */
class EnvironmentScope {
  
  private
    $cwd,
    $argv    = array();
  
  function __construct($cwd, $argv) {
    $this->cwd     = $cwd;
    $this->argv    = $argv;
  }
  
  function getArgv() {
    return $this->argv;
  }
  
  function getCurrentWorkingDirectory() {
    return $this->cwd;
  }
  
}
