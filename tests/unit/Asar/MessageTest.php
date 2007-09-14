<?php
require_once 'PHPUnit/Framework.php';
require_once 'Asar/Message.php';

class Message_Test_Class extends Asar_Message {}

class Asar_MessageTest extends PHPUnit_Framework_TestCase {

  protected function setUp() {
    $this->req = new Message_Test_Class();
  }
  
  
  function testSetAndGetAddress() {
    $app = 'NewApplication';
    $this->req->setAddress($app);
    
    $this->assertEquals($app, $this->req->getAddress(), 'Wrong value found in address');
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
  
  function testSetAndGetAParam() {
    $testval = 'The quick brown fox jumps over the lazy dog';
    $key = 'test_key';
    
    $this->req->setParam($key, $testval);
    
    $this->assertEquals($testval, $this->req->getParam($key), 'Parameter was not set');
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
  
}


?>
