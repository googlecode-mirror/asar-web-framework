<?php

namespace Asar\Tests\Unit {

require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\ApplicationFactory;

class ApplicationFactoryTest extends \Asar\Tests\TestCase {
  
  function setUp() {
    /**
     * See test classes near the end of this file.
     */
    
    $this->config = $this->getMock('Asar\Config\ConfigInterface');
    $this->F = new ApplicationFactory($this->config);
  }
  
  
  function testGettingApplication() {
    $this->config->expects($this->any())
      ->method('getConfig')
      ->with('default_classes.config')
      ->will($this->returnValue('Asar\Config\DefaultConfig'));
    $this->assertInstanceOf(
      'Asar\Tests\Unit\ApplicationFactoryTest\TestApp1\Application',
      $this->F->getApplication(
        'Asar\Tests\Unit\ApplicationFactoryTest\TestApp1'
      )
    );
  }
  
  function testGettingApplicationViaConfig() {
    $this->config->expects($this->atLeastOnce())
      ->method('getConfig')
      ->with('default_classes.application')
      ->will($this->returnValue('Asar\ApplicationBasic'));
    $this->assertNotSame(
      false,
      $this->F->getApplication(
        'Asar\Tests\Unit\ApplicationFactoryTest\TestApp2'
      )
    );
    $this->assertInstanceOf(
      'Asar\ApplicationBasic',
      $this->F->getApplication(
        'Asar\Tests\Unit\ApplicationFactoryTest\TestApp2'
      )
    );
  }
  
}

}

namespace Asar\Tests\Unit\ApplicationFactoryTest\TestApp1 {
  class Application extends \Asar\Application {}
}

namespace Asar\Tests\Unit\ApplicationFactoryTest\TestApp2 {
  class Config extends \Asar\Config\DefaultConfig {}
}
