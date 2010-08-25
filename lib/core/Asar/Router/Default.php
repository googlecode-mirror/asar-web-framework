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
    $count = count($levels);
    $i = 0;
    foreach($levels as $level) {
      $i++;
      $old_rname = $rname;
      $rname = $rname . '_' . Asar_Utility_String::camelCase($level);
      if (class_exists($rname) && $i == $count) {
        return $rname;
      }
      $class_starts_with = $this->getClassesWithPrefix($old_rname . '_Rt');
      if (!empty($class_starts_with)) {
        $rname = $class_starts_with[0];
        if ($i == $count) {
          return $class_starts_with[0];
        }
      }
      if (!class_exists($rname)) {
         throw new Asar_Router_Exception_ResourceNotFound;
      }
    }
    return $rname;
  }
  
  private function getClassesWithPrefix($prefix) {
    $classes = array();
    $declared_classes = get_declared_classes();
    foreach ($declared_classes as $class) {
      if (strpos($class, $prefix) === 0) {
        $classes[] = $class;
      }
    }
    return $classes;
  }
}
