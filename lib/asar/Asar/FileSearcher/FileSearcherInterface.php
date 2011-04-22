<?php
namespace Asar\FileSearcher;

/**
 * Provides an interfaces for searching a file given a path
 */
interface FileSearcherInterface {
  
  /**
   * @param string $file_path a path to a file that is not usually absolute
   * @return string|boolean returns an absolute path to the file or false if it
   *                        didn't find the file in the known paths
   */
  function find($file_path);
}
