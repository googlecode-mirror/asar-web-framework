<?php
/**
 * Created on Jul 2, 2007
 * 
 * @author     Wayne Duran
 */
require_once realpath(dirname(__FILE__). '/../../config.php');

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
		$this->assertEquals($testString, $file->getContent());
		$this->assertEquals($testString, $file->getContents());
		$this->assertEquals($testString, $file->read());
	}
	
	public function testStaticUnlink() {
		
		$testFileName = self::getTempDir().'Suchadirtyword';
		$testString = '';
		file_put_contents($testFileName, $testString);
        $this->assertTrue(file_exists($testFileName), 'Unable to create test file');
        
        $file = Asar_File::unlink($testFileName);
        
        $this->assertFalse(file_exists($testFileName), 'Unable to delete the file');
	}
	
	public function testStaticUnlinkReturnsFalseWhenFileDoesNotExist()
	{
	    $testFileName = self::getTempDir().'Nothingnothing';
	    $this->assertFalse(
	        Asar_File::unlink($testFileName),
	        'Asar_File::unlink() did not return false for non-existent-file.'
        );
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
	
	public function testRaiseExceptionWhenTheFileAlreadyExists()
	{
		$testString = 'nananananana';
		$testFileName = self::getTempDir().'nanana.txt';
		
		Asar_File::create($testFileName)->write($testString)->save();
		try {
			Asar_File::create($testFileName);
		} catch (Exception $e) {
			$this->assertEquals(
				'Asar_File_Exception', get_class($e),
				'Asar_File did not raise the right exception for duplicate files'
			);
			$this->assertEquals(
				"Asar_File::create failed. The file '$testFileName' already exists.",
				$e->getMessage(),
				'Asar_File did not set the correct exception message for duplicate files'
			);
		}
	}
	
	public function testOpeningAFile()
	{
	    $testFileName = self::getTempDir().'nanananana.js';
		$testString = 'nananananana';
		file_put_contents($testFileName, $testString);
		$obj = Asar_File::open($testFileName);
		$this->assertTrue($obj instanceof Asar_File);
	    $this->assertEquals($testString, $obj->getContent());
	}
	
	public function testRaiseExceptionWhenOpeningAFileThatDoesNotExist()
	{
		$testFileName = self::getTempDir().'hahahaha.js';
		
		try {
			Asar_File::open($testFileName);
		} catch (Exception $e) {
			$this->assertEquals(
				'Asar_File_Exception', get_class($e),
				'Asar_File did not raise the right exception for opening non-existent files'
			);
			$this->assertEquals(
				"Asar_File::open failed. The file '$testFileName' does not exist.",
				$e->getMessage(),
				'Asar_File did not set the correct exception message for opening non-existent files'
			);
		}
	}
	
	public function testRaiseExceptionWhenNoFileNameIsSpecified()
	{
		$file = new Asar_File;
		try {
			$file->save();
		} catch (Exception $e) {
			$this->assertEquals(
				'Asar_File_Exception', get_class($e),
				'Asar_File did not raise the right exception for file object with no file name.'
			);
			$this->assertEquals(
				"Asar_File::getResource failed. The file object does not have a file name.",
				$e->getMessage(),
				'Asar_File did not set the correct exception message for file object with no file name.'
			);
			return;
		}
		$this->fail('Did not raise Asar_File_Exception');
	}
	
	public function testRaiseExceptionWhenSettingInvalidFileNames()
	{
		$names = array(
			null, 1, array(1,2,3), ''
		);
		$file = new Asar_File;
		foreach ($names as $name) {
			try {
				$file->setFileName($name);
			} catch (Exception $e) {
				$this->assertEquals(
					'Asar_File_Exception', get_class($e),
					'Asar_File did not raise the right exception for setting an invalid file name.'
				);
				$this->assertEquals(
					'Asar_File::setFileName failed. Filename should be a '.
					'non-empty string.',
					$e->getMessage(),
					'Asar_File did not set the correct exception message for setting an invalid file name.'
				);
			}
		}
	}
	
	function testRaiseExceptionWhenCreatingAFileOnANonExistentDirectory() {
	  try {
	    Asar_File::create('a/non-existent/directory/file.txt');
    } catch (Asar_File_Exception $e) {
      $this->assertEquals(
        'Asar_File_Exception_DirectoryNotFound', get_class($e)
      );
      $this->assertEquals(
				'Asar_File::create failed. Unable to find the directory to create the '.
				'file to (a/non-existent/directory).',
				$e->getMessage()
			);
			return;
    }
    $this->fail('Did not raise expected exception (Asar_File_Exception).');
	}
	
	public function testSettingContentUsingArrayAsArgument() {
	    $content = array('AA', 'BB', 'CC', 'DD');
		$testFileName = self::getTempDir().'temp/XXXXXXtest.txt';
		
		mkdir(self::getTempDir().'/temp');
		
		$file = Asar_File::create($testFileName)
            ->write($content)
            ->save();
        $this->assertEquals("AA\nBB\nCC\nDD", $file->getContent());
	}
  
}
