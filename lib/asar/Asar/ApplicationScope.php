<?php
namespace Asar;

use \Asar\Config\ConfigInterface;

/**
 * @package Asar
 * @subpackage core
 */
class ApplicationScope {
  
  private $app_name, $config, $cache = array();
  
  function __construct($app_name, ConfigInterface $config) {
    $this->app_name = $app_name;
    $this->config = $config;
  }
  
  function getAppName() {
    return $this->app_name;
  }
  
  function getConfig() {
    return $this->config;
  }
  
  function addToCache($name, $object) {
    $this->cache[$name] = $object;
  }

  function getCache($name) {
    if (!isset($this->cache[$name])) {
      throw new Exception("Cannot find '$name' in cache.");
    }
    return $this->cache[$name];
  }

  function isInCache($name) {
    return isset($this->cache[$name]);
  }
  
}
