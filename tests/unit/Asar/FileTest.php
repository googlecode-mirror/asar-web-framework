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
require_once 'Asar/Test/Helper.php';

require_once 'Asar/File.php';

class Asar_FileTest extends Asar_Test_Helper {
	
	public function testSettingFileName() {
		
		$testFileName = self::getTempDir().'AAAAARD';
		
		$file = new Asar_File();
		$file->setFileName($testFileName);
		$this->assertEquals($testFileName, $file->getFileName(), 'Filename returned is not the same');
	}
	
	public function testSimpleCreateFile() {
		$testString = 'This is a test';
		$testFileName = self::getTempDir().'Asar_FileTesting.txt';
		
		$file = Asar_File::create($testFileName);
		
		//$this->assertFalse(file_exists($testFileName), 'Created file  prematurely or was unable to cleanup properly');
		
		$file->write($testString)
		     ->save();
    
    	$this->assertTrue(file_exists($testFileName), 'Unable to create file');
	    $this->assertEquals($testString, file_get_contents($testFileName), 'Contents of file is not correct');
	}
	
	public function testLongWayToCreateFile() {
		
		$testString = 'This is just a string';
		$testFileName = self::getTempDir().'GAAnotherFileToTest.txt';
		
		$file = new Asar_File();
		$file->setFileName($testFileName);

		$file->write($testString);
		$file->save();
		
        
        $this->assertTrue(file_exists($testFileName), 'Unable to create file');
        $this->assertEquals($testString, file_get_contents($testFileName), 'Contents of file is not correct');
	}
	
	public function testOpeningAndGettingContents() {
				
		$testString = 'Different operating system families have different line-ending conventions. When you write a text file and want to insert a line break, you need to use the correct line-ending character(s) for your operating system.';
		$testFileName = self::getTempDir().'GAAnotherFileToTest.txt';
		
		file_put_contents($testFileName, $testString);
        $this->assertTrue(file_exists($testFileName), 'Unable to create test file');
        
		$file = new Asar_File($testFileName);
		$this->assertEquals($testString, $file->getContent(), 'Contents did not match');
	}
	
	public function testStaticUnlink() {
		
		$testFileName = self::getTempDir().'Suchadirtyword';
		$testString = '';
		
		
		file_put_contents($testFileName, $testString);
        $this->assertTrue(file_exists($testFileName), 'Unable to create test file');
        
        $file = Asar_File::unlink($testFileName);
        
        $this->assertFalse(file_exists($testFileName), 'Unable to delete the file');
	}
	
	public function testDeleting() {
		
		$testFileName = self::getTempDir().'Suchadirtywordaaa.txt';
		$testString = 'asdfsadf';
		
		
		file_put_contents($testFileName, $testString);
    	$this->assertTrue(file_exists($testFileName), 'Unable to create test file');
    
    
	    $file = new Asar_File($testFileName);
	    $file->write($testString)->save();
	    $file->delete();
    
	    $this->assertFalse(file_exists($testFileName), 'Unable to delete the file');
	}
	
	public function testWritingBeforeAndAfter() {
		
		$testString = 'XXX';
		$testFileName = self::getTempDir().'Asar_FileTesting.txt';
		
		Asar_File::create($testFileName)
		      ->write($testString)
		      ->writeBefore('BBBB')
		      ->writeAfter('CCC')
		      ->save();
		
        
		$this->assertTrue(file_exists($testFileName), 'Unable to create or save file');
		$this->assertEquals('BBBBXXXCCC', file_get_contents($testFileName), 'Unable to write successfully');
	}
	
	public function testManyWrites() {
		$testString = 'XXX';
		$testFileName = self::getTempDir().'Asar_FileTesting.txt';
		
		$testfile = Asar_File::create($testFileName);
		$testfile->write($testString)->save();
		$testfile->write('iii')->save();
		$testfile->write('ABCDEFG')->save();
		$this->assertTrue(file_exists($testFileName), 'Unable to create or save file');
		$this->assertEquals('ABCDEFG', file_get_contents($testFileName), 'Unable to write successfully');
	}
	
	public function testManyWritesButInAppendMode() {
		$testString = 'XXX';
		$testFileName = self::getTempDir().'Asar_FileTesting.txt';
		
		$testfile = Asar_File::create($testFileName)->appendMode();
		$testfile->write($testString)->save();
		$testfile->write('iii')->save();
		$testfile->write('ABCDEFG')->save();
		$this->assertTrue(file_exists($testFileName), 'Unable to create or save file');
		$this->assertEquals('XXXiiiABCDEFG', file_get_contents($testFileName), 'Unable to write successfully');
	}
	
	public function testCreatingFilesWithPathsInName() {
		$testString = 'asdf;lkj';
		$testFileName = self::getTempDir().'temp/XXXXXXtest.txt';
		
		mkdir(self::getTempDir().'/temp');
		
		Asar_File::create($testFileName)
		      ->write($testString)
		      ->save();
		
		$this->assertTrue(file_exists($testFileName), 'Unable to create or save file');
		$this->assertEquals($testString, file_get_contents($testFileName), 'Unable to write successfully');
	}
  
}
?>
