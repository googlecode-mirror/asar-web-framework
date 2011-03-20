<?php

class Asar_Logger_Default implements Asar_Logger_Interface {
  
  private $file;
  
  /**
   * The constructor
   *
   * @param Asar_File $file an Asar_File object to store the log files in
   */
  function __construct(Asar_File $file) {
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
