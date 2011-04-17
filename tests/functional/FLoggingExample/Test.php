<?php
require_once realpath(dirname(__FILE__) . '/../../config.php');

use \Asar\Client;
use \Asar\ApplicationInjector;
use \Asar\ApplicationScope;
use \Asar\Config\DefaultConfig;

set_include_path(get_include_path() . PATH_SEPARATOR . dirname(realpath(__FILE__)));

class FLoggingExample_Test extends PHPUnit_Framework_TestCase {

  function setUp() {
    $this->tempdir = Asar::getInstance()->getFrameworkTestsDataTempPath();
    $this->TFM = new Asar_TempFilesManager($this->tempdir);
    $this->TFM->clearTempDirectory();
    $this->log_file = $this->tempdir . DIRECTORY_SEPARATOR . 'example.log';
    $this->client = new Client;
    $this->app = ApplicationInjector::injectApplication(
      new ApplicationScope('LoggingExample', new DefaultConfig)
    );
  }
  
  function tearDown() {
    $this->TFM->clearTempDirectory();
  }
  
  function testLoggingStoresLogFileInWhateverIsSetInConfig() {
    $this->client->GET($this->app);
    $this->assertFileExists($this->log_file);
  }
  
  function testLogShouldMatchFormat() {
    $response = $this->client->GET($this->app);
    $this->assertEquals(200, $response->getStatus(), $response->getContent());
    $contents = file($this->log_file, FILE_IGNORE_NEW_LINES);
    // Matches with [Thu, 11 Oct 2007 12:38:29 GMT] Sample Log text.
    $this->assertRegExp(
      '/^\[[MTWFS][ouehra][neduit], [0-3][0-9] [A-Za-z]{3} [0-9]{4} ' .
      '[0-2][0-9]:[0-6][0-9]:[0-6][0-9] GMT\] This is the first log./', 
      $contents[0]
    );
  }

}
