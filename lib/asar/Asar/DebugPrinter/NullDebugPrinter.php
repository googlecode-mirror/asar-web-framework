<?php
namespace Asar\DebugPrinter;

use Asar\DebugPrinter\DebugPrinterInterface;
use Asar\Debug;

/**
 * A Null DebugPrinter that doesn't do anything but return the content
 *
 * @package Asar
 * @subpackage core
 */
class NullDebugPrinter implements DebugPrinterInterface {
  
  /**
   * Simply returns the passed $content. Nothing more.
   *
   * @param Asar_Debug $debug
   * @param string $content
   * @return string returns the $content without modifications
   */
  function printDebug(Debug $debug, $content) {
    return $content;
  }
  
}
