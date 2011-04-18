<?php
namespace Asar;

use \Asar\Config\Exception as ConfigException;

/**
 * @package Asar
 * @subpackage core
 */
class Config implements Config\ConfigInterface {
  
  protected $config = array();
  
  function __construct($init_config = array()) {
    if ($init_config) {
      $this->config = $this->configMerge($init_config, $this->config);
    }
    $this->init();
  }
  
  protected function init() {}
  
  function getConfig($key = null) {
    if (is_string($key)) {
      $keys = explode('.', $key);
      for (
        $i=0, $els = count($keys), $e = null, $arr = $this->config;
        $i < $els; $i++
      ) { 
        if (isset($arr[$keys[$i]])) {
          $a = $arr[$keys[$i]];
          if (is_array($a)) {
            $arr = $a;
          }
          if ($i == $els - 1) {
            $e = $a;
          }
        }
      }
      return $e;
    }
    return $this->config;
  }
  
  function importConfig(Config\ConfigInterface $config) {
    $this->config = $this->configMerge($config->getConfig(), $this->config);
  }
  
  private function configMerge($from, $to, $parent_key = null) {
    foreach ($from as $key => $value) {
      if (isset($to[$key])) {
        $thekey = !$parent_key ? $key : "$parent_key.$key";
        if (is_array($value) XOR is_array($to[$key])) {
          throw new ConfigException(
            "Asar\Config::importConfig() failed. Type mismatch. Unable to " .
            "merge '$thekey' => '$value' with Array."
          );
        }          
        if (is_array($value)) {
          $to[$key] = $this->configMerge($value, $to[$key], $thekey);
        }
      } else {
        $to[$key] = $value;
      }
    }
    return $to;
  }
  
}
