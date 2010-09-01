<?php

class Asar_ResourceLister implements Asar_ResourceLister_Interface {
  
  private $file_searcher;
  
  function __construct(Asar_FileSearcher $file_searcher) {
    $this->file_searcher = $file_searcher;
  }
  
  // TODO: Possibly make use of directory iterator instead
  function getResourceListFor($app_name) {
    $app_dir = $this->file_searcher->find(str_replace('_', '/', $app_name));
    $resource_dir = $app_dir . DIRECTORY_SEPARATOR . 'Resource';
    $resources = $this->collectResources(
      $resource_dir, $app_name . '_Resource'
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
        $class_name = $prefix . '_' . substr($file, 0, -4);
        $resources[] = $class_name;
      }
      
      if (is_dir($path . DIRECTORY_SEPARATOR . $file)) {
        $resources = array_merge(
          $resources, 
          $this->collectResources(
            $path . DIRECTORY_SEPARATOR . $file,
            $prefix . '_' . $file
          )
        );
      }
    }
    return $resources;
  }
  
}