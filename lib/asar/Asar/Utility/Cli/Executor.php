<?php
namespace Asar\Utility\Cli;

use \Asar\Utility\Cli\Executor\ExecutorInterface;
use \Asar\Utility\Cli\CliInterface;
use \Asar\Utility\Cli\Command;
use \Asar\Utility\Cli\Exception\UndefinedTask;
use \Asar\Utility\String;

/**
 */
class Executor implements ExecutorInterface {
  
  private $tasklists = array(), $tasklists_n = array();
  
  function registerTasks(
    CliInterface $tasklist, $namespace = null
  ) {
    if ($namespace) {
      $this->tasklists_n[$namespace] = $tasklist;
    } else {
      $this->tasklists[] = $tasklist;
    }
  }
  
  function getRegisteredTasks() {
    $list = array();
    $alltasks = array_merge($this->tasklists, $this->tasklists_n);
    foreach ($alltasks as $namespace => $tasklist) {
      $refclass = new \ReflectionClass(get_class($tasklist));
      $methods = $refclass->getMethods();
      foreach ($methods as $method) {
        $name = $method->getName();
        if (strpos($name, 'task') === 0) {
          $n_name = String::dashLowerCase(substr($name, 4));
          $list[] = is_string($namespace) ? "$namespace:$n_name" : $n_name;
        }
      }
    }
    return $list;
  }
  
  function execute(Command $command) {
    $method = 'task' . String::camelCase($command->getCommand());
    $method_called = false;
    if (array_key_exists($command->getNamespace(), $this->tasklists_n)) {
      $method_called = $this->invokeTaskMethod(
        $this->tasklists_n[$command->getNamespace()], $method,
        $command->getArguments()
      );
    } else {
      $tasklistsr = array_reverse($this->tasklists, true);
      foreach ($tasklistsr as $tasklist) {
        $method_called = $this->invokeTaskMethod(
          $tasklist, $method, $command->getArguments()
        );
        if ($method_called) break;
      }
    }
    if ($command->getCommand() && !$method_called) {
      throw new UndefinedTask(
        "The task method '$method' is not defined."
      );
    }
    if ($command->getFlags()) {
      foreach ($command->getFlags() as $flag) {
        $this->executeFlag($flag);
      }
    }
  }
  
  private function executeFlag($flag) {
    $tasklists = $this->getAllTaskLists();
    foreach ($tasklists as $tasklist) {
      $method = 'flag' . String::camelCase($flag);
      $this->invokeTaskMethod($tasklist, $method, array());
    }
  }
  
  private function getAllTaskLists() {
    return array_merge($this->tasklists, $this->tasklists_n);
  }
  
  private function invokeTaskMethod($tasklist, $method, $args) {
    if (method_exists($tasklist, $method)) {
      call_user_func_array(
        array($tasklist, $method), $args
      );
      return true;
    }
    return false;
  }
  
}
