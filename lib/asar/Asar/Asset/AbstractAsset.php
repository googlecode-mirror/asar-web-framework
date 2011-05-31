<?php

namespace Asar\Asset;

/**
 * An object representation of a CSS file that might be included in a project
 * as additional resource when rendering HTML.
 */
abstract class AbstractAsset {
  
  protected 
    $path,
    $dependencies,
    $abstract_defaults = array(
      'requires' => array()
    );
  
  function __construct($path, array $options = array()) {
    $this->path = $path;
    $options = array_merge($this->abstract_defaults, $options);
    $this->dependencies = $options['requires'];
  }
  
  abstract function render();
  
  function getDependencies() {
    return $this->dependencies;
  }
  
  function getPath() {
    return $this->path;
  }
  
}
  
