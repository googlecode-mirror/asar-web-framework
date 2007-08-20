<?php
require_once 'PHPUnit/Framework.php';
require_once 'Asar/Request.php';
 
class Asar_RequestTest extends PHPUnit_Framework_TestCase {

  protected function setUp() {
    $this->req = new Asar_Request();
  }
  
  function testSetAndGetAddress() {
    $app = 'ApplicationName';
    $con = 'ControllerName';
    $this->req->setAddress($app, $con);
    
    $address = $this->req->getAddress();
    
    $this->assertEquals($app, $address['application'], 'Wrong value in address');
    $this->assertEquals($con, $address['controller'], 'Wrong value in address');  
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
  
  function testSetAndGetRequestType() {
    $reqtype = 'html';
    $this->req->setType($reqtype);
    
    $this->assertEquals($reqtype, $this->req->getType(), 'Request type did not match');
  }
  
  function testSetAndGetRequestMethod() {
    $this->req->setMethod(Asar_Request::GET);
    
  }
  
}


?>
