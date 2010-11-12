<?php

class Asar_EnvironmentHelper_Cli {

  private $cli, $argv, $tasklists = array(), $task_file_loader;

  function __construct($cli, $argv, $tasklists, $task_file_loader) {
    $this->cli = $cli;
    $this->argv = $argv;
    $this->tasklists = $tasklists;
    $this->task_file_loader = $task_file_loader;
  }
  
  function run() {
    foreach ($this->tasklists as $tasklist) {
      $this->cli->register($tasklist);
    }
    $this->task_file_loader->registerTasks();
    $this->cli->execute($this->argv);
  }
  
}
