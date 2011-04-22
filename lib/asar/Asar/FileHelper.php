<?php
namespace Asar;

use \Asar\File;
use \Asar\FileHelper\Exception\FileAlreadyExists;
use \Asar\FileHelper\Exception\DirectoryAlreadyExists;
use \Asar\FileHelper\Exception\ParentDirectoryDoesNotExist;
/**
 */
class FileHelper {
  
  function create($filename, $contents) {
    try {
      return File::create($filename)->write($contents)->save();
    } catch (\Asar\File\Exception\FileAlreadyExists $e) {
      throw new FileAlreadyExists(
        "The file '$filename' already exists." 
      );
    }
  }
  
  function createDir() {
    $args = func_get_args();
    $truth = true;
    foreach ($args as $arg) {
      $truth = $truth && $this->_createDir($arg);
    }
    return $truth;
  }
  
  function _createDir($directory) {
    if (file_exists($directory)) {
      throw new DirectoryAlreadyExists(
        "The directory '" . $directory . "' already exists."
      );
    }
    if (!file_exists(dirname($directory))) {
      throw new ParentDirectoryDoesNotExist(
        "The directory '" . dirname($directory) . "' does not exist."
      );
    }
    if (!is_writable(dirname($directory))) {
      return FALSE;
    }
    mkdir($directory);
    if (file_exists($directory)) {
      return TRUE;
    }
  }
}
