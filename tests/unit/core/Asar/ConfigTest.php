<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

// For testing purposes only...
class Asar_ConfigTest_ConfigSample extends Asar_Config {
  protected $config = array(
    'foo' => 'bar',
    'goo' => 'car',
    'hoo' => 'dar'
  );
}

class Asar_ConfigTest_ConfigSample2 extends Asar_Config {
  protected $config = array(
    'foo' => 'baz',
    'zoo' => 'zaz'
  );
}

class Asar_ConfigTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->config = new Asar_ConfigTest_ConfigSample;
    $this->config2 = new Asar_ConfigTest_ConfigSample2;
  }
  
  function testGettingConfig() {
    $this->assertEquals(
      array('foo' => 'bar', 'goo' => 'car', 'hoo' => 'dar'),
      $this->config->getConfig()
    );
  }
  
  function testGettingSpecificConfig() {
    $this->assertEquals('bar', $this->config->getConfig('foo'));
    $this->assertEquals('car', $this->config->getConfig('goo'));
  }
  
  function testGettingSpecificConfigThatDoesNotExistReturnsNull() {
    $this->assertEquals(null, $this->config->getConfig('zoo'));
    $this->assertEquals(null, $this->config->getConfig('boo'));
  }
  
  function testImportingConfig() {
    $this->config->importConfig($this->config2);
    $this->assertEquals('bar', $this->config->getConfig('foo'));
    $this->assertEquals('zaz', $this->config->getConfig('zoo'));
  }
  
}
