<?php
require_once 'Asar/Toolset.php';

/**
 * The Asar class. Useful for getting information about the framework
 * and its directories.
 *
 */
class Asar {
  
  private static $paths = array(), $instance;
  private $toolset;
  
  /**
   * @param string $key path key to store or to obtain from self::$paths cache
   * @param string $path path to store or retrieve.
   * @return string the full path translated from $path
   */
  private function getPath($key, $path) {
    if (!isset(self::$paths[$key])) {
      self::$paths[$key] = $this->getFrameworkPath() . $path;
    }
    return self::$paths[$key];
  }
  
  /**
   * @return string the full path to the framework source code
   */
  private static function _getFrameworkPath() {
    return realpath(dirname(__FILE__) . '/../../');
  }
  
  /**
   * Provides a stored instance of the arch class. The instance itself
   * doesn't store a useful state. This is more for utility.
   * @return Asar
   */
  static function getInstance() {
    if (!self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
  }
  
  /**
   * Public function for getting the framework path
   * @return string the full path to the framework source code
   */
  function getFrameworkPath() {
    if (!isset(self::$paths['framework'])) {
      self::$paths['framework'] = self::_getFrameworkPath();
    }
    return self::$paths['framework'];
  }
  
  /**
   * @return string full path to the core libraries
   */
  function getFrameworkCorePath() {
    return $this->getPath('framework_core', '/lib/asar');  
  }
  
  /**
   * @return string full path to the vendor or 3rd-party source code
   */
  function getFrameworkVendorPath() {
    return $this->getPath('framework_vendor', '/lib/vendor');
  }
  
  /**
   * @return string full path to the extensions code
   */
  function getFrameworkExtensionsPath() {
    return $this->getPath('framework_extensions', '/lib/extensions');  
  }
  
  /**
   * @return string full path to the development-related codes used for
   *                testing and development
   */
  function getFrameworkDevPath() {
    return $this->getPath('framework_dev', '/lib/dev');
  }
  
  /**
   * @return string full path to the test directory useful for running tests
   */
  function getFrameworkTestsPath() {
    return $this->getPath('framework_tests', '/tests');  
  }
  
  /**
   * @return string full path to the test data path where test-related files
   *                are stored. Includes fixtures and temporary test files.
   */
  function getFrameworkTestsDataPath() {
    return $this->getPath('framework_tests_data', '/tests/data');
  }
  
  /**
   * @return string full path to the server test fixtures
   */
  function getFrameworkTestsDataServerFixturesPath() {
    return $this->getPath(
      'framework_tests_data_test_server_fixtures',
      '/tests/data/test-server-fixtures'
    );
  }
  
  /**
   * @return string full path to the test temporary files
   */
  function getFrameworkTestsDataTempPath() {
    return $this->getPath('framework_tests_data_temp', '/tests/data/temp');
  }
  
  /**
   * @return string returns the version of the framework
   */
  function getVersion() {
    return '0.4b';
  }
  
  /**
   * @return Asar_Toolset an instance of the Asar_Toolset useful for managing
   *                      include paths and directories.
   * @todo this is unnecessary in future implementation
   */
  function getToolSet() {
    if (!$this->toolset) {
      $this->toolset = new \Asar\Toolset;
    }
    return $this->toolset;
  }
  
}
