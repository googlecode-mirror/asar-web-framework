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
  
  function testSetAndGetType() {
  	$this->req->setType('txt');
  	$this->assertEquals('txt', $this->req->getType(), 'Type mismatch');
    $this->assertEquals('text/plain', $this->req->getMimeType(), 'Mime-type did not match');
    $this->req->setType('html');
    $this->assertEquals('html', $this->req->getType(), 'Type mismatch');
    $this->assertEquals('text/html', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeTxt() {
    $this->req->setType('txt');
    $this->assertEquals('text/plain', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeDefault() {
    $this->assertEquals('text/html', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeDefaultSlash() {
    $this->req->setType('');
    $this->assertEquals('text/html', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypePhp() {
    $this->req->setType('php');
    $this->assertEquals('text/html', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeHtml() {
    $this->req->setType('html');
    $this->assertEquals('text/html', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeHtm() {
    $this->req->setType('htm');
    $this->assertEquals('text/html', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeRss() {
    $this->req->setType('rss');
    $this->assertEquals('application/xml', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeXhtml() {
    $this->req->setType('xhtml');
    $this->assertEquals('application/xhtml+xml', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeXhr() {
    $this->req->setType('xhr');
    // @todo: make sure xhr is the right mime-type ('text/plain' or 'text/javascript') 
    $this->assertEquals('text/plain', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeJson() {
    $this->req->setType('json');
    $this->assertEquals('application/json', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
}


?>
