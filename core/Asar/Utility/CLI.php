<?php

class Asar_Utility_CLI {
  
  function execute(array $arguments) {
    $exec = $this->interpret($arguments);
    if (is_array($exec) && array_key_exists('command', $exec)) {
      $method = 'task' . Asar_Utility_String::camelCase($exec['command']);
      call_user_func_array(
        array($this, $method), $exec['arguments']
      );
      //$this->taskCreateProjectDirectories($exec['arguments'][0]);
    }
    return 'Asar Web Framework ' . Asar::getVersion();
  }
  
  function interpret(array $args) {
    $caller = array_shift($args);
    $flags = array();
    $arguments = array();
    $is_command_found = false;
    $result = array();
    foreach ($args as $arg) {
      if ($is_command_found) {
        $arguments[] = $arg;
      } elseif (strpos($arg, '--') === 0) {
        $flags[] = substr($arg, 2);
      } else {
        $result['command'] = $arg;
        $is_command_found = true;
      } 
    }
    $result['caller']    = $caller;
    $result['flags']     = $flags;
    $result['arguments'] = $arguments;
    return $result;
  }
  
  function __call($method, $args) {
    echo "\nmethod: $method";
  }
  
  function taskCreateProjectDirectories($root) {
    $project_path = getcwd() . DIRECTORY_SEPARATOR . $root;
    $directories = array(
      $project_path,
      Asar::constructPath($project_path, 'apps'),
      Asar::constructPath($project_path, 'vendor'),
      Asar::constructPath($project_path, 'web'),
      Asar::constructPath($project_path, 'tests'),
      Asar::constructPath($project_path, 'logs')
    );
    foreach ($directories as $directory) {
      mkdir($directory);
    }
  }
}
