<?php
require_once 'Asar/Test/Helper.php';


/**
 * Test for Asar_Test_Helper
 **/
class Asar_Test_HelperTest extends PHPUnit_Framework_TestCase
{

	private $cleanupList = array();
	
	protected function setUp() {
		$this->temp_path = realpath(dirname(__FILE__).$this->upPath(4)).DIRECTORY_SEPARATOR.'_temp'.DIRECTORY_SEPARATOR;
		$this->recursiveDelete($this->temp_path);
	}
	
	private function upPath($levels=1) {
		$p = '';
		for($i = 0;$i < $levels;++$i) {
			$p .= DIRECTORY_SEPARATOR.'..';
		}
		return $p;
	}
	
	protected function tearDown() {
		$this->recursiveDelete($this->temp_path);
	}
	
	public function __destruct() {
		$this->recursiveDelete($this->temp_path);
	}

	protected function recursiveDelete($folderPath) {
		if (file_exists($folderPath) && is_dir($folderPath)) {
			$contents = scandir($folderPath);
	        foreach ($contents as $value) {
	            if ( $value != "." && $value != ".." ) {
   					$value = $folderPath . "/" . $value;
					if (is_dir($value)) {
	                    self::recursiveDelete($value );
	                } elseif (is_file($value)) {
	                    @unlink ($value);
					}
				}
			}
			return rmdir($folderPath);
		} else {
			return FALSE;
		}
	}
	
	public function testGettingTemporaryTestFilesDir()
	{
		$dir = Asar_Test_Helper::getTempDir();
		$this->assertEquals($this->temp_path, $dir, 'Temp directory did not match expected path');
		
	}
	
	public function testCreatingFiles()
	{
		$filename = 'dummy.txt';
		Asar_Test_Helper::newFile($filename, '');
		$this->assertFileExists($this->temp_path.$filename, 'The file does not exist');
	}
	
	public function testCreatingFilesWithContents()
	{
		$expected = 'Contents';
		$filename = 'dummy2.txt';
		Asar_Test_Helper::newFile($filename, $expected);
		$this->assertEquals($expected, Asar_Test_Helper::getContents($filename), 'The file does not exist');
	}
	
	public function testCreatingAFileAndThenDeletingIt()
	{
		$filename = 'dummy3.txt';
		Asar_Test_Helper::newFile($filename, ' ');
		Asar_Test_Helper::deleteFile($filename);
		$this->assertFileNotExists($this->temp_path.$filename, 'The test file must no longer exist after deleting it');
	}
	
	public function testClearingTemporaryDirectory()
	{
		$files = array('1.txt' => 'one', '2.txt'=>'two', '3.txt'=>'three');
		
		foreach ($files as $file => $content) {
			Asar_Test_Helper::newFile($file, $content);
		}
		// Make sure they exist first
		foreach ($files as $f => $c) {
			$this->assertFileExists($this->temp_path.$f, 'The test file '.$f.' must first exist before deleting it');
		}
		Asar_Test_Helper::clearTemp();
		foreach ($files as $f => $c) {
			$this->assertFileNotExists($this->temp_path.$f, 'The test file '.$f.' must no longer exist after deleting it');
		}
	}
	
	public function testClearingWhenNoTempDirectoryIsCreatedMustNotSendError()
	{
		try {
			Asar_Test_Helper::clearTemp();
		} catch (Exception $e) {
			$this->fail('An Exception was raised. This must run silently.');
		}
	}
	
	public function testGettingTheFullPathForFilesCreatedWithnewFile()
	{
		$file = 'dummy4.txt';
		Asar_Test_Helper::newFile($file, '');
		$this->assertEquals($this->temp_path.$file, Asar_Test_Helper::getPath($file), 'The file path returned is not equal to expected');
		
	}
	
	public function testGettingTheFullPathWhenAFileDoesNotExistMustReturnFalse()
	{
		$this->assertFalse(Asar_Test_Helper::getPath('file_that_does_not_exist'), 'The method must return false if the file does not exist');
	}
} // END class Asar_TestTest extends PHPUnit_FrameworkTestCase
?>