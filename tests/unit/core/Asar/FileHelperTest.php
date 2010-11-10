<?php
require_once realpath(dirname(__FILE__). '/../../../config.php');

class Asar_FileHelperTest extends PHPUnit_Framework_TestCase {
	
	function setUp() {
	  $this->tempdir = Asar::getInstance()->getFrameworkTestsDataTempPath();
    $this->TFM = new Asar_TempFilesManager($this->tempdir);
    $this->TFM->clearTempDirectory();
    $this->helper = new Asar_FileHelper;
	}
	
	function tearDown() {
	  $this->TFM->clearTempDirectory();
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
	  $this->assertType('Asar_File', $file);
	  $this->assertEquals($filename, $file->getFileName());
	  $this->assertEquals("Bar!", $file->getContents());
	}
	
	function testCreationReturnsFalseWhenItFailsToCreateFile() {
	  $filename = $this->createDirPath($this->tempdir, 'foo.txt');
	  Asar_File::create($filename, 'boo!');
	  $this->setExpectedException('Asar_FileHelper_Exception_FileAlreadyExists');
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
    $this->setExpectedException('Asar_FileHelper_Exception_ParentDirectoryDoesNotExist');
    $this->assertFalse($this->helper->createDir($dir));
	}
	
	function testCreatingDirectoriesThrowsDirectoryExistsException() {
    $dir = $this->createDirPath($this->tempdir, 'foo');
    mkdir($dir);
    $this->setExpectedException('Asar_FileHelper_Exception_DirectoryAlreadyExists');
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
