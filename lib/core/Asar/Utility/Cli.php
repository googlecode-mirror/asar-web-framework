<?php

class Asar_Utility_Cli {
  
  private $interpreter, $executor, $cwd, $out_first;
  
  function __construct(
    Asar_Utility_Cli_Interpreter $interpreter,
    Asar_Utility_Cli_Executor_Interface $executor,
    $cwd
  ) {
    $this->interpreter = $interpreter;
    $this->executor = $executor;
    $this->cwd = $cwd;
  }
  
  function execute(array $arguments) {
    $this->executor->execute($this->interpreter->interpret($arguments));
  }
  
  function register(Asar_Utility_Cli_Interface $tasklist) {
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
