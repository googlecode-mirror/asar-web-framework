<?php

namespace Asar\Tests;

/**
 * A helper class to wrap common test setups in one class for easier testing.
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase {

  protected function quickMock($class, array $methods = array()) {
    return $this->getMock($class, $methods, array(), '', false);
  }
  
  protected function getTempDir() {
    return \Asar::getInstance()->getFrameworkTestsDataTempPath();
  }
  
  function getTFM() {
    if (!isset($this->_TFM)) {
      $this->_TFM = new TempFilesManager($this->getTempDir());
    }
    return $this->_TFM;
  }
  
  protected function clearTestTempDirectory() {
    $this->getTFM()->clearTempDirectory();
  }
  
  protected function generateAppName($last) {
    return str_replace('\\', '_', get_class($this)) . $last;
  }
  
  protected function generateUnderscoredName($name) {
    return str_replace('\\', '_', $name);
  }

}
