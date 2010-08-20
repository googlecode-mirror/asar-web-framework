<?php

class Asar_Router implements Asar_Router_Interface {
  
  private $resource_factory;
  
  function __construct(Asar_ResourceFactory $resource_factory) {
    $this->resource_factory = $resource_factory;
  }
  
  function route($app_name, $path, $map) {
    if (is_array($map) && array_key_exists($path, $map)) {
      $rname = $this->getResourceNamePrefix($app_name) . '_' . $map[$path];
    } else {
      $rname = $this->getNameFromPath($app_name, $path);
    }
    return $this->resource_factory->getResource($rname);
  }
  
  private function getResourceNamePrefix($app_name) {
    return $app_name . '_Resource';
  }
  
  private function getNameFromPath($app_name, $path) {
    $levels = explode('/', ltrim($path, '/'));
    $rname = $this->getResourceNamePrefix($app_name);
    foreach($levels as $level) {
      $test = $rname . '_' . Asar_Utility_String::camelCase($level);
      if (class_exists($test)) {
        $rname = $test;
      } else {
        $rname = $rname .'_Item';
        if (!class_exists($rname)) {
          throw new Asar_Router_Exception_ResourceNotFound;
        }
      }
    }
    return $rname;
  }
}
