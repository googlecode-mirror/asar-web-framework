<?php

class Asar_Utility_Cli_Command {
  
  private
    $caller,
    $namespace,
    $command,
    $flags = array(),
    $arguments = array();

  function __construct(array $options = array()) {
    if (array_key_exists('caller', $options)) {
      $this->caller    = $options['caller'];
    }
    if (array_key_exists('command', $options)) {
      $this->command   = $options['command'];
    }
    if (array_key_exists('namespace', $options)) {
      $this->namespace = $options['namespace'];
    }
    if (array_key_exists('flags', $options)) {
      $this->flags     = $options['flags'];
    }
    if (array_key_exists('arguments', $options)) {
      $this->arguments = $options['arguments'];
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
