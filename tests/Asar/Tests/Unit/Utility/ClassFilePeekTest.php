<?php

namespace Asar\Tests\Unit\Utility;

require_once realpath(dirname(__FILE__) . '/../../../../config.php');

use \Asar\Utility\ClassFilePeek;

class ClassFilePeekTest extends \Asar\Tests\TestCase {
  
  function setUp() {
    $this->peek = new ClassFilePeek;
    $this->file = \Asar::getInstance()->getFrameworkTestsDataPath() .
      DIRECTORY_SEPARATOR . 'classfilepeektestfile.php';
  }
  
  function testGettingClasses() {
    $this->assertEquals(
      array('Asar_ClassFilePeekTest_Foo', 'Asar_ClassFilePeekTest_Bar'), 
      $this->peek->getDefinedClasses($this->file)
    );
  }
  
}
