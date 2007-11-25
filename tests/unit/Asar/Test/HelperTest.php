<?php
require_once 'Asar/Test/Helper.php';


/**
 * Test for Asar_Test_Helper
 **/
class Asar_Test_HelperTest extends PHPUnit_Framework_TestCase
{

	private $cleanupList = array();
	
	protected function setUp() {
		$this->recursiveDelete(realpath('../../_temp'));
		
	}
	
	protected function tearDown() {
		$this->recursiveDelete(realpath('../../_temp'));
	}
	
	public function __destruct() {
		$this->recursiveDelete(realpath('../../_temp'));
	}

	protected function recursiveDelete($folderPath) {
		if (is_dir($folderPath)) {
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
		$this->assertEquals(realpath('../../_temp').'/', $dir, 'Temp directory did not match expected path');
		
	}
	
	
	public function testCreatingFiles()
	{
		$filename = 'dummy.txt';
		Asar_Test_Helper::newFile($filename, '');
		$this->assertFileExists(realpath('../../_temp/'.$filename), 'The file does not exist');
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
		$this->assertFileNotExists(realpath('../../_temp/'.$filename), 'The test file must no longer exist after deleting it');
	}
	
	public function testClearingTemporaryDirectory()
	{
		$files = array('1.txt' => 'one', '2.txt'=>'two', '3.txt'=>'three');
		
		foreach ($files as $file => $content) {
			Asar_Test_Helper::newFile($file, $content);
		}
		Asar_Test_Helper::clearTemp();
		foreach ($files as $f => $c) {
			$this->assertFileNotExists(realpath('../../_temp/').'/'.$f, 'The test file '.$f.' must no longer exist after deleting it');
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
		$this->assertEquals(realpath('../../_temp').'/'.$file, Asar_Test_Helper::getPath($file), 'The file path returned is not equal to expected');
		
	}
	
	public function testGettingTheFullPathWhenAFileDoesNotExistMustReturnFalse()
	{
		$this->assertFalse(Asar_Test_Helper::getPath('file_that_does_not_exist'), 'The method must return false if the file does not exist');
	}
} // END class Asar_TestTest extends PHPUnit_FrameworkTestCase
?>