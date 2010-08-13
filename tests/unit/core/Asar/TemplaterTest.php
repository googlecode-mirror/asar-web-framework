<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

class Asar_TemplaterTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->resource = $this->getMock(
      'Asar_Resource', array('handleRequest', 'getConfig')
    );
    $this->renderer  = $this->getMock(
      'Asar_TemplateRenderer', array('renderFor'), array(), '', false
    );
    $this->templater = new Asar_Templater($this->resource, $this->renderer);
  }
  
  private function resourceReturnsResponse(
    Asar_Response $response = null, $use_templates = true
  ) {
    if (!$response) {
      $response = new Asar_Response;
    }
    $this->resource->expects($this->any())
      ->method('getConfig')
      ->with('use_templates')
      ->will($this->returnValue($use_templates));
    $this->resource->expects($this->any())
      ->method('handleRequest')
      ->will($this->returnValue($response));
    
  }
  
  function testResourceReceivesRequest() {
    $this->resourceReturnsResponse();
    $request = new Asar_Request(array('path' => '/foo'));
    $this->resource->expects($this->once())
      ->method('handleRequest')
      ->with($request);
    $this->templater->handleRequest($request);
  }
  
  function testHandleRequestPassesResourceNameAndRequestToLocator() {
    $request = new Asar_Request(array( 'path'=> '/foo' ));
    $response = new Asar_Response(array('content' => 'Foo.'));
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
    $this->templater->handleRequest(new Asar_Request);
  }
  
  function testUsesResponseFromResourceWhenConfigUseTemplatesIsFalse() {
    $response = new Asar_Response(array('content' => 'hello'));
    $this->resourceReturnsResponse($response, false);
    $response2 = $this->templater->handleRequest(new Asar_Request);
    $this->assertEquals($response, $response2);
  }
  
  function testReturnsModifiedResponseByRenderer() {
    $request = new Asar_Request;
    $this->resourceReturnsResponse();
    $content = "Hello World!";
    $this->renderer->expects($this->once())
      ->method('renderFor')
      ->will($this->returnValue(
        new Asar_Response(array('content' => $content))
      ));
    $response = $this->templater->handleRequest($request);
    $this->assertEquals($content, $response->getContent());
  }
  
  function testSkipsRendererWhenResponseFromResourceIsNot200() {
    $response = new Asar_Response(array('status' => 405));
    $this->resourceReturnsResponse($response);
    $this->renderer->expects($this->never())
      ->method('renderFor');
    $this->templater->handleRequest(new Asar_Request);
  }
  
  function testSkippingRenderingReturnsResponseFromResource() {
    $response = new Asar_Response(array('status' => 405));
    $this->resourceReturnsResponse($response);
    $response2 = $this->templater->handleRequest(new Asar_Request);
    $this->assertEquals($response, $response2);
  }
  
}
