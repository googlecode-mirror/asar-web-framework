<?php
require_once 'Asar/Test/Helper.php';


/**
 * Test for Asar_Test_Helper
 **/
class Asar_Test_HelperTest extends PHPUnit_Framework_TestCase
{

	private $cleanupList = array();
	
	/**
	 * Stores the last error
	 *
	 * @var array|null
	 **/
	private $last_error = null;
	
	/**
	 * Stores the old error handler when
	 * a new error handler is set using
	 * silenceErrors()
	 *
	 * @var $old_error_handler
	 **/
	private $old_error_handler = null;
	
	function __construct() {
		parent::__construct();
		$this->temp_path = realpath(dirname(__FILE__).$this->upPath(4)).DIRECTORY_SEPARATOR.'_temp'.DIRECTORY_SEPARATOR;
	}
	
	protected function setUp() {
		$this->recursiveDelete($this->temp_path);
		$this->unSilenceErrors();
	}
	
	private function upPath($levels=1) {
		$p = '';
		for($i = 0;$i < $levels;++$i) {
			$p .= DIRECTORY_SEPARATOR.'..';
		}
		return $p;
	}
	
	protected function tearDown() {
	    $this->unSilenceErrors();
		$this->recursiveDelete($this->temp_path);
	}
	
	/**
	 * This is an error handler that suppressses the
	 * display of errors.
	 *
	 * @return bool
	 **/
	protected function silenceErrors()
	{
	    $this->unSilenceErrors();
	    if (null === $this->old_error_handler) {
	        $this->old_error_handler = set_error_handler(
	            array(&$this, 'store_error')
	        );
	    }
	    //echo "\n 999999999999999999\n";
	    //print_r($this->old_error_handler); 
	}
	
	/**
	 * Error Handler that stores the last error
	 * in thrown. This is invoked by silenceErrors
	 *
	 * @return void
	 **/
	public function store_error($errno, $errstr, $errfile, $errline)
	{
	    $this->last_error = array('message' => $errstr);
	    //echo $errstr;
	}
	
	/**
	 * Returns the old error_handler if a new error handler
	 * was set and sets the $last_error to null
	 *
	 * @return void
	 **/
	protected function unSilenceErrors()
	{
	    if (null !== $this->old_error_handler) {
	        restore_error_handler();
        }
        $this->last_error = null;
	}
	
	protected function getCurrentErrorHandler()
	{
	    $old = set_error_handler(create_function('$errno, $errstr, $errfile, $errline', ''));
	    restore_error_handler();
	    return $old;
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
	
	public function testCreatingDirectories()
	{
		$dir = 'testDir/subDir/newDir/';
		Asar_Test_Helper::createDir($dir);
		$tempdir = Asar_Test_Helper::getTempDir();
		$this->assertTrue(file_exists($tempdir.'testDir'), 'Did not create first directory');
		$this->assertTrue(file_exists($tempdir.'testDir/subDir'), 'Did not create subdirectory');
		$this->assertTrue(file_exists($tempdir.'testDir/subDir/newDir'), 'Did not create last path');
	}
	
	public function testCreatingAFileWithASpecifiedPath()
	{
		$file = 'aFolder/anotherFolder/thefile.txt';
		Asar_Test_Helper::newFile($file, '');
		$tempdir = Asar_Test_Helper::getTempDir();
		$this->assertTrue(file_exists($tempdir.'aFolder'), 'Did not create first directory');
		$this->assertTrue(file_exists($tempdir.'aFolder/anotherFolder'), 'Did not create subdirectory');
		$this->assertTrue(file_exists($tempdir.'aFolder/anotherFolder/thefile.txt'), 'Did not create file');
	}
	
	/**
	 * Catching Errors that are triggered by PHP user errors
	 * and PHP standard errors
	 *
	 * @return void
	 **/
	public function testCatchingErrors()
	{
	    $this->silenceErrors();
	    Asar_Test_Helper::watchErrors();
        trigger_error('This is an error', E_USER_WARNING);
        $e = Asar_Test_Helper::getLastError();
        $this->assertEquals('This is an error', $e['message'], 'Error was not caught');
        trigger_error('This is another error', E_USER_WARNING);
        $e = Asar_Test_Helper::getLastError();
        $this->assertEquals('This is another error', $e['message'], 'Error was not caught');
        /**
         * @todo this is just a workaround. Can't return to the old error handler
         */
        //Asar_Test_Helper::stopWatchErrors();
	}
	
	/**
	 * Test Stopping Asar_Test_Helper::watchErrors();
	 *
	 * @return void
	 **/
	public function testStopCatchingErrors()
	{
	    $this->silenceErrors();
	    Asar_Test_Helper::watchErrors();
	    trigger_error('There is an error', E_USER_WARNING);
        trigger_error('This is an error', E_USER_WARNING);
        $e = Asar_Test_Helper::getLastError();
        $this->assertEquals('This is an error', $e['message'], 'Error was not caught');
        Asar_Test_Helper::stopWatchErrors();
        trigger_error('This is another error', E_USER_WARNING);
        $e = Asar_Test_Helper::getLastError();
        $this->assertNotEquals('Asar_Test_Helper', $old_error_handler[0], 'Asar_Test_Helper must no longer handle errors');
        $this->assertEquals('This is an error', $e['message'], 'Error was not caught');
	}
	
	
	/**
	 * Do not attempt to create a directory when it already exists
	 * Prevent issuing of Warning message when it already exists
	 *
	 * @return void
	 **/
	public function testDoNotAttemptCreateDirectoryWhenItAlreadyExists()
	{
        $dir = 'yet/another/test/directory';
        Asar_Test_Helper::createDir($dir);
        $this->silenceErrors();
        Asar_Test_Helper::createDir($dir);
        $this->assertEquals(null, $this->last_error, 'There should be no errors thrown');
        //$this->assertNotContains('mkdir', $this->last_error['message'], 'There should be no errors thrown');
	}
	
	public function testGettingTheFullPathWhenAFileDoesNotExistMustReturnFalse()
	{
		$this->assertFalse(Asar_Test_Helper::getPath('file_that_does_not_exist'), 'The method must return false if the file does not exist');
	}
} // END class Asar_TestTest extends PHPUnit_FrameworkTestCase
