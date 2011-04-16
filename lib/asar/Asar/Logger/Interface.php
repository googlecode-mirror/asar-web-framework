<?php
/**
 * Provides a generic interface for logging
 * @package Asar
 * @subpackage core
 */
interface Asar_Logger_Interface {
  
  /**
   * Log a message
   * @param string $message the message to be logged
   */
  function log($message);
}
