<?php

namespace Asar\Asset;

/**
 * An object representation of a CSS file that might be included in a project
 * as additional resource when rendering HTML.
 */
class Js extends AbstractAsset {
  
  function __construct($path, array $options = array()) {
    parent::__construct($path, $options);
  }
  
  function render() {
    return '<script type="text/javascript" ' .
      "src=\"/{$this->path}\">" . '</script>';
  }
  
}

