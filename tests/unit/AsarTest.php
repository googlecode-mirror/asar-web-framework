<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar/Asar.php';
 
class AsarTest extends PHPUnit_Framework_TestCase {
  
  protected function setUp() {
    //$this->asar = new Asar();
  }
  
  function testGetVersion() {
    $this->assertEquals(Asar::getVersion(), '0.0.1pa', 'Unable to get proper version');
  }
  
  function testSetAsarPath() {
    
    $testpath = '/testpathxyz';
    Asar::setAsarPath($testpath);
    
    $this->assertTrue( strpos(get_include_path(), $testpath) !== FALSE, 'Path setting not found');
  }
}

?>