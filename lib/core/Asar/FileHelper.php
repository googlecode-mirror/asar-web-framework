<?php
/**
 * @package Asar
 * @subpackage core
 */
class Asar_FileHelper {
  
  function create($filename, $contents) {
    try {
      return Asar_File::create($filename)->write($contents)->save();
    } catch (Asar_File_Exception_FileAlreadyExists $e) {
      throw new Asar_FileHelper_Exception_FileAlreadyExists(
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
      throw new Asar_FileHelper_Exception_DirectoryAlreadyExists(
        "The directory '" . $directory . "' already exists."
      );
    }
    if (!file_exists(dirname($directory))) {
      throw new Asar_FileHelper_Exception_ParentDirectoryDoesNotExist(
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
