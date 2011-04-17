<?php
namespace Asar\Utility;

use \Asar\Utility\Cli\Interpreter;
use \Asar\Utility\Cli\Executor\ExecutorInterface;
use \Asar\Utility\Cli\CliInterface;
/**
 * @package Asar
 * @subpackage core
 */
class Cli {
  
  private $interpreter, $executor, $cwd;
  
  function __construct(
    Interpreter $interpreter, ExecutorInterface $executor, $cwd
  ) {
    $this->interpreter = $interpreter;
    $this->executor = $executor;
    $this->cwd = $cwd;
  }
  
  function execute(array $arguments) {
    $this->executor->execute($this->interpreter->interpret($arguments));
  }
  
  function register(CliInterface $tasklist) {
    $this->executor->registerTasks($tasklist, $tasklist->getTaskNamespace());
    $tasklist->setController($this);
  }
  
  function getRegisteredTasks() {
    return $this->executor->getRegisteredTasks();
  }
  
  function out($string) {
      echo $string,"\n";
  }
  
  function getWorkingDirectory() {
    return $this->cwd;
  }
  
}
