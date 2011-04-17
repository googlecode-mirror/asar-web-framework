<?php
require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\File;

class FileTest extends PHPUnit_Framework_TestCase {
	
	function setUp() {
	  $this->tempdir = \Asar::getInstance()->getFrameworkTestsDataTempPath();
    $this->TFM = new Asar_TempFilesManager($this->tempdir);
    $this->TFM->clearTempDirectory();
	}
	
	function tearDown() {
	 $this->TFM->clearTempDirectory();
	}
	
	function getTempFileName($filename) {
	  return $this->tempdir . DIRECTORY_SEPARATOR . $filename;
	}
	
	function testSettingFileName() {
		$testFileName = $this->tempdir.'AAAAARD';
		$file = new File();
		$file->setFileName($testFileName);
		$this->assertEquals($testFileName, $file->getFileName(), 'Filename returned is not the same');
	}
	
	function testSimpleCreateFile() {
		$testString = 'This is a test';
		$testFileName = $this->getTempFileName('FileTesting.txt');
		$file = File::create($testFileName);
		$file->write($testString)
		     ->save();
    $this->assertFileExists($testFileName, 'Unable to create file');
	  $this->assertEquals($testString, file_get_contents($testFileName), 'Contents of file is not correct');
	}
	
	function testLongWayToCreateFile() {
		$testString = 'This is just a string';
		$testFileName = $this->getTempFileName('GAAnotherFileToTest.txt');
		$file = new File();
		$file->setFileName($testFileName);
		$file->write($testString);
		$file->save();
    $this->assertFileExists($testFileName, 'Unable to create file');
    $this->assertEquals($testString, file_get_contents($testFileName), 'Contents of file is not correct');
	}
	
	function testOpeningAndGettingContents() {
		$testString = 'Different operating system families have different line-ending conventions. When you write a text file and want to insert a line break, you need to use the correct line-ending character(s) for your operating system.';
		$testFileName = $this->getTempFileName('GAAnotherFileToTest.txt');
		file_put_contents($testFileName, $testString);
    $this->assertFileExists($testFileName, 'Unable to create test file');
		$file = new File($testFileName);
		$this->assertEquals($testString, $file->getContent());
		$this->assertEquals($testString, $file->getContents());
		$this->assertEquals($testString, $file->read());
	}
	
	function testStaticUnlink() {
		$testFileName = $this->getTempFileName('Suchadirtyword');
		$testString = '';
		file_put_contents($testFileName, $testString);
    $this->assertFileExists($testFileName, 'Unable to create test file');
    $file = File::unlink($testFileName);
    $this->assertFileNotExists($testFileName, 'Unable to delete the file');
	}
	
	function testStaticUnlinkReturnsFalseWhenFileDoesNotExist() {
    $testFileName = $this->getTempFileName('Nothingnothing');
    $this->assertFalse(
      File::unlink($testFileName),
      'File::unlink() did not return false for non-existent-file.'
    );
	}
	
	function testDeleting() {
		$testFileName = $this->getTempFileName('Suchadirtywordaaa.txt');
		$testString = 'asdfsadf';
		file_put_contents($testFileName, $testString);
   	$this->assertFileExists($testFileName, 'Unable to create test file');
    $file = new File($testFileName);
    $file->write($testString)->save();
    $file->delete();
  
    $this->assertFileNotExists($testFileName, 'Unable to delete the file');
	}
	
	function testWritingBeforeAndAfter() {
		$testString = 'XXX';
		$testFileName = $this->getTempFileName('FileTesting.txt');
		File::create($testFileName)
		      ->write($testString)
		      ->writeBefore('BBBB')
		      ->writeAfter('CCC')
		      ->save();
		$this->assertFileExists($testFileName, 'Unable to create or save file');
		$this->assertEquals('BBBBXXXCCC', file_get_contents($testFileName), 'Unable to write successfully');
	}
	
