<?php

require_once realpath(
  dirname(__FILE__). '/../../../../lib/core/Asar/Toolset.php'
);

class Asar_ToolsetTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->toolset = new Asar_Toolset;
  }
  
  function testGettingIncludePathManager() {
    $this->assertType(
      'Asar_IncludePathManager', $this->toolset->getIncludePathManager()
    );
  }
  
  function testGettingIncludePathManagerReturnsOnlyOneInstance() {
    $this->assertSame(
      $this->toolset->getIncludePathManager(),
      $this->toolset->getIncludePathManager()
    );
  }
  
}