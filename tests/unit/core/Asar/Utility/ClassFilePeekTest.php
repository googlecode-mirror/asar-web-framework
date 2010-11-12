<?php

require_once realpath(dirname(__FILE__) . '/../../../../config.php');

class Asar_Utility_ClassFilePeekTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->peek = new Asar_Utility_ClassFilePeek;
    $this->file = Asar::getInstance()->getFrameworkTestsDataPath() .
      DIRECTORY_SEPARATOR . 'classfilepeektestfile.php';
  }
  
  function testGettingClasses() {
    $this->assertEquals(
      array('Asar_ClassFilePeekTest_Foo', 'Asar_ClassFilePeekTest_Bar'), 
      $this->peek->getDefinedClasses($this->file)
    );
  }
  
}