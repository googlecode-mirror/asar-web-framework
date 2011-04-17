<?php

namespace Asar\Tests;

/**
 * A helper class to wrap common test setups in one class for easier testing.
 */
abstract class TestCase extends PHPUnit_Framework_TestCase {

  protected function quickMock($class, array $methods = array()) {
    return $this->getMock($class, $methods, array(), '', false);
  }

}
