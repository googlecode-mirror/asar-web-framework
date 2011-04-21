<?php

namespace Asar\Tests\Functional\ResourceAndRepresentation {

require_once realpath(__DIR__ . '/../../../../') . '/config.php';

use \Asar\Request;

class Test extends \Asar\Tests\TestCase {
  
  function setUp() {
    
  }
  
  private function getResourceMock($methods = array()) {
    return $this->getMock('Asar\Resource', $methods);
  }
  
  function testResourceAndRepresentationInteraction() {
    $resource = $this->getResourceMock(array('GET'));
    $resource->expects($this->once())
      ->method('GET')
      ->will($this->returnValue('Hello!'));
    $rep = new \FResourceAndRepresentation_Test_RepresentationDummy($resource);
    $response = $rep->handleRequest(new Request);
    $this->assertContains('<h1>GET Hello!</h1>', $response->getContent());
  }
  
  function testResourceAndRepresentationInteractionTxt() {
    $resource = $this->getResourceMock(array('GET'));
    $resource->expects($this->once())
      ->method('GET')
      ->will($this->returnValue('Hello!'));
    $rep = new \FResourceAndRepresentation_Test_RepresentationDummy($resource);
    $response = $rep->handleRequest(
      new Request(array('headers' => array('Accept' => 'text/plain')))
    );
    $this->assertContains("===========\nGET Hello!", $response->getContent());
  }
  
  function testReturn406ResponseForUndefinedTypeMethods() {
    $resource = $this->getResourceMock(array('GET'));
    $resource->expects($this->once())
      ->method('GET')
      ->will($this->returnValue('Hello!'));
    $rep = new \FResourceAndRepresentation_Test_RepresentationDummy($resource);
    $response = $rep->handleRequest(
      new Request(array('headers' => array('Accept' => 'application/json')))
    );
    $this->assertEquals(406, $response->getStatus());
  }
  
}

}

namespace {

  /**
   * Test class
   */
  class FResourceAndRepresentation_Test_RepresentationDummy 
    extends \Asar\Representation {
    
    function getHtml($data) {
      return "<html><head></head><body><h1>GET $data</h1></body></html>";
    }
    
    function getTxt($data) {
      return "===========\nGET $data\n===========\n";
    }
  }
}


