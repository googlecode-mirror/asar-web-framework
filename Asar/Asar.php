<?php

class Asar {
  private static $version  = '0.0.1pa';
  private static $asarpath = NULL;
  
  static function getVersion() {
    return self::$version;
  }
  
  static function setAsarPath($path) {
    self::$asarpath = $path;
    set_include_path(self::$asarpath);
  }
  /**
   * Asar Loader
   * argument must follow naming convention
   */
  static function load($class) {
    
  }
}
?>