<?php
class RemoveSvnEntries implements \Asar\Utility\Cli\CliInterface {
  
  private $cwd;
  private $controller = null;
  
  function __construct() {
    $this->cwd = getcwd();
  }
  
  function setController(\Asar\Utility\Cli $controller) {
    $this->controller = $controller;
  }
  
  function getTaskNamespace() {
    return '';
  }
  
  function removeSvnDirs($path) {
    foreach (scandir($path) as $file) {
      if (preg_match('/^.{1,2}$/', $file)) {
        continue;
      }
      
      if (preg_match('/\.svn$/', $file)) {
        $fullFilePath = $path . '/' . $file;
        $this->recursiveDelete($fullFilePath);
      }
        
      if (is_dir($path . '/' . $file)) {
        $this->removeSvnDirs($path . '/' . $file);
      }
    }
  }
  
  function recursiveDelete($path) {
    foreach(scandir($path) as $file) {
      if (preg_match('/^.{1,2}$/', $file)) {
        continue;
      }
      if (is_dir($path . '/' . $file)) {
        $this->recursiveDelete($path . '/' . $file);
        continue;
      }
      echo "removing $path/$file \n";
      unlink($path . '/' . $file);
    }
    echo "removing $path \n";
    rmdir($path);
  }
  
  function taskCleanup() {
    $this->removeSvnDirs($this->cwd);
  }
}

