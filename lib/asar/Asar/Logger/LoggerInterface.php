<?php
namespace Asar\Logger;

/**
 * Provides a generic interface for logging
 * @package Asar
 * @subpackage core
 */
interface LoggerInterface {
  
  /**
   * Log a message
   * @param string $message the message to be logged
   */
  function log($message);
}
