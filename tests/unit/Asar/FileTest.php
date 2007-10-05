<?php
/**
 * Created on Jul 2, 2007
 * 
 * @author     Wayne Duran
 */

// Call TemplateTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "TemplateTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Asar/File.php';

class Asar_FileTest extends PHPUnit_Framework_TestCase {
	
	protected $cleanupList = array();
	
	
	protected function setUp() {
		$this->cleanUp();
		
	}
	
	protected function tearDown() {
		$this->cleanUp();
	}

	protected function cleanUp() {
		foreach ($this->cleanupList as $f) {
			if (is_dir($f)) {
				rmdir($f);
			} elseif (file_exists($f)) {
				unlink($f);
			}
		}
		$this->cleanupList = array();
	}
	
	public function __destruct() {
		$this->cleanUp();
	}
	
	public function testSettingFileName() {
		
		$testFileName = 'AAAAARD';
		
		$file = new Asar_File();
		$file->setFileName($testFileName);
		$this->assertEquals($testFileName, $file->getFileName(), 'Filename returned is not the same');
	}
	
	public function testSimpleCreateFile() {
		$testString = 'This is a test';
		$testFileName = 'Asar_FileTesting.txt';
		
		$file = Asar_File::create($testFileName);
		
		//$this->assertFalse(file_exists($testFileName), 'Created file  prematurely or was unable to cleanup properly');
		
		$file->write($testString)
		     ->save();
    // Add this to cleanup
    $this->cleanupList[] = $testFileName;
    
    $this->assertTrue(file_exists($testFileName), 'Unable to create file');
    $this->assertEquals($testString, file_get_contents($testFileName), 'Contents of file is not correct');
	}
	
	public function testLongWayToCreateFile() {
		
		$testString = 'This is just a string';
		$testFileName = 'GAAnotherFileToTest.txt';
		
		$file = new Asar_File();
		$file->setFileName($testFileName);

		$file->write($testString);
		$file->save();
		
        // Add this to cleanup
        $this->cleanupList[] = $testFileName;
        
        $this->assertTrue(file_exists($testFileName), 'Unable to create file');
        $this->assertEquals($testString, file_get_contents($testFileName), 'Contents of file is not correct');
	}
	
	public function testOpeningAndGettingContents() {
				
		$testString = 'Different operating system families have different line-ending conventions. When you write a text file and want to insert a line break, you need to use the correct line-ending character(s) for your operating system.';
		$testFileName = 'GAAnotherFileToTest.txt';
		
		file_put_contents($testFileName, $testString);
        $this->assertTrue(file_exists($testFileName), 'Unable to create test file');
        
        $this->cleanupList[] = $testFileName;
        
		$file = new Asar_File($testFileName);
		$this->assertEquals($testString, $file->getContent(), 'Contents did not match');
	}
	
	public function testStaticUnlink() {
		
		$testFileName = 'Suchadirtyword';
		$testString = '';
		
		
		file_put_contents($testFileName, $testString);
        $this->assertTrue(file_exists($testFileName), 'Unable to create test file');
        
        $this->cleanupList[] = $testFileName;
        
        $file = Asar_File::unlink($testFileName);
        
        $this->assertFalse(file_exists($testFileName), 'Unable to delete the file');
	}
	
	public function testDeleting() {
		
		$testFileName = 'Suchadirtywordaaa.txt';
		$testString = 'asdfsadf';
		
		
		file_put_contents($testFileName, $testString);
    $this->assertTrue(file_exists($testFileName), 'Unable to create test file');
    
    $this->cleanupList[] = $testFileName;
    
    $file = new Asar_File($testFileName);
    $file->write($testString)->save();
    $file->delete();
    
    $this->assertFalse(file_exists($testFileName), 'Unable to delete the file');
	}
	
	public function testWritingBeforeAndAfter() {
		
		$testString = 'XXX';
		mkdir('temp');
		$testFileName = 'temp/Asar_FileTesting.txt';
		
		Asar_File::create($testFileName)
		      ->write($testString)
		      ->writeBefore('BBBB')
		      ->writeAfter('CCC')
		      ->save();
		
        $this->cleanupList[] = $testFileName;
        $this->cleanupList[] = 'temp';
        
		$this->assertTrue(file_exists($testFileName), 'Unable to create or save file');
		$this->assertEquals('BBBBXXXCCC', file_get_contents($testFileName), 'Unable to write successfully');
	}
	
	public function testManyWrites() {
		$testString = 'XXX';
		$testFileName = 'Asar_FileTesting.txt';
		
		$testfile = Asar_File::create($testFileName);
        $this->cleanupList[] = $testFileName;
		$testfile->write($testString)->save();
		$testfile->write('iii')->save();
		$testfile->write('ABCDEFG')->save();
		$this->assertTrue(file_exists($testFileName), 'Unable to create or save file');
		$this->assertEquals('ABCDEFG', file_get_contents($testFileName), 'Unable to write successfully');
	}
	
	public function testManyWritesButInAppendMode() {
		$testString = 'XXX';
		$testFileName = 'Asar_FileTesting.txt';
		
		$testfile = Asar_File::create($testFileName)->appendMode();
        $this->cleanupList[] = $testFileName;
		$testfile->write($testString)->save();
		$testfile->write('iii')->save();
		$testfile->write('ABCDEFG')->save();
		$this->assertTrue(file_exists($testFileName), 'Unable to create or save file');
		$this->assertEquals('XXXiiiABCDEFG', file_get_contents($testFileName), 'Unable to write successfully');
	}
	
	public function testCreatingFilesWithPathsInName() {
		$testString = 'asdf;lkj';
		$testFileName = 'temp/XXXXXXtest.txt';
		
		mkdir('temp');
		
		$this->cleanupList[] = $testFileName;
		$this->cleanupList[] = 'temp';
		
		Asar_File::create($testFileName)
		      ->write($testString)
		      ->save();
		
		$this->assertTrue(file_exists($testFileName), 'Unable to create or save file');
		$this->assertEquals($testString, file_get_contents($testFileName), 'Unable to write successfully');
	}
  
}
?>
