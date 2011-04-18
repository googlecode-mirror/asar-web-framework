<?php

namespace Asar\Tests\Unit;

require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\Client;
use \Asar\Request;
use \Asar\Response;

class ClientTest extends \Asar\Tests\TestCase {
  
  function setUp() {
    $this->client = new Client;
    $this->server = $this->getMock(
      'Asar\Resource\ResourceInterface', array('handleRequest')
    );
  }
  
  function testClientSendsRequestToServer() {
    $request = new Request;
    $this->server->expects($this->once())
      ->method('handleRequest')
      ->with($request);
    $this->client->sendRequest($this->server, $request);
  }
  
  /**
   * @dataProvider dataClientSendsRequest
   */
  function testClientSendsRequest($method, $options) {
    $request = new Request($options);
    $request->setMethod($method);
    $this->server->expects($this->once())
      ->method('handleRequest')
      ->with($request);
    call_user_func_array(
      array($this->client, $method), array($this->server, $options)
    );
  }
  
  function dataClientSendsRequest() {
    return array(
      array(
        'GET', array('path' => '/foo', 'params' => array('bar' => 'baz'))
      ),
      array(
        'POST', array('path' => '/foo/bar', 'params' => array('foo' => 'bar'))
      ),
      array(
        'PUT', array('path' => '/bar/baz', 'params' => array('boo' => 'far'))
      ),
      array(
        'DELETE', array('path' => '/del/ete', 'params' => array('joo' => 'jar'))
      )
    );
  }
  
  function testClientReturnsResponseFromServer() {
    $response = new Response;
    $this->server->expects($this->once())
      ->method('handleRequest')
      ->will($this->returnValue($response));
    $this->assertSame(
      $response, $this->client->sendRequest($this->server, new Request)
    );
  }
  
  function testHelperMethodsReturnsResponseFromServer() {
    $response = new Response;
    $this->server->expects($this->any())
      ->method('handleRequest')
      ->will($this->returnValue($response));
    foreach(array('GET', 'POST', 'PUT', 'DELETE') as $method) {
      $this->assertSame(
        $response, call_user_func(array($this->client, $method), $this->server)
      );
    }
  }
  
  /**
   * @dataProvider dataClientThrowsExceptionWhenServerIsUnknown
   */
  function testClientThrowsExceptionWhenServerIsUnknown($method, $server) {
    $this->setExpectedException('Asar\Client\Exception\UnknownServerType');
    call_user_func_array(
      array($this->client, $method), array($server, array())
    );
  }
  
  function dataClientThrowsExceptionWhenServerIsUnknown() {
    return array(
      array('GET', 'foo'),
      array('POST', new \stdClass),
      array('PUT', null), 
      array('DELETE', 1)
    );
  }
  
  function testClientSendsRequestWithModifiedOptions() {
    $this->server->expects($this->any())
      ->method('handleRequest')
      ->with($this->equalTo(
        new Request(array('headers' => array('Accept' => 'text/plain')))
      ));
    $this->client->GET(
      $this->server, array('headers' => array('Accept' => 'text/plain'))
    );
  }
  
}
