<?php

namespace Asar\Application;

use \Asar\Application\Finder\Exception as AppFinderException;

class Finder {
  
  function find($app_name) {
    if (
      $dir = ($_dir = $this->findDirIfDefined($app_name, 'Application')) ?
        $_dir : $this->findDirIfDefined($app_name, 'Config')
    ) {
      return $dir;
    }
    throw new AppFinderException(
      "Unable to find the app named '$app_name'. It could be that no " .
      'Application or Config class was defined in the app directory.'
    );
  }
  
  function findDirIfDefined($app_name, $suffix) {
    if (class_exists($suffix_class_name = $app_name . '\\' . $suffix )) {
      $suffix_class = new \ReflectionClass($suffix_class_name);
      return dirname($suffix_class->getFileName());
    }
  }
  
}
