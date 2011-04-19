<?php

namespace Asar\Tests\Unit;

require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\Tests\TestCase;
use \Asar;

class AsarTest extends TestCase {
  
  function setUp() {
    $this->A = new Asar;
    $this->framework_path = realpath(dirname(__FILE__). '/../../../../');
  }
  
  /**
   * @dataProvider gettingPathsData
   */
  function testGettingPaths($method, $path) {
    $this->assertEquals(
      realpath($this->framework_path . $path), 
      call_user_func(array($this->A, $method))
    );
  }
  
  function gettingPathsData() {
    return array(
      array('getFrameworkPath', ''),
      array('getFrameworkCorePath', '/lib/core'),
      array('getFrameworkVendorPath', '/lib/vendor'),
      array('getFrameworkExtensionsPath', '/lib/extensions'),
      array('getFrameworkDevPath', '/lib/dev'),
      array('getFrameworkDevTestingPath', '/lib/dev/testing'),
      array('getFrameworkTestsPath', '/tests'),
      array('getFrameworkTestsDataPath', '/tests/data'),
      array(
        'getFrameworkTestsDataServerFixturesPath',
        '/tests/data/test-server-fixtures'
      ),
      array('getFrameworkTestsDataTempPath', '/tests/data/temp')
    );
  }
  
  function testGettingAnInstance() {
    $A1 = Asar::getInstance();
    $this->assertInstanceOf('Asar', $A1);
  }
  
  function testGettingInstanceReturnsTheSameInstance() {
    $A1 = Asar::getInstance();
    $A2 = Asar::getInstance();
    $this->assertSame($A1, $A2);
  }
  
  function testGettingVersion() {
    $this->assertEquals('0.4b', $this->A->getVersion());
  }
  
  function testGettingToolSet() {
    $this->assertInstanceOf('Asar\Toolset', $this->A->getToolSet());
  }
  
  function testGettingToolSetAgainReturnsTheSameInstance() {
    $this->assertSame($this->A->getToolSet(), $this->A->getToolSet());
  }
}
