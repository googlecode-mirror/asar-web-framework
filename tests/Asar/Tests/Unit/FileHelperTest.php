<?php

namespace Asar\Tests\Unit;

require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\FileHelper;
use \Asar\File;
use \Asar;
use \Asar\Tests\TempFilesManager;

class FileHelperTest extends \Asar\Tests\TestCase {
	
	function setUp() {
	  $this->tempdir = $this->getTempDir();
	  $this->clearTestTempDirectory();
    $this->helper = new FileHelper;
	}
	
	function tearDown() {
	  $this->clearTestTempDirectory();
	}
	
	private function createDirPath() {
	 $args = func_get_args();
	 return implode(DIRECTORY_SEPARATOR, $args);
	}
	
	function testBasicCreation() {
	  $filename = $this->createDirPath($this->tempdir, 'foo.txt');
	  $this->helper->create($filename, "Foo!");
	  $this->assertFileExists($filename);
	  $this->assertEquals("Foo!", file_get_contents($filename));
	}
	
	function testBasicCreationReturnsAFileObject() {
	  $filename = $this->createDirPath($this->tempdir, 'bar.txt');
	  $file = $this->helper->create($filename, "Bar!");
	  $this->assertInstanceOf('Asar\File', $file);
	  $this->assertEquals($filename, $file->getFileName());
	  $this->assertEquals("Bar!", $file->getContents());
	}
	
	function testCreationReturnsFalseWhenItFailsToCreateFile() {
	  $filename = $this->createDirPath($this->tempdir, 'foo.txt');
	  File::create($filename, 'boo!');
	  $this->setExpectedException('Asar\FileHelper\Exception\FileAlreadyExists');
	  $this->helper->create($filename, "Foo!");
	}
	
	function testCreatingDirectories() {
    $dir = $this->createDirPath($this->tempdir, 'foo');
    $this->helper->createDir($dir);
    $this->assertFileExists($dir);
	}
	
	function testCreatingDirectoriesReturnsTrueWhenSuccessful() {
    $dir = $this->createDirPath($this->tempdir, 'bar');
    $this->assertTrue($this->helper->createDir($dir));
	}
	
	function testCreatingDirectoriesReturnsFalseWhenNotSuccessful() {
    $dir = $this->createDirPath($this->tempdir, 'choo', 'bar');
    mkdir(dirname($dir));
    chmod(dirname($dir), 0444);
    $result = $this->helper->createDir($dir);
    $this->assertFileNotExists($dir);
    $this->assertFalse($result);
	}
	
	function testCreatingDirectoriesThrowsParentDirectoryDoesNotExistException() {
    $dir = $this->createDirPath('/bar', 'foo');
    $this->setExpectedException(
      'Asar\FileHelper\Exception\ParentDirectoryDoesNotExist'
    );
    $this->assertFalse($this->helper->createDir($dir));
	}
	
	function testCreatingDirectoriesThrowsDirectoryExistsException() {
    $dir = $this->createDirPath($this->tempdir, 'foo');
    mkdir($dir);
    $this->setExpectedException(
      'Asar\FileHelper\Exception\DirectoryAlreadyExists'
    );
    $this->assertFalse($this->helper->createDir($dir));
	}
	
	function testCreatingMultipleDirectories() {
    $dir1 = $this->createDirPath($this->tempdir, 'foo');
    $dir2 = $this->createDirPath($this->tempdir, 'bar');
    $dir3 = $this->createDirPath($this->tempdir, 'baz');
    $this->helper->createDir($dir1, $dir2, $dir3);
    foreach (array($dir1, $dir2, $dir3) as $dir) {
      $this->assertFileExists($dir);
    }
	}
	
}
