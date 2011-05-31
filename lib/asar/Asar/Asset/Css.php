<?php

namespace Asar\Asset;

/**
 * An object representation of a CSS file that might be included in a project
 * as additional resource when rendering HTML.
 */
class Css extends AbstractAsset {
  
  private
    $media,
    $defaults = array(
      'media' => 'screen'
    );
  
  function __construct($path, array $options = array()) {
    parent::__construct($path, $options);
    $options = array_merge($this->defaults, $options);
    $this->media = $options['media'];
  }
  
  function render() {
    return '<link rel="stylesheet" type="text/css" '.
      "media=\"{$this->media}\" href=\"/{$this->path}\" />";
  }
  
}

