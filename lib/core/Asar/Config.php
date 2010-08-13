<?php

abstract class Asar_Config implements Asar_Config_Interface {
  
  function getConfig($key = null) {
    if ($key) {
      if (array_key_exists($key, $this->config)) {
        return $this->config[$key];
      }
      return null;
    }
    return $this->config;
  }
  
  function importConfig(Asar_Config_Interface $config) {
    $this->config = array_merge($config->getConfig(), $this->config);
  }
  
}
