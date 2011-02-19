<?php
require_once realpath(dirname(__FILE__). '/../../../../config.php');

class Asar_HttpServer_FsocketTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    global $argv;
    
    $this->A = new Asar;
    $this->TSM = new Asar_TestServerManager($this->A->getFrameworkTestsDataPath());
    $this->markTestSkipped();
    if (!$this->TSM->isCanConnectToTestServer()) {
      $this->markTestSkipped(
        'Unable to connect to test server. Check server setup.'
      );
    }
    $this->TSM->clearTestServer();
    $this->TSM->setUp(array('fixture' => 'normal'));
    $this->host = 'http://asar-test.local';
    $this->server = new Asar_HttpServer_Fsocket($this->host);
  }
  
  function tearDown() {
    $this->TSM->clearTestServer();
  }
  
  private function constructPath() {
    $args = func_get_args();
    return implode(DIRECTORY_SEPARATOR, $args);
  }
  
  function testFirstConnection() {
    $response = $this->server->handleRequest(new Asar_Request);
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
  
}