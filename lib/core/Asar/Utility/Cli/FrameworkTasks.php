<?php

class Asar_Utility_Cli_FrameworkTasks implements Asar_Utility_Cli_Interface {
  
  private $controller, $file_helper, $cwd;
  
  function __construct(Asar_FileHelper $file_helper) {
    $this->file_helper = $file_helper;
  }
  
  function setController(Asar_Utility_Cli $controller) {
    $this->controller = $controller;
    $this->cwd = $this->controller->getWorkingDirectory();
  }
  
  private function out($string) {
    $this->controller->out($string);
  }
  
  private function getFullPath($path) {
    return $this->cwd . DIRECTORY_SEPARATOR . $path;
  }
  
  function taskCreateFile($filename, $contents) {
    try {
      $result = $this->file_helper->create(
        $this->getFullPath($filename), $contents
      );    
      if ($result) {
        $this->out('Created: ' . $filename);
      }
    } catch (Asar_FileHelper_Exception_FileAlreadyExists $e) {
      $this->out('Skipped - File exists: ' . $filename);
    }
  }
  
  function taskCreateDirectory($dir) {
    try {
      $this->file_helper->createDir($this->getFullPath($dir));
      $this->out("Created: $dir");
    } catch (Asar_FileHelper_Exception_DirectoryAlreadyExists $e) {
      $this->out("Skipped - Directory exists: $dir");
    }
  }
  
  function taskCreateFileAndDirectory($filepath, $contents) {
    $this->taskCreateDirectory(dirname($filepath));
    $this->taskCreateFile($filepath, $contents);
  }
  
  function taskCreateProjectDirectories($path) {
    $this->taskCreateDirectory($path);
    $subpaths = array('apps', 'lib', 'lib/vendor', 'web', 'tests', 'logs');
    foreach ($subpaths as $subpath) {
      $this->taskCreateDirectory($path . DIRECTORY_SEPARATOR . $subpath);
    }
  }
  
  
}
