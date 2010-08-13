<?php

class Asar_EnvironmentHelper_Bootstrap {

  private $class_loader;

  function __construct($class_loader) {
    $this->class_loader = $class_loader;
  }

  function run() {
    $this->loadClassLoader();
  }

  private function loadClassLoader() {
    spl_autoload_register(array($this->class_loader, 'load'));
  }
}