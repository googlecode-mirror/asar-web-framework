<?php
class Asar_Utility_String {
  static function dashCamelCase($string) {
    return str_replace(' ', '-', self::ucwordsLower($string));
  }
  
  static function camelCase($string) {
    return str_replace(array(' ', '-'), '', self::ucwordsLower($string));
  }
  
  private static function ucwordsLower($string) {
    return ucwords(
      strtolower(str_replace(array('-', '_'), ' ', $string))
    );
  }
}
