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
  
  function testSetAndGetAction() {
    $test_action = 'test-action';
    $this->req->setAction($test_action);
    $this->assertEquals($test_action, $this->req->getAction(), 'Wrong value in action');
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
  
  
  function testSetAndGetParams() {
    $requestVars = array(
      'action' => 'Stupid',
      'var1'   => 'Anything',
      'var2'   => 'Ahahahaha',
      'var3'   => 'Yo!',
      'lover'  => 'Fell in love with a girl'
    );
    
    $this->req->setParams($requestVars);
    
    $test = $this->req->getParams();
    
    $this->assertEquals($requestVars, $test, 'Request params did not match');
  }
  
  function testSetAndGetRequestType() {
    $reqtype = 'html';
    $this->req->setType($reqtype);
    
    $this->assertEquals($reqtype, $this->req->getType(), 'Request type did not match');
  }
  
  function testSetAndGetRequestMethod() {
    $this->req->setMethod(Asar_Request::GET);
    
    $this->assertEquals(Asar_Request::GET, $this->req->getMethod(), 'Request method did not match');
  }
  
  function testSetAndGetAParam() {
    $testval = 'The quick brown fox jumps over the lazy dog';
    $key = 'test_key';
    
    $this->req->setParam($key, $testval);
    
    $this->assertEquals($testval, $this->req->getParam($key), 'Parameter was not set');
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
  
}


?>
