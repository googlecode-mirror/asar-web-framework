<?php

namespace Asar\Tests\Functional\LoggingExample;

require_once realpath(__DIR__ . '/../../../../') . '/config.php';

use \Asar\Client;
use \Asar\ApplicationInjector;
use \Asar\ApplicationScope;
use \Asar\Config\DefaultConfig;

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__);

class Test extends \Asar\Tests\TestCase {

  function setUp() {
    $this->clearTestTempDirectory();
    $this->log_file = $this->getTempDir() . DIRECTORY_SEPARATOR . 'example.log';
    $this->client = new Client;
    $this->app = ApplicationInjector::injectApplication(
      new ApplicationScope(
        'Asar\Tests\Functional\LoggingExample\LoggingExample',
        new DefaultConfig
      )
    );
  }
  
  function tearDown() {
    $this->clearTestTempDirectory();
  }
  
  function testLoggingStoresLogFileInWhateverIsSetInConfig() {
    $response = $this->client->GET($this->app);
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
