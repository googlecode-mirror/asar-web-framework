<?php
namespace Asar;

use \Asar\ResourceLister\ResourceListerInterface;
use \Asar\Application\Finder as AppFinder;
/**
 */
class ResourceLister implements ResourceListerInterface {
  
  private $app_finder;
  
  function __construct(AppFinder $app_finder) {
    $this->app_finder = $app_finder;
  }
  
  // TODO: Possibly make use of directory iterator instead
  function getResourceListFor($app_name) {
    $app_dir = $this->app_finder->find($app_name);
    $resource_dir = $app_dir . DIRECTORY_SEPARATOR . 'Resource';
    $resources = $this->collectResources(
      $resource_dir, $app_name . '\Resource'
    );
    return $resources;
  }
  
  private function collectResources($path, $prefix) {
    $resources = array();
    foreach (scandir($path) as $file) {
      if (preg_match('/^.{1,2}$/', $file)) {
        continue;
      }
      if (preg_match('/.php$/', $file)) {
        $class_name = $prefix . '\\' . substr($file, 0, -4);
        $resources[] = $class_name;
      }
      
      if (is_dir($path . DIRECTORY_SEPARATOR . $file)) {
        $resources = array_merge(
          $resources, 
          $this->collectResources(
            $path . DIRECTORY_SEPARATOR . $file,
            $prefix . '\\' . $file
          )
        );
      }
    }
    return $resources;
  }
  
}
