<?php

namespace Asar\Tests\Unit;

require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\Templater;
use \Asar\Config;
use \Asar\Response;
use \Asar\Request;

class TemplaterTest extends \Asar\Tests\TestCase {
  
  function setUp() {
    $this->resource = $this->quickMock(
      'Asar\Resource',
      array('handleRequest', 'getConfig', 'importConfig', 'getPermaPath')
    );
    $this->renderer  = $this->quickMock(
      'Asar\TemplateRenderer', array('renderFor')
    );
    $this->conf = $this->quickMock('Asar\Config\ConfigInterface');
    $this->templater = new Templater($this->resource, $this->renderer);
  }
  
  private function resourceReturnsResponse(
    Response $response = null, $use_templates = true
  ) {
    if (!$response) {
      $response = new Response;
    }
    $this->resource->expects($this->any())
      ->method('getConfig')
      ->with('use_templates')
      ->will($this->returnValue($use_templates));
    $this->resource->expects($this->any())
      ->method('handleRequest')
      ->will($this->returnValue($response));
  }
  
  function testTemplaterImplementsAsarConfigInterface() {
    $this->assertInstanceOf('Asar\Config\ConfigInterface', $this->templater);
  }
  
  function testTemplaterDelagatesGetConfigToResource() {
    $this->resource->expects($this->once())
      ->method('getConfig')
      ->with('foo')
      ->will($this->returnValue('bar'));
    $this->assertEquals('bar', $this->templater->getConfig('foo'));
  }
  
  function testTemplaterDelagatesImportConfigToResource() {
    $this->resource->expects($this->once())
      ->method('importConfig')
      ->with($this->conf);
    $this->templater->importConfig($this->conf);
  }
  
  function testIfResourceIsNotAsarConfigImportConfigCreatesConfig() {
    $resource = $this->getMock('Asar\Resource\ResourceInterface');
    $conf = new Config(array('foo' => 'bar'));
    $templater = new Templater($resource, $this->renderer);
    $templater->importConfig($conf);
    $this->assertEquals('bar', $templater->getConfig('foo'));
  }
  
  function testResourceReceivesRequest() {
    $this->resourceReturnsResponse();
    $request = new Request(array('path' => '/foo'));
    $this->resource->expects($this->once())
      ->method('handleRequest')
      ->with($request);
    $this->templater->handleRequest($request);
  }
  
  function testHandleRequestPassesResourceNameAndRequestToLocator() {
    $request = new Request(array( 'path'=> '/foo' ));
    $response = new Response(array('content' => 'Foo.'));
    $this->resourceReturnsResponse($response);
    $this->renderer->expects($this->once())
      ->method('renderFor')
      ->with(get_class($this->resource), $response, $request);
    $this->templater->handleRequest($request);
  }
  
  function testSkipsRendererWhenResourceConfigUseTemplatesIsFalse() {
    $this->resourceReturnsResponse(null, false);
    $this->renderer->expects($this->never())
      ->method('renderFor');
    $this->templater->handleRequest(new Request);
  }
  
  function testUsesResponseFromResourceWhenConfigUseTemplatesIsFalse() {
    $response = new Response(array('content' => 'hello'));
    $this->resourceReturnsResponse($response, false);
    $response2 = $this->templater->handleRequest(new Request);
    $this->assertEquals($response, $response2);
  }
  
  function testReturnsModifiedResponseByRenderer() {
    $request = new Request;
    $this->resourceReturnsResponse();
    $content = "Hello World!";
    $this->renderer->expects($this->once())
      ->method('renderFor')
      ->will($this->returnValue(
        new Response(array('content' => $content))
      ));
    $response = $this->templater->handleRequest($request);
    $this->assertEquals($content, $response->getContent());
  }
  
  function testSkipsRendererWhenResponseFromResourceIsNot200() {
    $response = new Response(array('status' => 405));
    $this->resourceReturnsResponse($response);
    $this->renderer->expects($this->never())
      ->method('renderFor');
    $this->templater->handleRequest(new Request);
  }
  
  function testSkippingRenderingReturnsResponseFromResource() {
    $response = new Response(array('status' => 405));
    $this->resourceReturnsResponse($response);
    $response2 = $this->templater->handleRequest(new Request);
    $this->assertEquals($response, $response2);
  }
  
  function testTemplaterImplementsPathDiscoverInterface() {
    $this->assertInstanceOf(
      'Asar\PathDiscover\PathDiscoverInterface', $this->templater
    );
  }
  
  function testPassesGetPermaPathCallToResource() {
    $options = array('var' => 'val');
    $this->resource->expects($this->once())
      ->method('getPermaPath')
      ->with($options)
      ->will($this->returnValue('/bar/foo'));
    $this->assertEquals('/bar/foo', $this->templater->getPermaPath($options));
  }
  
  function testThrowExceptionWhenResourceReturnsSomethingOtherThanResponse() {
    $this->setExpectedException(
      'Asar\Templater\Exception',
      'Unable to create template. The Resource did not return a response object.'
    );
    $this->resource->expects($this->any())
      ->method('handleRequest')
      ->will($this->returnValue(false));
    $this->templater->handleRequest(new Request);
  }
  
}
