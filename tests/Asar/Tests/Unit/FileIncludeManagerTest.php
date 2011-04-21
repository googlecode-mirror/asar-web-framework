<?php

namespace Asar\Tests\Unit;

require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\FileIncludeManager;

class FileIncludeManagerTest extends \Asar\Tests\TestCase {
  
  function setUp() {
    $this->FI = new FileIncludeManager;
	  $this->clearTestTempDirectory();
  }
  
  function tearDown() {
	  $this->clearTestTempDirectory();
	}
  
  function testRequireFileOnce() {
    $this->getTFM()->newFile(
      'foo.php',
      '<?php return "hello from foo!";'
    );
    $this->assertEquals(
      'hello from foo!',
      $this->FI->requireFileOnce($this->getTFM()->getPath('foo.php'))
    );
    $this->assertSame(
      true, $this->FI->requireFileOnce($this->getTFM()->getPath('foo.php'))
    );
  }
  
  function testInclude() {
    $this->getTFM()->newFile(
      'bar.php',
      '<?php return "hello from bar!";'
    );
    $this->assertEquals(
      'hello from bar!',
      $this->FI->includeFile($this->getTFM()->getPath('bar.php'))
    );
  }
}
