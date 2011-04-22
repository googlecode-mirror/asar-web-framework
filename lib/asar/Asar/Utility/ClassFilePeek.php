<?php
namespace Asar\Utility;
/**
 */
class ClassFilePeek {
  
  function getDefinedClasses($file) {
    $php_code = file_get_contents($file);
    $classes = $this->get_php_classes($php_code);
    return $classes;
  }

  private function get_php_classes($php_code) {
    $classes = array();
    $tokens = token_get_all($php_code);
    $count = count($tokens);
    for ($i = 2; $i < $count; $i++) {
      if (   $tokens[$i - 2][0] == T_CLASS
          && $tokens[$i - 1][0] == T_WHITESPACE
          && $tokens[$i][0] == T_STRING) {

          $class_name = $tokens[$i][1];
          $classes[] = $class_name;
      }
    }
    return $classes;
  }
  
  
}
