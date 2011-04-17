<?php

namespace Asar\EnvironmentHelper;
/**
 * A bootstrap environment helper for setting up a class loader and registering
 * it.
 *
 * @package Asar
 * @subpackage core
 */
class Bootstrap {

  private $class_loader;
  
  /**
   * @param callback $class_loader
   */
  function __construct($class_loader) {
    $this->class_loader = $class_loader;
  }

  /**
   * Registers the $class_loader
   */
  function run() {
    $this->loadClassLoader();
  }

  private function loadClassLoader() {
    spl_autoload_register(array($this->class_loader, 'load'));
  }
}
