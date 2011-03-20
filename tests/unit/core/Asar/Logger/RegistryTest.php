<?php

require_once realpath(dirname(__FILE__). '/../../../../config.php');

class Namespace1_ClassName {}
class Namespace1_ClassName2 {}
class Namespace2_ClassName {}

class Asar_Logger_RegistryTest extends PHPUnit_Framework_TestCase {

  function setUp() {
    $this->tempdir = Asar::getInstance()->getFrameworkTestsDataTempPath();
    $this->TFM = new Asar_TempFilesManager($this->tempdir);
    $this->TFM->clearTempDirectory();
    $this->log_file = $this->tempdir . DIRECTORY_SEPARATOR . 'log1.log';
    $this->unsetTestRegisteredNamespaces();
  }
  
  function tearDown() {
    $this->TFM->clearTempDirectory();
    $this->unsetTestRegisteredNamespaces();
  }
  
  private function unsetTestRegisteredNamespaces() {
    Asar_Logger_Registry::unRegister('Namespace1');
    Asar_Logger_Registry::unRegister('Namespace2');
  }
  
  function testRegisteringReturnsTrueIfSuccessful() {
    $this->assertTrue(
      Asar_Logger_Registry::register('Namespace1', $this->log_file)
    );
  }
  
  function testRegisteringThrowsExceptionIfDirectoryOfLogIsNonExistent() {
    $log_file = 'some_none-existent_dir' . DIRECTORY_SEPARATOR . 'log1.log';
    $this->setExpectedException(
      'Asar_Logger_Registry_Exception',
      "Unable to register logger for 'Namespace1' with log file '$log_file'. " .
      "The directory 'some_none-existent_dir' does not exist."
    );
    Asar_Logger_Registry::register('Namespace1', $log_file);
  }
  
  function testGettingALoggerForARegisteredNamespace() {
    Asar_Logger_Registry::register('Namespace1', $this->log_file);
    $this->assertInstanceOf(
      'Asar_Logger_Default',
      Asar_Logger_Registry::getLogger(new Namespace1_ClassName)
    );
  }
  
  function testGettingALoggerCreatesTheLogFileForThatRegisteredNamespace() {
    Asar_Logger_Registry::register('Namespace1', $this->log_file);
    $logger = Asar_Logger_Registry::getLogger(new Namespace1_ClassName);
    $this->assertEquals($this->log_file, $logger->getLogFile()->getFileName());
  }
  
  function testGetLoggerReturnsOnlyOneInstanceForThatSameObject() {
    Asar_Logger_Registry::register('Namespace1', $this->log_file);
    $logger1 = Asar_Logger_Registry::getLogger(new Namespace1_ClassName);
    $logger2 = Asar_Logger_Registry::getLogger(new Namespace1_ClassName);
    $this->assertSame($logger1, $logger2);
  }
  
  function testGetLoggerReturnsOnlyOneInstanceForThatSameNamespace() {
    Asar_Logger_Registry::register('Namespace1', $this->log_file);
    $logger1 = Asar_Logger_Registry::getLogger(new Namespace1_ClassName);
    $logger2 = Asar_Logger_Registry::getLogger(new Namespace1_ClassName2);
    $this->assertSame($logger1, $logger2);
  }
  
  function testGetLoggerReturnsDifferentLoggerInstancesForDifferentNamespace() {
    Asar_Logger_Registry::register('Namespace1', $this->log_file);
    $log_file2 = $this->tempdir . DIRECTORY_SEPARATOR . 'log2.log';
    Asar_Logger_Registry::register('Namespace2', $log_file2);
    $logger1 = Asar_Logger_Registry::getLogger(new Namespace1_ClassName);
    $logger2 = Asar_Logger_Registry::getLogger(new Namespace2_ClassName);
    $this->assertNotSame($logger1, $logger2);
  }
  
  function testGettingLoggerUsingStringAsIdentifier() {
    Asar_Logger_Registry::register('Namespace1', $this->log_file);
    $logger1 = Asar_Logger_Registry::getLogger(new Namespace1_ClassName);
    $logger2 = Asar_Logger_Registry::getLogger('Namespace1');
    $this->assertSame($logger1, $logger2);
  }
  
  function testGettingLoggerThatIsNotRegisteredWillThrowException() {
    $this->setExpectedException(
      'Asar_Logger_Registry_Exception_UnregisteredNamespace',
      "The identifier 'SomeOtherNamespace' was not found in " .
      "the logger registry."
    );
    Asar_Logger_Registry::getLogger('SomeOtherNamespace');
  }
  
  function testUnsettingARegisteredLogger() {
    $this->setExpectedException(
      'Asar_Logger_Registry_Exception_UnregisteredNamespace'
    );
    Asar_Logger_Registry::register('Namespace1', $this->log_file);
    Asar_Logger_Registry::unRegister('Namespace1');
    Asar_Logger_Registry::getLogger('Namespace1');
  }
  
  function testUnsettingARegisteredLoggerRemovesInstancesOfItFromRegistry() {
    Asar_Logger_Registry::register('Namespace1', $this->log_file);
    $logger1 = Asar_Logger_Registry::getLogger('Namespace1');
    Asar_Logger_Registry::unRegister('Namespace1');
    Asar_Logger_Registry::register('Namespace1', $this->log_file);
    $logger2 = Asar_Logger_Registry::getLogger('Namespace1');
    $this->assertNotSame($logger1, $logger2);
  }
  
}
