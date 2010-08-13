<?php
class RemoveSvnEntries implements Asar_Utility_CLI_Interface {
  
  private $cwd;
  private $controller = null;
  
  function __construct() {
    $this->cwd = getcwd();
  }
  
  function setController(Asar_Utility_CLI $controller) {
    $this->controller = $controller;
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

$cli->register(new RemoveSvnEntries);
var_dump($cli);
