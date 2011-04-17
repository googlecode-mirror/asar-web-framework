<?php

require_once realpath(dirname(__FILE__). '/../../../../config.php');

use Asar\Logger\Registry;

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
    Registry::unRegister('Namespace1');
    Registry::unRegister('Namespace2');
  }
  
  function testRegisteringReturnsTrueIfSuccessful() {
    $this->assertTrue(
      Registry::register('Namespace1', $this->log_file)
    );
  }
  
  function testRegisteringThrowsExceptionIfDirectoryOfLogIsNonExistent() {
    $log_file = 'some_none-existent_dir' . DIRECTORY_SEPARATOR . 'log1.log';
    $this->setExpectedException(
      'Asar\Logger\Registry\Exception',
      "Unable to register logger for 'Namespace1' with log file '$log_file'. " .
      "The directory 'some_none-existent_dir' does not exist."
    );
    Registry::register('Namespace1', $log_file);
  }
  
  function testGettingALoggerForARegisteredNamespace() {
    Registry::register('Namespace1', $this->log_file);
    $this->assertInstanceOf(
      'Asar\Logger\DefaultLogger',
      Registry::getLogger(new Namespace1_ClassName)
    );
  }
  
  function testGettingALoggerCreatesTheLogFileForThatRegisteredNamespace() {
    Registry::register('Namespace1', $this->log_file);
    $logger = Registry::getLogger(new Namespace1_ClassName);
    $this->assertEquals($this->log_file, $logger->getLogFile()->getFileName());
  }
  
  function testGetLoggerReturnsOnlyOneInstanceForThatSameObject() {
    Registry::register('Namespace1', $this->log_file);
    $logger1 = Registry::getLogger(new Namespace1_ClassName);
    $logger2 = Registry::getLogger(new Namespace1_ClassName);
    $this->assertSame($logger1, $logger2);
  }
  
  function testGetLoggerReturnsOnlyOneInstanceForThatSameNamespace() {
    Registry::register('Namespace1', $this->log_file);
    $logger1 = Registry::getLogger(new Namespace1_ClassName);
    $logger2 = Registry::getLogger(new Namespace1_ClassName2);
    $this->assertSame($logger1, $logger2);
  }
  
  function testGetLoggerReturnsDifferentLoggerInstancesForDifferentNamespace() {
    Registry::register('Namespace1', $this->log_file);
    $log_file2 = $this->tempdir . DIRECTORY_SEPARATOR . 'log2.log';
    Registry::register('Namespace2', $log_file2);
    $logger1 = Registry::getLogger(new Namespace1_ClassName);
    $logger2 = Registry::getLogger(new Namespace2_ClassName);
    $this->assertNotSame($logger1, $logger2);
  }
  
  function testGettingLoggerUsingStringAsIdentifier() {
    Registry::register('Namespace1', $this->log_file);
    $logger1 = Registry::getLogger(new Namespace1_ClassName);
    $logger2 = Registry::getLogger('Namespace1');
    $this->assertSame($logger1, $logger2);
  }
  
  function testGettingLoggerThatIsNotRegisteredWillThrowException() {
    $this->setExpectedException(
      'Asar\Logger\Registry\Exception\UnregisteredNamespace',
      "The identifier 'SomeOtherNamespace' was not found in " .
      "the logger registry."
    );
    Registry::getLogger('SomeOtherNamespace');
  }
  
  function testUnsettingARegisteredLogger() {
    $this->setExpectedException(
      'Asar\Logger\Registry\Exception\UnregisteredNamespace'
    );
    Registry::register('Namespace1', $this->log_file);
    Registry::unRegister('Namespace1');
    Registry::getLogger('Namespace1');
  }
  
  function testUnsettingARegisteredLoggerRemovesInstancesOfItFromRegistry() {
    Registry::register('Namespace1', $this->log_file);
    $logger1 = Registry::getLogger('Namespace1');
    Registry::unRegister('Namespace1');
    Registry::register('Namespace1', $this->log_file);
    $logger2 = Registry::getLogger('Namespace1');
    $this->assertNotSame($logger1, $logger2);
  }
  
}
