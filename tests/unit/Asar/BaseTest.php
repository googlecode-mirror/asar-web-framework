<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';
require_once 'Asar/Base.php';

class Asar_Base_Child_Temp extends Asar_Base {
  protected $first   = 'First Attribute';
  protected $second  = 'Second Attribute';
  protected $third   = 'Third Attribute';
  
  public $fourth  = '';
  public $fifth   = '';
  public $sixth   = '';
  
  public $seventh = '';
  public $eighth   = '';
  public $ninth   = '';
  
  protected static $attr_reader   = array('first', 'second', 'third');
  protected static $attr_writer   = array('fourth', 'fifth', 'sixth');
  protected static $attr_accessor = array('seventh', 'eighth', 'ninth');
  
  function throwException() {
  	$this->exception('Exception thrown from Asar_Base_Child_Temp');
  }
}

class Asar_BaseTest extends PHPUnit_Framework_TestCase {
  
  public static function main()
  {
      PHPUnit_TextUI_TestRunner::run(self::suite());
  }
  
  protected function setUp() {
    $this->ABC = new Asar_Base_Child_Temp();
  }
  
  function testUnderscore() {
    $teststr = 'IAmQuiteAware';
    $expected = 'i_am_quite_aware';
    $this->assertEquals($expected, Asar_Base::underscore($teststr), 'Was unable to underscore test string');
  }
  
  function testDash() {
    $teststr = 'IAmQuiteAware';
    $expected = 'i-am-quite-aware';
    $this->assertEquals($expected, Asar_Base::dash($teststr), 'Was unable to dash test string');
  }
  
  function testCamelCase() {
    $teststr = 'i_am_quite_aware';
    $expected = 'IAmQuiteAware';
    $this->assertEquals($expected, Asar_Base::camelCase($teststr), 'Was unable to CamelCase test string');
  }
  
  function testLowerCamelCase() {
    $teststr = 'i_am_quite_aware';
    $expected = 'iAmQuiteAware';
    $this->assertEquals($expected, Asar_Base::lowerCamelCase($teststr), 'Was unable to lowerCamelCase test string');
  }
  
  function testThrowingException() {
    try {
      $this->ABC->throwException();
      $this->assertTrue(false, 'Exception not thrown');
    } catch (Exception $e) {
      $this->assertEquals('Asar_Base_Exception', get_class($e), 'Wrong exception thrown');
      $this->assertEquals('Exception thrown from Asar_Base_Child_Temp', $e->getMessage(), 'Exception message mismatch');
    }
  }
  
  /*
  function testAttrReader() {
    $this->assertEquals('First Attribute', $this->ABC->getFirst(), 'Unable to get first attribute');
    $this->assertEquals('Second Attribute', $this->ABC->getSecond(), 'Unable to get second attribute');
    $this->assertEquals('Third Attribute', $this->ABC->getThird(), 'Unable to get third attribute');
  }
  
  
  
  function testAttrWriter() {
    
    $this->ABC->setFourth('Just Fourth');
    $this->ABC->setFifth('Just Fifth');
    $this->ABC->setSixth('Just Sixth');
    
    $this->assertEquals('Just Fourth', $this->ABC->fourth, 'Unable to set fourth attribute');
    $this->assertEquals('Just Fifth',  $this->ABC->fifth, 'Unable to set fifth attribute');
    $this->assertEquals('Just Sixth', $this->ABC->sixth, 'Unable to set sixth attribute');
  }
  
  function testAttrAccessor() {
    
    $this->ABC->setFourth('Just Seventh');
    $this->ABC->setFifth('Just Eighth');
    $this->ABC->setSixth('Just Ninth');
    
    $this->assertEquals('Just Seventh', $this->ABC->fourth, 'Unable to set seventh attribute');
    $this->assertEquals('Just Eighth',  $this->ABC->fifth, 'Unable to set eighth attribute');
    $this->assertEquals('Just Ninth', $this->ABC->sixth, 'Unable to set ninth attribute');
    
    $this->assertEquals('Just Seventh', $this->ABC->getSeventh(), 'Unable to get seventh attribute');
    $this->assertEquals('Just Eighth', $this->ABC->getEighth(), 'Unable to get eighth attribute');
    $this->assertEquals('Just Ninth', $this->ABC->getNinth(), 'Unable to get ninth attribute');
  }
  */
}
?>