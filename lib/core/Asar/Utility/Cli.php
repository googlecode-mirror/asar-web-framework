<?php

class Asar_Utility_CLI {
  
  private static $instance = null;
  private $tasklists = array();
  
  private function __construct() {}
  
  private function __clone() {}
  
  static function instance() {
    if (!self::$instance) {
      self::$instance = new self;
    }
    return self::$instance;
  }
  
  function register(Asar_Utility_CLI_Interface $tasklist, $namespace = null) {
    if ($namespace) {
      $this->tasklists[$namespace] = $tasklist;
    } else {
      $this->tasklists[] = $tasklist;
    }
    $tasklist->setController($this);
  }
  
  function execute(array $arguments) {
    $exec = $this->interpret($arguments);
    if (is_array($exec) && array_key_exists('command', $exec)) {
      $method = 'task' . Asar_Utility_String::camelCase($exec['command']);
      $method_called = false;
      if (isset($exec['namespace']) && array_key_exists($exec['namespace'], $this->tasklists)) {
        $method_called = $this->invokeTaskMethod(
          $this->tasklists[$exec['namespace']], $method, $exec['arguments']
        );
      } else {
        $tasklistsr = array_reverse($this->tasklists, true);
        foreach ($tasklistsr as $tasklist) {
          $method_called = $this->invokeTaskMethod(
            $tasklist, $method, $exec['arguments']
          );
          if ($method_called) break;
        }
      }
      if (!$method_called) {
        throw new Asar_Utility_CLI_Exception_UndefinedTask(
          "The task method '$method' is not defined."
        );
      }
    }
    if ($exec['flags'] && array_search('version', $exec['flags']) !== FALSE) {
      echo 'Asar Web Framework ' . Asar::getVersion();
    }
  }
  
  function invokeTaskMethod($tasklist, $method, $args) {
    if (method_exists($tasklist, $method)) {
      call_user_func_array(
        array($tasklist, $method), $args
      );
      return true;
    }
    return false;
  }
  
  function interpret(array $args) {
    $result = array();
    $result['caller'] = array_shift($args);
    $flags = array();
    $arguments = array();
    $is_command_found = false;
    foreach ($args as $arg) {
      if ($is_command_found) {
        $arguments[] = $arg;
      } elseif (strpos($arg, '--') === 0) {
        $flags[] = substr($arg, 2);
      } else {
        $colon = strpos($arg, ':');
        if ($colon > 0) {
          $result['namespace'] = substr($arg, 0, $colon);
          $result['command'] = substr($arg, $colon + 1);
        } else {
          $result['command'] = $arg;
        }
        $is_command_found = true;
      } 
    }
    $result['flags']     = $flags;
    $result['arguments'] = $arguments;
    return $result;
  }
  

}
