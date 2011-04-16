<?php
/**
 * A Null DebugPrinter that doesn't do anything but return the content
 *
 * @package Asar
 * @subpackage core
 */
class Asar_DebugPrinter_Null implements Asar_DebugPrinter_Interface {
  
  /**
   * Simply returns the passed $content. Nothing more.
   *
   * @param Asar_Debug $debug
   * @param string $content
   * @return string returns the $content without modifications
   */
  function printDebug(Asar_Debug $debug, $content) {
    return $content;
  }
  
}
