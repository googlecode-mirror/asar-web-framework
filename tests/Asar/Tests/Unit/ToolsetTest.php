<?php

namespace Asar\Tests\Unit;

require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\Toolset;

class ToolsetTest extends \Asar\Tests\TestCase {
  
  function setUp() {
    $this->toolset = new Toolset;
  }
  
  function testGettingIncludePathManager() {
    $this->assertInstanceOf(
      'Asar\IncludePathManager', $this->toolset->getIncludePathManager()
    );
  }
  
  function testGettingIncludePathManagerReturnsOnlyOneInstance() {
    $this->assertSame(
      $this->toolset->getIncludePathManager(),
      $this->toolset->getIncludePathManager()
    );
  }
  
}
