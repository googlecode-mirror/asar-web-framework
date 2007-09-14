<?php
require_once 'PHPUnit/Framework.php';
require_once 'Asar/Request.php';
 
class Asar_RequestTest extends PHPUnit_Framework_TestCase {

  protected function setUp() {
    $this->req = new Asar_Request();
  }
  
  function testSetAndGetContent() {
    $requestVars = array(
      'action' => 'Stupid',
      'var1'   => 'Anything',
      'var2'   => 'Ahahahaha',
      'var3'   => 'Yo!',
      'lover'  => 'Fell in love with a girl'
    );
    
    $this->req->setContent($requestVars);
    
    $test = $this->req->getContent();
    
    $this->assertEquals($requestVars, $test, 'Request contents did not match');
  }
  
  function testSetAndGetRequestMethod() {
    $this->req->setMethod(Asar_Request::GET);
    
    $this->assertEquals(Asar_Request::GET, $this->req->getMethod(), 'Request method did not match');
  }
  
  function testSetAndGetRequestType() {
    $reqtype = 'html';
    $this->req->setType($reqtype);
    
    $this->assertEquals($reqtype, $this->req->getType(), 'Request type did not match');
  }
  
  function testAddParams() {
    $requestVars = array(
      'action' => 'Stupid',
      'var1'   => 'Anything',
      'var2'   => 'Ahahahaha',
      'var3'   => 'Yo!',
      'lover'  => 'Fell in love with a girl'
    );
    
    $toAdd = array(
      'stupid' => 'boy',
      'crazy'  => 'girl'
    );
    
    $this->req->setParams($requestVars);
    $this->req->setParams($toAdd);
    
    $this->assertEquals(array_merge($requestVars, $toAdd), $this->req->getParams(), 'Unable to set and add params');
  }
  
  function testSendTo() {
    $respondent = $this->getMock('Asar_Requestable', array('processRequest'));
    $respondent->expects($this->once())
               ->method('processRequest')
               ->with($this->req);
    $this->req->sendTo($respondent);
  }
  
  function testSendToWithArguments() {
    $arguments = array('test' => 'right', 'been' => array(1,2,3));
    $respondent = $this->getMock('Asar_Requestable', array('processRequest'));
    $respondent->expects($this->once())
               ->method('processRequest')
               ->with($this->req, $arguments);
    $this->req->sendTo($respondent, $arguments);
    
  }
  
}


?>
