<?php

namespace Asar\Tests;

require_once realpath(dirname(__FILE__). '/../../config.php');

use \Asar\Tests\TestServerManager;

class TestServerManagerTest extends \Asar\Tests\TestCase {
  
  function setUp() {
    $this->A = new \Asar;
    $this->TFM = $this->getTFM();
    $this->TFM->clearTempDirectory();
    $this->TSM = new TestServerManager($this->A->getFrameworkTestsDataPath());
    $this->test_server_path = $this->A->getFrameworkTestsDataPath() . 
      DIRECTORY_SEPARATOR . 'test-server';
    $this->clearTestServer();
  }
  
  private function constructRealPath() {
    $args = func_get_args();
    return realpath(implode(DIRECTORY_SEPARATOR, $args));
  }
  
  function tearDown() {
    $this->clearTestServer();
    $this->TFM->clearTempDirectory();
  }
  
  private function clearTestServer() {
    if (file_exists($this->test_server_path)) {
      unlink($this->test_server_path);
    }
  }

  function testSetupFixtures() {
    $this->TSM->setUp(array('fixture' => 'normal'));
    $this->assertEquals(
      $this->constructRealPath(
        $this->A->getFrameworkTestsDataServerFixturesPath(), 'normal'
      ),
      realpath($this->test_server_path),
      'The test-server directory does not ' .
        'point to the normal fixture directory.'      
    );
  }
  
  function testSetupWithFullPath() {
    $subdirs = implode(DIRECTORY_SEPARATOR, array('foo', 'bar', 'baz', 'boor'));
    $this->TFM->newDir($subdirs);
    $serverdir = $this->TFM->getPath($subdirs);
    $this->TSM->setUp(array('path' => $serverdir));
    $this->assertEquals(
      realpath($serverdir), realpath($this->test_server_path),
      'The test-server directory does not ' .
        'point to the specified directory.'
    );
  }
  
  function testClearTestServer() {
    $this->TSM->setUp(array('fixture' => 'normal'));
    $this->TSM->clearTestServer();
    $this->assertNotEquals(
      $this->constructRealPath(
        $this->A->getFrameworkTestsDataServerFixturesPath(), 'normal'
      ),
      realpath($this->test_server_path),
      'The test-server directory does not ' .
        'point to the normal fixture directory.'      
    );
  }
}
