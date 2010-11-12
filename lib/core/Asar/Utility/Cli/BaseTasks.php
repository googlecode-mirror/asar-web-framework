<?php

class Asar_Utility_Cli_BaseTasks implements Asar_Utility_Cli_Interface {
  
  private $controller;
  private $asar = null;
  
  function setController(Asar_Utility_Cli $controller) {
    $this->controller = $controller;
  }
  
  private function getAsar() {
    if (!$this->asar) {
      $this->asar = new Asar;
    }
    return $this->asar;
  }
  
  function flagVersion() {
    $this->out('Asar Web Framework '. $this->getAsar()->getVersion());
  }
  
  function taskList() {
    $tasks = $this->controller->getRegisteredTasks();
    $this->out('Available tasks (' . count($tasks) . '):');
    foreach ($tasks as $task) {
      $this->out(" $task");
    }
  }
  
  private function out($string) {
    $this->controller->out($string);
  }
  
  function getTaskNamespace() {}
  
}
