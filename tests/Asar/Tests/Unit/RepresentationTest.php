<?php

namespace Asar\Tests\Unit;

require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\Representation;
use \Asar\Request;
use \Asar\Response;
use \Asar\Config;

class RepresentationTest extends \Asar\Tests\TestCase {

  private $types = array('Txt', 'Html', 'Xml', 'Json');

  function setUp() {
    $this->resource = $this->getMock('Asar\Resource');
  }
  
  private function resourceExpectsOnceHandleRequest() {
    return $this->resource->expects($this->once())->method('handleRequest');
  }
  
  private function resourceWillReturnResponse($response) {
    return $this->resourceExpectsOnceHandleRequest()
      ->will($this->returnValue($response));
  }
  
  private function mockRepresentation($methods = array()) {
    return $this->getMock(
      'Asar\Representation', $methods , array($this->resource)
    );
  }
  
  function testRepresentationDecoratesResource() {
    $request = new Request(array('path' => '/foo'));
    $response = new Response(array('content' => 'bar'));
    $this->resourceWillReturnResponse($response)
      ->with($this->equalTo($request));
    $R = new Representation($this->resource);
    $this->assertSame($response, $R->handleRequest($request));
  }
  
  function testRepresentationUsesContentFromResource() {
    $response = new Response(array('content' => 'Hello!'));
    $this->resourceWillReturnResponse($response);
    $R = $this->mockRepresentation(array('getHtml'));
    $R->expects($this->once())
      ->method('getHtml')
      ->with($this->equalTo('Hello!'));
    $R->handleRequest(new Request);
  }
  
  function testRepresentationInvokesGetTxtMethodForTxtRequest() {
    $response = new Response(array('content' => 'Hi!'));
    $this->resourceWillReturnResponse($response);
    $R = $this->mockRepresentation(array('getTxt'));
    $R->expects($this->once())
      ->method('getTxt')
      ->with($this->equalTo('Hi!'));
    $R->handleRequest(new Request(
      array('headers' => array('accept' => 'text/plain'))
    ));
  }
  
  /**
   * @dataProvider dataOnlyInvokesRespectiveMethodPerRequestType
   */
  function testOnlyInvokesRespectiveMethodPerRequestType(
    $type, $method, $type_method
  ) {
    $response = new Response(array('content' => 'Foo!'));
    $this->resourceWillReturnResponse($response);
    $type_methods = $this->getAllPossibleTypeMethods($this->types);
    $R = $this->mockRepresentation($type_methods);
    foreach ($type_methods as $amethod) {
      if ($amethod != $type_method) {
        $R->expects($this->never())->method($amethod);
      }
    }
    $R->expects($this->once())
      ->method($type_method)
      ->with($this->equalTo('Foo!'));
    $R->handleRequest(new Request(
      array(
        'headers' => array('accept' => $type),
        'method'  => $method
      )
    ));
  }
  
  
  function dataOnlyInvokesRespectiveMethodPerRequestType() {
    return array(
      array('text/plain', 'GET', 'getTxt'),
      array('text/html', 'GET', 'getHtml'),
      array('application/xml', 'GET', 'getXml'),
      array('application/json', 'GET', 'getJson'),
      array('text/plain', 'POST', 'postTxt'),
      array('text/html', 'POST', 'postHtml'),
      array('application/xml', 'POST', 'postXml'),
      array('application/json', 'POST', 'postJson'),
      array('application/json', 'PUT', 'putJson'),
    );
  }
  
  function getAllPossibleTypeMethods($types) {
    $methods = array('get', 'post', 'put');
    $all_type_methods = array();
    foreach ($methods as $method) {
      foreach ($types as $type) {
        $all_type_methods[] = $method . $type;
      }
    }
    return $all_type_methods;
  }
  
  function testUseOutputFromTypeMethodAsContentForResponse() {
    $response = new Response(array('content' => 'Baz.'));
    $this->resourceWillReturnResponse($response);
    $R = $this->mockRepresentation(array('getTxt'));
    $R->expects($this->once())
      ->method('getTxt')
      ->will($this->returnCallback(array($this, 'getTxtDummy')));
    $this->assertEquals(
      'Bar Baz.', $R->handleRequest(new Request(
        array('headers' => array('Accept' => 'text/plain'))
      ))->getContent()
    );
  }
  
  function getTxtDummy($data) {
    return 'Bar ' . $data;
  }
  
  function testReturn406StatusForUnknownTypes() {
    $this->resourceWillReturnResponse(new Response);
    $R = $this->mockRepresentation(array('getTxt'));
    $this->assertEquals(406, $R->handleRequest(new Request)->getStatus());
  }
  
  function testRepresentationRunsSetupCodeOnConstruction() {
    $cname = $this->generateAppName('_RunSetup');
    eval('
      class '. $cname . ' extends \Asar\Representation {
        protected function setUp() {
          $_POST["foo"] = "bar";
        }
      }
    ');
    $R = new $cname($this->resource);
    $this->assertTrue(array_key_exists('foo', $_POST));
    $this->assertEquals('bar', $_POST['foo']);
  }
  
  function testRepresentationIsConfig() {
    $R = new Representation($this->resource);
    $this->assertInstanceOf('Asar\Config\ConfigInterface', $R);
  }
  
  function testImportConfigPassesConfigToResource() {
    $R = new Representation($this->resource);
    $config = new Config( array('yo' => 'ya') );
    $this->resource->expects($this->once())
      ->method('importConfig')
      ->with($config);
    $R->importConfig($config);
  }
  
  function testImportConfigDoesNotPassConfigToResourceIfNotConfigInterface() {
    $resource = $this->getMock('Asar\Resource\ResourceInterface');
    $R = new Representation($resource);
    $config = new Config( array('yo' => 'ya') );
    $R->importConfig($config);
  }
  
  function testImportingConfigurationDoesNotOverrideInternalConfig() {
    $classname = $this->generateAppName('_Configuration');
    eval('
      class ' . $classname . ' extends \Asar\Representation {
        protected function setUp() {
          $this->config["foo"] = "baz";
        }
      }
    ');
    $R = new $classname($this->resource);
    $R->importConfig(new Config(array('foo' => 'bar', 'doo' => 'jar')));
    $this->assertEquals('jar', $R->getConfig('doo'));
    $this->assertEquals('baz', $R->getConfig('foo'));
  }

}
