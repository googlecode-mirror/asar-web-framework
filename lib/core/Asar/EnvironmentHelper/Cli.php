<?php

class Asar_EnvironmentHelper_Cli {

  private $cli, $argv, $tasklists = array();

  function __construct($cli, $argv, $tasklists) {
    $this->cli = $cli;
    $this->argv = $argv;
    $this->tasklists = $tasklists;
  }
  
  function run() {
    foreach ($this->tasklists as $tasklist) {
      $this->cli->register($tasklist);
    }
    $this->cli->execute($this->argv);
  }
  
}
