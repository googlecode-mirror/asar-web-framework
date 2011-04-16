<?php

class Asar_ToolsetTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->toolset = new Asar_Toolset;
  }
  
  function testGettingIncludePathManager() {
    $this->assertInstanceOf(
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
