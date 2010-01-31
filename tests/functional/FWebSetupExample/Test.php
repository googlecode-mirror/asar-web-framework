<?php
require_once realpath(dirname(__FILE__) . '/../../config.php');

class FWebSetupExample_Test extends PHPUnit_Framework_TestCase
{
  
  function setUp() {
    if (!Asar_Test_Server::isCanConnectToTestServer())
    $this->markTestSkipped('Unable to connect to test server. Check server setup.');
    $this->client = new Asar_Client;
    Asar_Test_Server::setUp(array(
      'path' =>  Asar::constructRealPath(dirname(__FILE__), 'web')
    ));
    $this->client->setServer('http://asar-test.local/');
  }
  
  function testRegularRequest() {
    $response = $this->client->GET('/');
    $this->assertEquals(
      200, $response->getStatus()
    );
    $contents = $response->getContent();
    //var_dump($contents);
    $this->assertContains(
      '<html>', $contents,
      'Contents were not properly sent.'
    );
    $this->assertContains(
      'Hello World!', $contents,
      'Expected string was not found on output.'
    );
    $this->assertEquals(
      'text/html', $response->getHeader('Content-Type'),
      'Application did not set the content-type header properly.'
    );
  }
  
  function testRegularRequest2() {
    $response = $this->client->GET('/hello');
    $this->assertEquals(200, $response->getStatus());
    $contents = $response->getContent();
    //var_dump($contents);
    $this->assertNotContains('<html>', $contents);
    $this->assertEquals('Hello there!', $contents);
    $this->assertEquals('text/html', $response->getHeader('Content-Type'));
  }
  
  function testNonExistentResource() {
    $response = $this->client->GET('/nowhere');
    $this->assertEquals( 404, $response->getStatus());
  }
  
  function testNoAvailableRepresentation() {
    $response = $this->client->GET('/', array(), array('Accept' => 'unknown/type'));
    $this->assertEquals( 406, $response->getStatus());
  }
  
  function testRequestWithComplexAcceptHeader() {
    $response = $this->client->GET('/', array(), array('Accept' => 
      'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
    ));
    $this->assertEquals( 200, $response->getStatus());
  }
  
}
