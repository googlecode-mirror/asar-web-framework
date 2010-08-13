<?php

require_once 'Asar/Toolset.php';

class Asar {
  
  private static $paths = array(), $instance;
  private $toolset;
  
  private function getPath($key, $path) {
    if (!isset(self::$paths[$key])) {
      self::$paths[$key] = $this->getFrameworkPath() . $path;
    }
    return self::$paths[$key];
  }
  
  private static function _getFrameworkPath() {
    return realpath(dirname(__FILE__) . '/../../');
  }
  
  static function getInstance() {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  function getFrameworkPath() {
    if (!isset(self::$paths['framework'])) {
      self::$paths['framework'] = self::_getFrameworkPath();
    }
    return self::$paths['framework'];
  }
  
  function getFrameworkCorePath() {
    return $this->getPath('framework_core', '/lib/core');  
  }
  
  function getFrameworkVendorPath() {
    return $this->getPath('framework_vendor', '/lib/vendor');
  }
  
  function getFrameworkExtensionsPath() {
    return $this->getPath('framework_extensions', '/lib/extensions');  
  }
  
  function getFrameworkDevPath() {
    return $this->getPath('framework_dev', '/lib/dev');
  }
  
  function getFrameworkDevTestingPath() {
    return $this->getPath('framework_dev_testing', '/lib/dev/testing');
  }
  
  function getFrameworkTestsPath() {
    return $this->getPath('framework_tests', '/tests');  
  }
  
  function getFrameworkTestsDataPath() {
    return $this->getPath('framework_tests_data', '/tests/data');
  }
  
  function getFrameworkTestsDataServerFixturesPath() {
    return $this->getPath(
      'framework_tests_data_test_server_fixtures',
      '/tests/data/test-server-fixtures'
    );
  }

  function getFrameworkTestsDataTempPath() {
    return $this->getPath('framework_tests_data_temp', '/tests/data/temp');
  }
  
  function getVersion() {
    return '0.4b';
  }
  
  function getToolSet() {
    if (!$this->toolset) {
      $this->toolset = new Asar_Toolset;
    }
    return $this->toolset;
  }
  
}
