<?php
namespace Asar;

/**
 */
class IncludePathManager {
  
  function add() {
    $paths = func_get_args();
    foreach ($paths as $path) {
      if (is_array($path)) {
        foreach ($path as $p) {
          $this->add($p);
        }
      } else {
        $this->realAdd($path);
      }
    }
  }
  
  private function realAdd($path) {
    $inc_path = get_include_path();
    if (array_search($path, explode(PATH_SEPARATOR, $inc_path)) === false) {
      set_include_path($inc_path . PATH_SEPARATOR . $path);
    }
  }
  
}