	function testManyWrites() {
		$testString = 'XXX';
		$testFileName = $this->getTempFileName('FileTesting.txt');
		$testfile = File::create($testFileName);
		$testfile->write($testString)->save();
		$testfile->write('iii')->save();
		$testfile->write('ABCDEFG')->save();
		$this->assertFileExists($testFileName, 'Unable to create or save file');
		$this->assertEquals('ABCDEFG', file_get_contents($testFileName), 'Unable to write successfully');
	}
	
	function testManyWritesButInAppendMode() {
		$testString = 'XXX';
		$testFileName = $this->getTempFileName('FileTesting.txt');
		$testfile = File::create($testFileName)->appendMode();
		$testfile->write($testString)->save();
		$testfile->write('iii')->save();
		$testfile->write('ABCDEFG')->save();
		$this->assertFileExists($testFileName, 'Unable to create or save file');
		$this->assertEquals('XXXiiiABCDEFG', file_get_contents($testFileName), 'Unable to write successfully');
	}
	
	function testCreatingFilesWithPathsInName() {
		$testString = 'asdf;lkj';
		$testFileName = $this->getTempFileName('temp/XXXXXXtest.txt');
		mkdir($this->getTempFileName('temp'));
		File::create($testFileName)
		      ->write($testString)
		      ->save();
		$this->assertFileExists($testFileName, 'Unable to create or save file');
		$this->assertEquals($testString, file_get_contents($testFileName), 'Unable to write successfully');
	}
	
	function testRaiseExceptionWhenTheFileAlreadyExists() {
		$testString = 'nananananana';
		$testFileName = $this->getTempFileName('nanana.txt');
		File::create($testFileName)->write($testString)->save();
		$this->setExpectedException(
		  'Asar\File\Exception\FileAlreadyExists',
		  "File::create failed. The file '$testFileName' already exists."
	  );
  	File::create($testFileName);
	}
	
	function testOpeningAFile() {
	    $testFileName = $this->getTempFileName('nanananana.js');
		$testString = 'nananananana';
		file_put_contents($testFileName, $testString);
		$obj = File::open($testFileName);
		$this->assertTrue($obj instanceof File);
	    $this->assertEquals($testString, $obj->getContent());
	}
	
	function testRaiseExceptionWhenOpeningAFileThatDoesNotExist() {
		$testFileName = $this->getTempFileName('hahahaha.js');
		$this->setExpectedException(
		  'Asar\File\Exception\FileDoesNotExist',
		  "File::open failed. The file '$testFileName' does not exist."
	  );
		File::open($testFileName);
	}
	
	function testRaiseExceptionWhenNoFileNameIsSpecified() {
		$file = new File;
		$this->setExpectedException(
		  'Asar\File\Exception',
		  "File::getResource failed. The file object does not have a file name."
	  );
	  $file->save();
	}
	
	function testRaiseExceptionWhenSettingInvalidFileNames() {
		$names = array( null, 1, array(1,2,3), '');
		$file = new File;
		foreach ($names as $name) {
		  $this->setExpectedException(
		    'Asar\File\Exception',
		    'File::setFileName failed. Filename should be a non-empty string.'
	    );
		  $file->setFileName($name);
		}
	}
	
	function testRaiseExceptionWhenCreatingAFileOnANonExistentDirectory() {
	  $this->setExpectedException(
		  'Asar\File\Exception\DirectoryNotFound',
		  'File::create failed. Unable to find the directory to create the '.
				'file to (a/non-existent/directory).'
	  );
	  File::create('a/non-existent/directory/file.txt');
	}
	
	function testSettingContentUsingArrayAsArgument() {
	  $content = array('AA', 'BB', 'CC', 'DD');
		$testFileName = $this->getTempFileName('temp/XXXXXXtest.txt');
		mkdir($this->getTempFileName('temp'));
		$file = File::create($testFileName)->write($content)->save();
    $this->assertEquals("AA\nBB\nCC\nDD", $file->getContent());
	}
  
}
