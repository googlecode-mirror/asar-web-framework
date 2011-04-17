<?php

namespace Asar\Utility\Cli;

use \Asar\Utility\Cli\CliInterface;
use \Asar\Utility\Cli;


/**
 * @package Asar
 * @subpackage core
 */
class BaseTasks implements CliInterface {
  
  private $controller;
  private $asar = null;
  
  function setController(Cli $controller) {
    $this->controller = $controller;
  }
  
  private function getAsar() {
    if (!$this->asar) {
      $this->asar = new \Asar;
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
