<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');
set_include_path(
  Asar::getInstance()->getFrameworkDevTestingPath() . PATH_SEPARATOR .
  get_include_path()
);

use \Asar\FileIncludeManager;

class Asar_FileIncludeManagerTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->FI = new FileIncludeManager;
    $this->tempdir = Asar::getInstance()->getFrameworkTestsDataTempPath();
    $this->TFM = new Asar_TempFilesManager($this->tempdir);
  }
  
  function tearDown() {
	  $this->TFM->clearTempDirectory();
	}
  
  function testRequireFileOnce() {
    $this->TFM->newFile(
      'foo.php',
      '<?php return "hello from foo!";'
    );
    $this->assertEquals(
      'hello from foo!', $this->FI->requireFileOnce($this->TFM->getPath('foo.php'))
    );
    $this->assertSame(
      true, $this->FI->requireFileOnce($this->TFM->getPath('foo.php'))
    );
  }
  
  function testInclude() {
    $this->TFM->newFile(
      'bar.php',
      '<?php return "hello from bar!";'
    );
    $this->assertEquals(
      'hello from bar!', $this->FI->includeFile($this->TFM->getPath('bar.php'))
    );
  }
}
