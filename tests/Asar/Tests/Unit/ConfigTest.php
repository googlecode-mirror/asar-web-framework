<?php

namespace Asar\Tests\Unit {

require_once realpath(dirname(__FILE__). '/../../../config.php');


/**
 * See test classes near the end of this file.
 */
class ConfigTest extends \Asar\Tests\TestCase {
  
  function setUp() {
    $this->config = new \Asar_ConfigTest_ConfigSample;
    $this->config2 = new \Asar_ConfigTest_ConfigSample2;
  }
  
  function testGettingConfig() {
    $this->assertEquals($this->config->config, $this->config->getConfig());
  }
  
  function testGettingSpecificConfig() {
    $this->assertEquals('bar', $this->config->getConfig('foo'));
    $this->assertEquals('car', $this->config->getConfig('goo'));
  }
  
  function testGettingSpecificConfigThatDoesNotExistReturnsNull() {
    $this->assertEquals(null, $this->config->getConfig('zoo'));
    $this->assertEquals(null, $this->config->getConfig('boo'));
  }
  
  function testGettingSpecificConfigArrayKeys() {
    $this->assertEquals('doo1', $this->config->getConfig('joo.joo1'));
    $this->assertEquals(32, $this->config->getConfig('joo.joo3.a'));
    $this->assertEquals(null, $this->config->getConfig('joo.joo3.b'));
    $this->assertEquals(null, $this->config->getConfig('joo.joo8.a'));
  }
  
  function testImportingConfig() {
    $this->config->importConfig($this->config2);
    $this->assertEquals('bar', $this->config->getConfig('foo'));
    $this->assertEquals('zaz', $this->config->getConfig('zoo'));
  }
  
  function testImportingConfigWithArrays() {
    $config3 = new \Asar_ConfigTest_ConfigSample3;
    $this->config->importConfig($config3);
    $this->assertSame('doo1', $this->config->getConfig('joo.joo1'));
    $this->assertSame('doo2', $this->config->getConfig('joo.joo2'));
    $this->assertSame('doo4', $this->config->getConfig('joo.joo4'));
    $this->assertSame(32, $this->config->getConfig('joo.joo3.a'));
    $this->assertSame('bee', $this->config->getConfig('joo.joo3.b'));
  }
  
  function testImportingConfigArrayConflict() {
    $this->setExpectedException(
	    '\Asar\Config\Exception',
	    'Asar\Config::importConfig() failed. Type mismatch. '.
	      'Unable to merge \'joo.joo3\' => '.
	      '\'meh\' with Array.'
    );
    $config4 = new \Asar_ConfigTest_ConfigSample4;
    $this ->config->importConfig($config4);
  }
  
  function testConfigConstruction() {
    $config_arr = array(
      'foo' => 1,
      'bar' => 2,
      'jar' => array(
        'a' => 'Aye'
      )
    );
    $config = new \Asar\Config($config_arr);
    $this->assertSame(2, $config->getConfig('bar'));
    $this->assertEquals('Aye', $config->getConfig('jar.a'));
  }
  
  function testConfigRunsInitializationOnConstruction() {
    $config = new \Asar_ConfigTest_ConfigSample4;
    $this->assertEquals('Foo', $config->getConfig('foo'));
  }
  
}
}


namespace {
  // For testing purposes only...
  class Asar_ConfigTest_ConfigSample extends \Asar\Config {
    public $config = array(
      'foo' => 'bar',
      'goo' => 'car',
      'hoo' => 'dar',
      'joo' => array(
        'joo1' => 'doo1',
        'joo2' => 'doo2',
        'joo3' => array(
          'a' => 32
        )
      )
    );
  }

  class Asar_ConfigTest_ConfigSample2 extends \Asar\Config {
    protected $config = array(
      'foo' => 'baz',
      'zoo' => 'zaz'
    );
  }

  class Asar_ConfigTest_ConfigSample3 extends \Asar\Config {
    protected $config = array(
      'joo' => array(
        'joo1' => 'eoo1',
        'joo3' => array(
          'a'  => 'aye',
          'b'  => 'bee'
        ),
        'joo4' => 'doo4'
      )
    );
  }

  class Asar_ConfigTest_ConfigSample4 extends \Asar\Config {
    protected $config = array(
      'joo' => array(
        'joo3' => 'meh'
      )
    );
    
    protected function init() {
      $this->config['foo'] = 'Foo';
    }
  }
}
