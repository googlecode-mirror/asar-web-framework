<?php
namespace Asar\Router;

use \Asar\Router\RouterInterface;
use \Asar\Router\Exception\ResourceNotFound;
use \Asar\ResourceFactory;
use \Asar\ResourceLister\ResourceListerInterface;
use \Asar\Debug;
use \Asar\Utility\String;

/**
 */
class DefaultRouter implements RouterInterface {
  
  private $resource_factory, $resource_lister, $debug;
  
  function __construct(
    ResourceFactory $resource_factory,
    ResourceListerInterface $resource_lister,
    Debug $debug = null
  ) {
    $this->resource_factory = $resource_factory;
    $this->resource_lister  = $resource_lister;
    $this->debug = $debug;
  }
  
  function route($app_name, $path, $map) {
    if (is_array($map) && array_key_exists($path, $map)) {
      $rname = $this->getResourceNamePrefix($app_name) . '\\' . $map[$path];
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
    return $app_name . '\Resource';
  }
  
  private function getNameFromClassSuffix($app_name, $name) {
    $rname = $this->getResourceNamePrefix($app_name) . '\\' . $name;
    if (!class_exists($rname)) {
      $this->throwResourceNotFoundException($name);
    }
    return $rname;
  }
  
  private function throwResourceNotFoundException($path) {
    throw new ResourceNotFound(
      "The resource class definition for the path '$path' was not found."
    );
  }
  
  /**
   * TODO: Refactor this!
   */
  private function getNameFromPath($app_name, $path) {
    $rname = $this->getResourceNamePrefix($app_name);
    $prefixed_with = $this->resource_lister->getResourceListFor($app_name);
    $count = substr_count($this->getResourceNamePrefix($app_name), '\\');
    foreach ($this->getPathSubspaces($path) as $subspace) {
      $count++;
      $old_rname = $rname;
      $rname = $rname . '\\' . String::camelCase($subspace);
      if (class_exists($rname)) {
        continue;
      }
      $rname = $this->getWildCardTypeRoute(
        $old_rname, $prefixed_with, $rname, $count
      );
      if (!class_exists($rname)) {
        $this->throwResourceNotFoundException($path);
      }
    }
    return $rname;
  }
  
  private function getWildCardTypeRoute($old_rname, $prefix, $rname, $count) {
    $prefix = $this->getClassesWithPrefix($old_rname . '\Rt', $prefix);
    if (!empty($prefix)) {
      foreach ($prefix as $class) {
        if ($count === substr_count($class, '\\')) {
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
