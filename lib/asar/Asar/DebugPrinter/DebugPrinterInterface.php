<?php
namespace Asar\DebugPrinter;

use Asar\Debug;

/**
 * Inserts debug information in a content with a suitable format
 *
 * @package Asar
 * @subpackage core
 */
interface DebugPrinterInterface {
  
  /**
   * Inserts debug information from $debug into $content
   *
   * @param Asar_Debug $debug
   * @param string $content the content where the debug info will be inserted
   * @return string $content with the debug info inserted
   */
  function printDebug(Debug $debug, $content);
}


