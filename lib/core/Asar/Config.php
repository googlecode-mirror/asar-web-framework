<?php

abstract class Asar_Config implements Asar_Config_Interface {
  
  function getConfig($key = null) {
    if (is_string($key)) {
      $keys = explode('.', $key);
      $arr = $this->config;
      $e = null;
      for ($i=0; $i < count($keys); $i++) { 
        if (array_key_exists($keys[$i], $arr)) {
          $e = $arr[$keys[$i]];
          if (is_array($e)) {
            $arr = $e;
          }
        }
      }
      return $e;
    }
    return $this->config;
  }
  
  function importConfig(Asar_Config_Interface $config) {
    $this->config = array_merge($config->getConfig(), $this->config);
  }
  
  private function strHasDot($str) {
    return strpos($str, '.') > 0;
  }
  
}
