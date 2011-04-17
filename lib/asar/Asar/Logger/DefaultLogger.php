<?php
namespace Asar\Logger;

use Asar\Logger\LoggerInterface;
use Asar\File;

/**
 * @package Asar
 * @subpackage core
 */
class DefaultLogger implements LoggerInterface {
  
  private $file;
  
  /**
   * @param Asar_File $file an Asar_File object to store the log files in
   */
  function __construct(File $file) {
    $this->file = $file;
  }

  /**
   * Logs the message to the log file
   *
   * @param string $message the message to log
   */
  function log($message) {
    $date_time = strftime("%a, %d %b %Y %H:%M:%S GMT");
    $this->file->writeAfter("[$date_time] $message\n");
    $this->file->save();
  }
  
  /**
   * Get the log file object
   * @return Asar_File the log file object {@link $file}
   */
  function getLogFile() {
    return $this->file;
  }

}
