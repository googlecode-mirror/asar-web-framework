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
    $this->temp_path = realpath(dirname(__FILE__).$this->upPath(3)) . 
      DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . '_temp' . 
      DIRECTORY_SEPARATOR;
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
  protected function silenceErrors() {
    $this->unSilenceErrors();
    if (null === $this->old_error_handler) {
      $this->old_error_handler = set_error_handler(
        array(&$this, 'store_error')
      );
    }
  }
  
  /**
   * Error Handler that stores the last error
   * in thrown. This is invoked by silenceErrors
   *
   * @return void
   **/
  function store_error($errno, $errstr, $errfile, $errline) {
    $this->last_error = array('message' => $errstr);
    //echo $errstr;
  }
  
  /**
   * Returns the old error_handler if a new error handler
   * was set and sets the $last_error to null
   *
   * @return void
   **/
  protected function unSilenceErrors() {
    if (null !== $this->old_error_handler) {
      restore_error_handler();
    }
    $this->last_error = null;
  }
  
  protected function getCurrentErrorHandler() {
    $old = set_error_handler(create_function('$errno, $errstr, $errfile, $errline', ''));
    restore_error_handler();
    return $old;
  }
  
  function __destruct() {
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
  
  function testGettingTemporaryTestFilesDir() {
    $dir = Asar_Test_Helper::getTempDir();
    $this->assertEquals($this->temp_path, $dir, 'Temp directory did not match expected path');
    
  }
  
  function testCreatingFiles() {
    $filename = 'dummy.txt';
    Asar_Test_Helper::newFile($filename, '');
    $this->assertFileExists($this->temp_path.$filename, 'The file does not exist');
  }
  
  function testCreatingFilesWithContents() {
    $expected = 'Contents';
    $filename = 'dummy2.txt';
    Asar_Test_Helper::newFile($filename, $expected);
    $this->assertEquals($expected, Asar_Test_Helper::getContents($filename), 'The file does not exist');
  }
  
  function testCreatingAFileAndThenDeletingIt() {
    $filename = 'dummy3.txt';
    Asar_Test_Helper::newFile($filename, ' ');
    Asar_Test_Helper::deleteFile($filename);
    $this->assertFileNotExists($this->temp_path.$filename, 'The test file must no longer exist after deleting it');
  }
  
  function testClearingTemporaryDirectory() {
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
  
  function testClearingWhenNoTempDirectoryIsCreatedMustNotSendError() {
    try {
      Asar_Test_Helper::clearTemp();
    } catch (Exception $e) {
      $this->fail('An Exception was raised. This must run silently.');
    }
  }
  
  function testGettingTheFullPathForFilesCreatedWithnewFile() {
    $file = 'dummy4.txt';
    Asar_Test_Helper::newFile($file, '');
    $this->assertEquals($this->temp_path.$file, Asar_Test_Helper::getPath($file), 'The file path returned is not equal to expected');
  }
  
  function testCreatingDirectories() {
    $dir = 'testDir/subDir/newDir/';
    Asar_Test_Helper::createDir($dir);
    $tempdir = Asar_Test_Helper::getTempDir();
    $this->assertTrue(file_exists($tempdir.'testDir'), 'Did not create first directory');
    $this->assertTrue(file_exists($tempdir.'testDir/subDir'), 'Did not create subdirectory');
    $this->assertTrue(file_exists($tempdir.'testDir/subDir/newDir'), 'Did not create last path');
  }
  
  function testCreatingDirectoryReturnsTheFullPath() {
    $dir = 'a/test/directory';
    $this->assertEquals(
      Asar_Test_Helper::getTempDir().$dir,
      Asar_Test_Helper::createDir($dir),
      'Asar_Test_Helper::createDir() did not return full path of created ' .
      'directory.'
    );
  }
  
  function testCreatingAFileWithASpecifiedPath() {
    $file = 'aFolder/anotherFolder/thefile.txt';
    Asar_Test_Helper::newFile($file, '');
    $tempdir = Asar_Test_Helper::getTempDir();
    $this->assertTrue(file_exists($tempdir.'aFolder'), 'Did not create first directory');
    $this->assertTrue(file_exists($tempdir.'aFolder/anotherFolder'), 'Did not create subdirectory');
    $this->assertTrue(file_exists($tempdir.'aFolder/anotherFolder/thefile.txt'), 'Did not create file');
  }
  
  function testDestructClearsTempDir() {
    $file = 'aFolder/anotherFolder/thefile.txt';
    Asar_Test_Helper::newFile($file, '');
    Asar_Test_Helper::newFile('anotherfile', 'some content');
    eval('class Asar_Test_HelperTest_E extends Asar_Test_Helper {}');
    $testobj = new Asar_Test_HelperTest_E;
    unset($testobj);
    $this->assertFalse(
      file_exists(Asar_Test_Helper::getTempDir()),
      'Destroying an Asar_Test_Helper clears temp directory.'
    );
  }
  
  /**
   * Catching Errors that are triggered by PHP user errors
   * and PHP standard errors
   *
   * @return void
   **/
  function testCatchingErrors() {
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
  }
  
  /**
   * Test Stopping Asar_Test_Helper::watchErrors();
   *
   * @return void
   **/
  function testStopCatchingErrors() {
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
  function testDoNotAttemptCreateDirectoryWhenItAlreadyExists() {
    $dir = 'yet/another/test/directory';
    Asar_Test_Helper::createDir($dir);
    $this->silenceErrors();
    Asar_Test_Helper::createDir($dir);
    $this->assertEquals(null, $this->last_error, 'There should be no errors thrown');
    $test = $this->last_error['message'] . '';
    $this->assertNotContains('mkdir', $test, 'There should be no errors thrown');
  }
  
  function testGettingTheFullPathWhenAFileDoesNotExistMustReturnFalse() {
    $this->assertFalse(
      Asar_Test_Helper::getPath('file_that_does_not_exist'), 
      'The method must return false if the file does not exist'
    );
  }
  
  function testStoringAndRetrievingMockObjectsThroughStaticMethod() {
    $mock = $this->getMock('stdClass');
    Asar_Test_Helper::saveObject('test_mock', $mock);
    $this->assertSame(
      $mock,
      Asar_Test_Helper::getObject('test_mock'),
      'The mock object was not stored and retrieved successfully.'
    );
  }
  
  function testStoringMultipleObjects() {
    $obj1 = $this->getMock('stdClass');
    $obj2 = $this->getMock('stdClass');
    Asar_Test_Helper::saveObject('obj1', $obj1);
    Asar_Test_Helper::saveObject('obj2', $obj2);
    $this->assertSame(
      $obj1,
      Asar_Test_Helper::getObject('obj1'),
      'The obj1 was not stored and retrieved successfully.'
    );
    $this->assertSame(
      $obj2,
      Asar_Test_Helper::getObject('obj2'),
      'The obj2 was not stored and retrieved successfully.'
    );
  }
  
  function testPurgingObjectStore() {
    $obj1 = array(1,2,3);
    $obj2 = array(4,5,6);
    Asar_Test_Helper::saveObject('obj1', $obj1);
    Asar_Test_Helper::saveObject('obj2', $obj2);
    Asar_Test_Helper::purgeSavedObjects();
    $this->assertNotSame(
      $obj1,
      Asar_Test_Helper::getObject('obj1'),
      'The obj1 was not stored and retrieved successfully.'
    );
    $this->assertNotSame(
      $obj2,
      Asar_Test_Helper::getObject('obj2'),
      'The obj2 was not stored and retrieved successfully.'
    );
  }
  
  function testRandomClassNameGenerator() {
    $generated = array();
    for ($i = 0; $i < 10; $i++) {
      $class_name = Asar_Test_Helper::generateRandomClassName();
      $this->assertNotContains(
        $class_name, $generated, "Class name was repeated."
      );
      $generated[] = $class_name;
      $this->assertFalse(
        class_exists($class_name, FALSE),
        "Created a class that already exists!."
      );
      $this->assertTrue(
        strlen((string) $class_name) > 0, 'Must not be an empty string.'
      );
      $this->assertRegExp(
        '/[a-zA-Z_][a-zA-Z0-9_]*/', $class_name, 'Not a valid PHP class name.'
      );
    }
  }
  
  function testRandomClassNameGeneratorSettingPrefix() {
    $class_name = Asar_Test_Helper::generateRandomClassName('Laklak');
    $this->assertTrue(
      strpos($class_name, 'Laklak_') === 0,
      'Did not set prefix on the generated class name.'
    );
  }
  
  function testRandomClassNameGeneratorSetsDefaultPrefix() {
    $class_name = Asar_Test_Helper::generateRandomClassName();
    $this->assertTrue(
      strpos($class_name, 'Amock_') === 0,
      'Did not set a default prefix on the generated class name.'
    );
  }
  
  function testRandomClassNameGeneratorSettingSuffix() {
    $class_name = Asar_Test_Helper::generateRandomClassName('', 'Rapplication');
    $this->assertTrue(
      Asar_Test_Helper::isEndsWith($class_name, '_Rapplication'),
      'Did not set suffix on the generated class name.'
    );
  }
  
  // TODO: Write custom assertions for these and convert functions
  // that depend on these to the custom assertions
  function testIsStartsWith() {
    $tests = array(
      'The quick brown fox'   => 'The',
      'WhenTheGoingGetsTough' => 'WhenTheGoingGetsToug',
      'churpi-churpi-churpi'  => 'churpi-',
      'AAAAANNNOOOOTTHHHEERR' => 'A'
    );
    foreach ($tests as $haystack => $needle)
    {
      $this->assertTrue(
        Asar_Test_Helper::isStartsWith($haystack, $needle),
        "Asar_Test_Helper::isStartsWith() should say that '$needle' " .
        "is found at the beginning of '$haystack'."
      );
    }
  }
  
  function testIsStartsFailures() {
    $tests = array(
      'Rhe quick brown fox'   => 'The',
      'WhenTheGoingGetsTough' => 'Going',
      'WhenTheGoingGetsToug'  => 'WhenTheGoingGetsTough',
      'churpi-churpi-churpi'  => '-churpi',
      'AAAAANNNOOOOTTHHHEERR' => 'AN',
    );
    foreach ($tests as $haystack => $needle)
    {
      $this->assertFalse(
        Asar_Test_Helper::isStartsWith($haystack, $needle),
        "Asar_Test_Helper::isStartsWith() should say that '$needle' " .
        "is NOT found at the beginning of '$haystack'."
      );
    }
  }
  
  function testIsEndsWith() {
    $tests = array(
      'The quick brown fox'   => 'fox',
      'WhenTheGoingGetsTough' => 'TheGoingGetsTough',
      'churpi-churpi-churpi'  => '-churpi',
      'AAAAANNNOOOOTTHHHEERR' => 'THHHEERR',
      'jumps over the lazy'   => 'e lazy'
    );
    foreach ($tests as $haystack => $needle) {
      $this->assertTrue(
        Asar_Test_Helper::isEndsWith($haystack, $needle),
        "Asar_Test_Helper::isEndsWith() should say that '$needle' " .
        "is found at the end of '$haystack'."
      );
    }
  }
  
  function testIsEndsWithFailures() {
    $tests = array(
      'The quick brown fox'   => 'box',
      'The quick brown fox'   => 'The',
      'WhenTheGoingGetsTough' => 'TheGoingGets',
      'TheGoingGetsTough'     => 'WhenTheGoingGetsTough',
      'churpi-churpi-churpi'  => 'churpi-',
      'AAAAANNNOOOOTTHHHEERR' => 'A',
      'jumps over the lazy'   => 'over'
    );
    foreach ($tests as $haystack => $needle) {
      $this->assertFalse(
        Asar_Test_Helper::isEndsWith($haystack, $needle),
        "Asar_Test_Helper::isEndsWith() should say that '$needle' " .
        "is NOT found at the end of '$haystack'."
      );
    }
  }
  
} // END class Asar_TestTest extends PHPUnit_FrameworkTestCase
