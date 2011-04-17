<?php
namespace Asar;

use \Asar\FileSearcher\FileSearcherInterface;

/**
 * @package Asar
 * @subpackage core
 */
class FileSearcher implements FileSearcherInterface {
  
  function find($file_path) {
    if ($this->isAbsolutePath($file_path)) {
      if (is_file($file_path)) {
        return $file_path;
      } else {
        return false;
      }
    }
    $search_paths = explode(PATH_SEPARATOR, get_include_path());
    foreach ($search_paths as $path) {
      $full_path = $path . DIRECTORY_SEPARATOR . $file_path;
      if (is_file($full_path) || is_dir($full_path)) {
        return $full_path;
        break;
      }
    }
    return false;
  }
  
  private function isAbsolutePath($file_path) {
    return strpos($file_path, '/') === 0;
  }
  
}
