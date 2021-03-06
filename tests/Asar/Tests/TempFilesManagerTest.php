<?php

namespace Asar\Tests;

require_once realpath(dirname(__FILE__). '/../../config.php');

use \Asar\Tests\TempFilesManager;

class TempFilesManagerTest extends \PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->tempdir_parent = realpath(__DIR__ . '/../../') . '/data';
    $this->tempdir = $this->tempdir_parent . DIRECTORY_SEPARATOR . 'temp';
    $this->TFM = new TempFilesManager($this->tempdir);
    $this->recursiveDelete($this->tempdir, false);
  }
  
  function tearDown() {
    $this->recursiveDelete($this->tempdir, false);
  }
  
  private function recursiveDelete($folderPath, $this_too = true) {
    if (file_exists($folderPath) && is_dir($folderPath)) {
      $contents = scandir($folderPath);
      foreach ($contents as $value) {
        if ($value != "." && $value != ".." && $value != '.svn') {
          $value = $folderPath . "/" . $value;
          if (is_dir($value)) {
            $this->recursiveDelete($value);
          } elseif (is_file($value)) {
            @unlink($value);
          }
        }
      }
      if ($this_too) {
        return rmdir($folderPath);
      }
    } else {
       return false;
    }
  }
  
  function testInitializing() {
    $this->assertContains(
      realpath(__DIR__ . '/../../'), $this->tempdir_parent
    );
    $this->assertFileExists($this->tempdir_parent);
  }
  
  function testInstatiationThrowsErrorWhenTempDirDoesNotExist() {
    $dir = 'foo_dir';
    $this->setExpectedException(
      'Asar\Tests\TempFilesManager\Exception',
      "The directory specified ($dir) as temporary directory does not exist."
    );
    $TFM = new TempFilesManager($dir);
  }
  
  private function getFilePath($file) {
    return $this->tempdir . DIRECTORY_SEPARATOR . $file;
  }
  
  function testAddingFilesToTemp() {
    $this->TFM->newFile('foo.txt', 'bar');
    $file_full_path = $this->getFilePath('foo.txt');
    $this->assertFileExists($file_full_path);
    $this->assertEquals('bar', file_get_contents($file_full_path));
  }
  
  function testAddingFilesWithDirectoryPaths() {
    $file = 'foo/bar/baz.txt';
    $this->TFM->newFile($file, 'foo bar baz');
    $this->assertFileExists($this->getFilePath($file));
    $this->assertEquals(
      'foo bar baz', file_get_contents($this->getFilePath($file))
    );
  }
  
  function testCreatingDirectories() {
    $dir = 'foo/bar/baz';
    $this->TFM->newDir($dir);
    $this->assertFileExists($this->getFilePath($dir));
  }
  
  function testGettingFullFilePath() {
    $files = array('foo.txt' => 'foo', 'bar/baz.txt' => 'bar baz');
    foreach ($files as $file => $contents) {
      $this->TFM->newFile($file, $contents);
      $this->assertEquals(
        $this->getFilePath($file), $this->TFM->getPath($file)
      );
    }
  }
  
  function testRemovingFilesInTemp() {
    $this->TFM->newFile('file1', 'test');
    $this->TFM->removeFile('file1');
    $this->assertFileNotExists($this->getFilePath('file1'));
  }
  
  /**
   * @dataProvider clearingTempDirTestData
   */
  function testClearingTempDirectory($files) {
    foreach($files as $file => $contents) {
      $this->TFM->newFile($file, $contents);
      $this->assertFileExists($this->getFilePath($file));
    }
    $this->TFM->clearTempDirectory();
    foreach(array_keys($files) as $file) {
      $this->assertFileNotExists($this->getFilePath($file));
    }
  }
  
  function clearingTempDirTestData() {
    return array(
      array(
        array('file1' => 'test1', 'file2' => 'test2', 'file3' => 'test3')
      ),
      array(
        array(
          'foo/file1' => 'test1',
          'bar/file2' => 'test2', 
          'foo/baz/file3' => 'test3'
        )
      )
    );
  }
  
  function testGettingTempDirectory() {
    $this->assertEquals($this->tempdir, $this->TFM->getTempDirectory());
  }
}
