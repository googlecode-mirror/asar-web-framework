<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

class Asar_ApplicationFactoryTest_Application extends Asar_Application {}

class Asar_ApplicationFactoryTest_Test2_Config implements Asar_Config_Interface {
    function getConfig($key = null) {
      return array();
    }
    
    function importConfig(Asar_Config_Interface $config) {}
}

// TODO: How do we test factories?
class Asar_ApplicationFactoryTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->config = $this->getMock('Asar_Config_Interface');
    $this->F = new Asar_ApplicationFactory;
  }
  
  
  function testGettingApplication() {
   /* $this->config->expects($this->once())
      ->method*/
    $this->assertType(
      'Asar_ApplicationFactoryTest_Application',
      $this->F->getApplication('Asar_ApplicationFactoryTest')
    );
  }
  
  function testGettingApplicationViaConfig() {
    $this->assertNotSame(
      false,
      $this->F->getApplication('Asar_ApplicationFactoryTest_Test2')
    );
    $this->assertType(
      'Asar_ApplicationBasic',
      $this->F->getApplication('Asar_ApplicationFactoryTest_Test2')
    );
  }
  
}
