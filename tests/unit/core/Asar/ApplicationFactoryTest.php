<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\ApplicationFactory;

class Asar_ApplicationFactoryTest_Application extends \Asar\Application {}

class Asar_ApplicationFactoryTest_Test2_Config
  extends \Asar\Config\DefaultConfig {}

class Asar_ApplicationFactoryTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->config = $this->getMock('Asar\Config\ConfigInterface');
    $this->F = new ApplicationFactory($this->config);
  }
  
  
  function testGettingApplication() {
    $this->config->expects($this->atLeastOnce())
      ->method('getConfig')
      ->with('default_classes.config')
      ->will($this->returnValue('Asar\Config\DefaultConfig'));
    $this->assertInstanceOf(
      'Asar_ApplicationFactoryTest_Application',
      $this->F->getApplication('Asar_ApplicationFactoryTest')
    );
  }
  
  function testGettingApplicationViaConfig() {
    $this->config->expects($this->atLeastOnce())
      ->method('getConfig')
      ->with('default_classes.application')
      ->will($this->returnValue('Asar\ApplicationBasic'));
    $this->assertNotSame(
      false,
      $this->F->getApplication('Asar_ApplicationFactoryTest_Test2')
    );
    $this->assertInstanceOf(
      'Asar\ApplicationBasic',
      $this->F->getApplication('Asar_ApplicationFactoryTest_Test2')
    );
  }
  
}
