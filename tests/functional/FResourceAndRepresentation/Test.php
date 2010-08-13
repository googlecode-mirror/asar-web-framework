<?php
require_once realpath(dirname(__FILE__) . '/../../config.php');

/**
 * Test class
 */
class FResourceAndRepresentation_Test_RepresentationDummy 
  extends Asar_Representation {
  
  function getHtml($data) {
    return "<html><head></head><body><h1>GET $data</h1></body></html>";
  }
  
  function getTxt($data) {
    return "===========\nGET $data\n===========\n";
  }
}

class FResourceAndRepresentation_Test extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    
  }
  
  private function getResourceMock($methods = array()) {
    return $this->getMock('Asar_Resource', $methods);
  }
  
  function testResourceAndRepresentationInteraction() {
    $resource = $this->getResourceMock(array('GET'));
    $resource->expects($this->once())
      ->method('GET')
      ->will($this->returnValue('Hello!'));
    $rep = new FResourceAndRepresentation_Test_RepresentationDummy($resource);
    $response = $rep->handleRequest(new Asar_Request);
    $this->assertContains('<h1>GET Hello!</h1>', $response->getContent());
  }
  
  function testResourceAndRepresentationInteractionTxt() {
    $resource = $this->getResourceMock(array('GET'));
    $resource->expects($this->once())
      ->method('GET')
      ->will($this->returnValue('Hello!'));
    $rep = new FResourceAndRepresentation_Test_RepresentationDummy($resource);
    $response = $rep->handleRequest(
      new Asar_Request(array('headers' => array('Accept' => 'text/plain')))
    );
    $this->assertContains("===========\nGET Hello!", $response->getContent());
  }
  
  function testReturn406ResponseForUndefinedTypeMethods() {
    $resource = $this->getResourceMock(array('GET'));
    $resource->expects($this->once())
      ->method('GET')
      ->will($this->returnValue('Hello!'));
    $rep = new FResourceAndRepresentation_Test_RepresentationDummy($resource);
    $response = $rep->handleRequest(
      new Asar_Request(array('headers' => array('Accept' => 'application/json')))
    );
    $this->assertEquals(406, $response->getStatus());
  }
  
}


