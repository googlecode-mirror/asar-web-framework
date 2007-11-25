<?php
#require_once 'Asar/File.php';

/**
 * Helper class for Asar Framework Tests
 * 
 * This class provides methods to help with tests. Some of these are:
 * - Creating and cleaning up dummy files
 * - assertStringContains
 *
 * @package asar-web-framework
 * @todo Make exceptions friendly
 **/
abstract class Asar_Test_Helper extends PHPUnit_Framework_TestCase
{
	private static $_temp_path = FALSE;
	
	/**
	 * Wrapper method for PHPUnit_Framework_TestCaes runbase to enable custom cleanup
	 * 
	 *
	 * @return void
	 * @todo Create tests for this
	 **/
	public function runBare()
	{
		
		self::clearTemp(); // Make sure we cleanup before we test
		mkdir(self::getTempDir()); // Make the temp directory ready
		parent::runBare();
		self::clearTemp(); // ...and after we test
	}
	
	/**
	 * Cleanup before destruction
	 *
	 * @return void
	 **/
	public function __destruct()
	{
		//self::clearTemp();
	}
	
	
	/**
	 * returns the temporary directory where the dummyfiles are located
	 *
	 * @return string
	 **/
	public static function getTempDir()
	{
		if (!self::$_temp_path) {
			//self::$_temp_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR;
			self::$_temp_path = realpath(dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..').DIRECTORY_SEPARATOR.'_temp'.DIRECTORY_SEPARATOR;
		}
		return self::$_temp_path;
	}
	
	/**
	 * Returns the path of a file declared with newFile()
	 *
	 * @return string
	 **/
	public static function getPath($file)
	{
		$filepath = self::getTempDir().$file;
		if (is_file($filepath)) {
			return $filepath;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * Creates a file on the path specified by getTempDir()
	 *
	 * @return void
	 **/
	public static function newFile($filename, $content)
	{
		if (!file_exists(self::getTempDir())) {
			mkdir(self::getTempDir());
		}
		file_put_contents(self::getTempDir().'/'.$filename, $content);
		
	}
	
	/**
	 * Removes the file with the specified path
	 *
	 * @return void
	 **/
	public static function deleteFile($filename)
	{
		if (file_exists(self::getTempDir().'/'.$filename)) {
			unlink(self::getTempDir().'/'.$filename);
		}
	}
	
	/**
	 * method to delete files and folders recursively
	 *
	 * @return void
	 **/
	private static function recursiveDelete($folderPath) {
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
	
	/**
	 * Clears the temp directory
	 *
	 * @return void
	 **/
	public static function clearTemp() {
		self::recursiveDelete(self::getTempDir());
	}
	
	/**
	 * Returns the content of a file
	 *
	 * @return string
	 **/
	public static function getContents($filename)
	{
		return file_get_contents(self::getTempDir().'/'.$filename);
	}
} // END abstract class Asar_Test_Helper
?>