<?php
/**
 * @package Asar
 * @subpackage core
 */
class Asar_Utility_Cli_TaskFileLoader {
  
  private $cwd, $file_peek, $tasks_file, $cli;
  
  function __construct(
    $cwd, Asar_Utility_ClassFilePeek $file_peek, Asar_Utility_Cli $cli
  ) {
    $this->cwd = $cwd;
    $this->file_peek = $file_peek;
    $this->tasks_file = $this->cwd . DIRECTORY_SEPARATOR . 'tasks.php';
    $this->cli = $cli;
  }
  
  function isFileExists() {
    return file_exists($this->tasks_file);
  }
  
  function registerTasks() {
    if ($this->isFileExists()) {
      // TODO: Write a test for this... include not covered.
      include_once $this->tasks_file;
      $classes = $this->file_peek->getDefinedClasses($this->tasks_file);
      if (is_array($classes)) {
        foreach ($classes as $class) {
          $obj = new $class;
          if ($obj instanceof Asar_Utility_Cli_Interface) {
            $this->cli->register($obj);
          }
        }
      }
    }
  }
  
}
