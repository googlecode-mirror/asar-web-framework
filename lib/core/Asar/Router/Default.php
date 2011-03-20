<?php

class Asar_Router_Default implements Asar_Router_Interface {
  
  private $resource_factory, $resource_lister, $debug;
  
  function __construct(
    Asar_ResourceFactory $resource_factory,
    Asar_ResourceLister_Interface $resource_lister,
    Asar_Debug $debug = null
  ) {
    $this->resource_factory = $resource_factory;
    $this->resource_lister  = $resource_lister;
    $this->debug = $debug;
  }
  
  function route($app_name, $path, $map) {
    if (is_array($map) && array_key_exists($path, $map)) {
      $rname = $this->getResourceNamePrefix($app_name) . '_' . $map[$path];
    } else {
      if (preg_match('/^\//', $path)) {
        $rname = $this->getNameFromPath($app_name, $path);
      } else {
        $rname = $this->getNameFromClassSuffix($app_name, $path);
      }
    }
    if ($this->debug) {
      $this->debug->set('Resource', $rname);
    }
    return $this->resource_factory->getResource($rname);
  }
  
  private function getResourceNamePrefix($app_name) {
    return $app_name . '_Resource';
  }
  
  private function getNameFromClassSuffix($app_name, $name) {
    $rname = $this->getResourceNamePrefix($app_name) . '_' . $name;
    if (!class_exists($rname)) {
      throw new Asar_Router_Exception_ResourceNotFound;
    }
    return $rname;
  }
  
  /**
   * TODO: Refactor this!
   */
  private function getNameFromPath($app_name, $path) {
    $rname = $this->getResourceNamePrefix($app_name);
    $prefixed_with = $this->resource_lister->getResourceListFor($app_name);
    $count = substr_count($this->getResourceNamePrefix($app_name), '_');
    foreach ($this->getPathSubspaces($path) as $subspace) {
      $count++;
      $old_rname = $rname;
      $rname = $rname . '_' . Asar_Utility_String::camelCase($subspace);
      if (class_exists($rname)) {
        continue;
      }
      $rname = $this->getWildCardTypeRoute(
        $old_rname, $prefixed_with, $rname, $count
      );
      if (!class_exists($rname)) {
        throw new Asar_Router_Exception_ResourceNotFound;
      }
    }
    return $rname;
  }
  
  private function getWildCardTypeRoute($old_rname, $prefix, $rname, $count) {
    $prefix = $this->getClassesWithPrefix($old_rname . '_Rt', $prefix);
    if (!empty($prefix)) {
      foreach ($prefix as $class) {
        if ($count === substr_count($class, '_')) {
          $rname = $class;
        }
      }
    }
    return $rname;
  }
  
  private function getPathSubspaces($path) {
    return explode('/', ltrim($path, '/'));
  }
  
  
  private function getClassesWithPrefix($prefix, $available) {
    $classes = array();
    foreach ($available as $class) {
      if (strpos($class, $prefix) === 0) {
        $classes[] = $class;
      }
    }
    return $classes;
  }
}
