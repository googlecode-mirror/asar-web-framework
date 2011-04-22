<?php
namespace Asar\Utility\Cli;

use \Asar\Utility\Cli;
use \Asar\Utility\Cli\CliInterface;
use \Asar\Utility\ClassFilePeek;

/**
 */
class TaskFileLoader {
  
  private $cwd, $file_peek, $tasks_file, $cli;
  
  function __construct(
    $cwd, ClassFilePeek $file_peek, Cli $cli
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
          if ($obj instanceof CliInterface) {
            $this->cli->register($obj);
          }
        }
      }
    }
  }
  
}
