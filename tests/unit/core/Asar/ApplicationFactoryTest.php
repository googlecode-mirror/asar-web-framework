<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

class Asar_ApplicationFactoryTest_Application extends Asar_Application {}

class Asar_ApplicationFactoryTest_Test2_Config extends Asar_Config_Default {
    
}

// TODO: How do we test factories?
class Asar_ApplicationFactoryTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->config = $this->getMock('Asar_Config_Interface');
    $this->F = new Asar_ApplicationFactory($this->config);
  }
  
  
  function testGettingApplication() {
    $this->config->expects($this->atLeastOnce())
      ->method('getConfig')
      ->with('default_classes.config')
      ->will($this->returnValue('Asar_Config_Default'));
    $this->assertInstanceOf(
      'Asar_ApplicationFactoryTest_Application',
      $this->F->getApplication('Asar_ApplicationFactoryTest')
    );
  }
  
  function testGettingApplicationViaConfig() {
    $this->config->expects($this->atLeastOnce())
      ->method('getConfig')
      ->with('default_classes.application')
      ->will($this->returnValue('Asar_ApplicationBasic'));
    $this->assertNotSame(
      false,
      $this->F->getApplication('Asar_ApplicationFactoryTest_Test2')
    );
    $this->assertInstanceOf(
      'Asar_ApplicationBasic',
      $this->F->getApplication('Asar_ApplicationFactoryTest_Test2')
    );
  }
  
}
