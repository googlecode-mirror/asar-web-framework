<?php

class Asar_DebugPrinter_Null implements Asar_DebugPrinter_Interface {
  
  function printDebug(Asar_Debug $debug, $content) {
    return $content;
  }
  
}
