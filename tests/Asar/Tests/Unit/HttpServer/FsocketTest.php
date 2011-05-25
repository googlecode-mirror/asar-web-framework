<?php

namespace Asar\Tests\Unit\HttpServer;

require_once realpath(dirname(__FILE__). '/../../../../config.php');

use \Asar\HttpServer\Fsocket as FsocketHttpServer;
use \Asar\Tests\TestServerManager;
use \Asar\Request;
use \Asar;

class FsocketTest extends \Asar\Tests\TestCase {
  
  function setUp() {
    global $argv;
    
    $this->A = new Asar;
    $this->TSM = new TestServerManager(
      $this->A->getFrameworkTestsDataPath()
    );
    $this->TSM->clearTestServer();
    $this->TSM->setUp(array('fixture' => 'normal'));
    $this->host = 'http://asar-test.local';
    $this->server = new FsocketHttpServer($this->host);
  }
  
  function tearDown() {
    $this->TSM->clearTestServer();
  }
  
  private function constructPath() {
    $args = func_get_args();
    return implode(DIRECTORY_SEPARATOR, $args);
  }
  
  function testFirstConnection() {
    if (!$this->TSM->isCanConnectToTestServer()) {
      $this->markTestSkipped(
        'Unable to connect to test server. Check server setup.'
      );
    }
    $response = $this->server->handleRequest(new Request);
    $this->assertEquals(200, $response->getStatus());
    $this->assertContains('text/html', $response->getHeader('Content-Type'));
    $this->assertEquals(
      file_get_contents(
        $this->constructPath(
          $this->A->getFrameworkTestsDataServerFixturesPath(),
          'normal', 'index.html'
        )
      ),
      $response->getContent()
    );
  }
  
  function testSettingHostCleansHost() {
    $server = new FsocketHttpServer('http://somewhere.com/');
    $this->assertEquals('somewhere.com', $server->getHostName());
  }
  
  function testSettingHostCleansHost2() {
    $server = new FsocketHttpServer('somewhere.com');
    $this->assertEquals('somewhere.com', $server->getHostName());
  }
  
}
