<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

use Asar\FileSearcher;

set_include_path(
  Asar::getInstance()->getFrameworkDevTestingPath() . PATH_SEPARATOR .
  get_include_path()
);

/*require_once 'Asar/TempFilesManager.php';
require_once 'Asar/FileSearcher.php';*/

class Asar_FileSearcherTest extends PHPUnit_Framework_TestCase {
  
  private $tempdir = null;
  private $TFM = null;
  private $old_include_path = null;
  
  function setUp() {
    if (!$this->tempdir) {
      $this->tempdir = Asar::getInstance()->getFrameworkTestsDataTempPath();
      $this->TFM = new Asar_TempFilesManager($this->tempdir);
      $this->TFM->clearTempDirectory();
      $this->old_include_path = get_include_path();
    }
    $this->FS = new FileSearcher;
    set_include_path($this->tempdir . PATH_SEPARATOR . $this->old_include_path);
  }
  
  function tearDown() {
    if ($this->TFM) {
      $this->TFM->clearTempDirectory();
    }
    set_include_path($this->old_include_path);
  }
  
  function testSearchingInIncludePaths() {
    $fname = 'foo.txt';
    $this->TFM->newFile($fname, 'Foo');
    $this->assertEquals($this->TFM->getPath($fname), $this->FS->find($fname));
  }
  
  function testSearchingInIncludePaths2() {
    $fname = 'foo/bar/baz.txt';
    $this->TFM->newFile($fname, 'Foo');
    $this->assertEquals($this->TFM->getPath($fname), $this->FS->find($fname));
  }
  
  function testSearchingDirectoryPath() {
    $fname = 'foo/1.txt';
    $this->TFM->newFile($fname, 'Foo');
    $this->assertEquals(dirname($this->TFM->getPath($fname)), $this->FS->find('foo'));
  }
  
  function testSearchingAbsolutePaths() {
    $this->TFM->newFile('bar', 'Foo');
    $fname = $this->TFM->getPath('bar');
    $this->assertEquals($fname, $this->FS->find($fname));
  }
  
  function testSearchingAbsolutePathFail() {
    $this->assertFalse($this->FS->find('/a/non/existent/file.txt'));
  }
  
  function testSearchingInIncludePathsWhenFileIsNonExistent() {
    $fname = 'bar.txt';
    $this->assertFalse($this->FS->find($fname));
  }
}
