<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Test_Parent_Class {
  function throwException() {
  	Asar::exception($this, 'Throwing exception for '.get_class($this));
  }
}
class Test_Child_Class extends Test_Parent_Class {}
class Test_2Child_Class extends Test_Parent_Class {}
class Test_GrandChild_Class extends Test_Child_Class {}
class Test_Parent_Class_Exception extends Exception {}
class Test_Child_Class_Exception extends Test_Parent_Class_Exception {} 
class Test_Class_With_No_Exception {
  function throwException() {
  	Asar::exception($this, 'Throwing exception for '.get_class($this));
  }
}
 
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
  
  function testSimpleException() {
    $obj = new Test_Parent_Class();
    try {
      $obj->throwException();
      $this->assertTrue(false, 'Exception not thrown');
    } catch (Exception $e) {
      $this->assertEquals('Test_Parent_Class_Exception', get_class($e), 'Wrong exception thrown');
      $this->assertEquals('Throwing exception for Test_Parent_Class', $e->getMessage(), 'Exception message mismatch');
    }
  }
  
  function testChildClassException() {
    $obj = new Test_Child_Class();
    try {
      $obj->throwException();
      $this->assertTrue(false, 'Exception not thrown');
    } catch (Exception $e) {
      $this->assertEquals('Test_Child_Class_Exception', get_class($e), 'Wrong exception thrown');
      $this->assertEquals('Throwing exception for Test_Child_Class', $e->getMessage(), 'Exception message mismatch');
    }
  }
  
  function testChildClassExceptionWithoutDefinedExceptionForIt() {
    $obj = new Test_2Child_Class();
    try {
      $obj->throwException();
      $this->assertTrue(false, 'Exception not thrown');
    } catch (Exception $e) {
      $this->assertEquals('Test_Parent_Class_Exception', get_class($e), 'Wrong exception thrown');
      $this->assertEquals('Throwing exception for Test_2Child_Class', $e->getMessage(), 'Exception message mismatch');
    }
  }
  
  function testGrandChildClassExceptionWithoutDefinedExceptionForIt() {
    $obj = new Test_GrandChild_Class();
    try {
      $obj->throwException();
      $this->assertTrue(false, 'Exception not thrown');
    } catch (Exception $e) {
      $this->assertEquals('Test_Child_Class_Exception', get_class($e), 'Wrong exception thrown');
      $this->assertEquals('Throwing exception for Test_GrandChild_Class', $e->getMessage(), 'Exception message mismatch');
    }
  }
  
  function testDefaultToException() {
    $obj = new Test_Class_With_No_Exception();
    try {
      $obj->throwException();
      $this->assertTrue(false, 'Exception not thrown');
    } catch (Exception $e) {
      $this->assertEquals('Exception', get_class($e), 'Wrong exception thrown');
      $this->assertEquals('Throwing exception for Test_Class_With_No_Exception', $e->getMessage(), 'Exception message mismatch');
    }
  }
}

?>