<?php
namespace Asar\Utility\Cli;
/**
 * @package Asar
 * @subpackage core
 */
class Command {
  
  private
    $caller,
    $namespace,
    $command,
    $flags = array(),
    $arguments = array();

  function __construct(array $options = array()) {
    foreach (
      array('caller', 'command', 'namespace', 'flags', 'arguments') as $type
    ) {
      $this->setOption($type, $options);
    }
  }
  
  function setOption($type, $options) {
    if (isset($options[$type])) {
      $this->$type = $options[$type];
    }
  }
  
  function getCaller() {
    return $this->caller;
  }
  
  function getNamespace() {
    return $this->namespace;
  }
  
  function getCommand() {
    return $this->command;
  }
  
  function getFlags() {
    return $this->flags;
  }
  
  function getArguments() {
    return $this->arguments;
  }

}
