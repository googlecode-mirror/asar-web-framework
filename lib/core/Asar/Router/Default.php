<?php

class Asar_Router_Default implements Asar_Router_Interface {
  
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
    $ref = count($levels) - 1;
    $class_starts_with = $this->getClassesWithPrefix(
      $app_name, get_declared_classes()
    );
    foreach($levels as $level) {
      $old_rname = $rname;
      $rname = $rname . '_' . Asar_Utility_String::camelCase($level);
      if (class_exists($rname)) {
        continue;
      }
      $class_starts_with = $this->getClassesWithPrefix(
        $old_rname . '_Rt', $class_starts_with
      );
      if (!empty($class_starts_with)) {
        $rname = $class_starts_with[0];
      }
      if (!class_exists($rname)) {
         throw new Asar_Router_Exception_ResourceNotFound;
      }
    }
    return $rname;
  }
  
  
  private function getClassesWithPrefix($prefix, $available_classes) {
    $classes = array();
    foreach ($available_classes as $class) {
      if (strpos($class, $prefix) === 0) {
        $classes[] = $class;
      }
    }
    return $classes;
  }
}
