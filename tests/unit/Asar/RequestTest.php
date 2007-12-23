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
  
	function testSetAndGetRequestMethodPOST() {
		$this->req->setMethod(Asar_Request::POST);
		$this->assertEquals(Asar_Request::POST, $this->req->getMethod(), 'Request method must be POST');
	}

	function testSetAndGetRequestMethodPUT() {
		$this->req->setMethod(Asar_Request::PUT);
		$this->assertEquals(Asar_Request::PUT, $this->req->getMethod(), 'Request method must be PUT');
	}

	function testSetAndGetRequestMethodDELETE() {
		$this->req->setMethod(Asar_Request::DELETE);
		$this->assertEquals(Asar_Request::DELETE, $this->req->getMethod(), 'Request method must be DELETE');
	}
	
	function testSetAndGetRequestMethodHEAD() {
		$this->req->setMethod(Asar_Request::HEAD);
		$this->assertEquals(Asar_Request::HEAD, $this->req->getMethod(), 'Request method must be DELETE');
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
  /*
  function testSettingAndGettingUri() {
  	$uri = '/'
  }*/
  
  function testSetAndGetType() {
    $uri = 'test/testing/stupid.txt';
    $this->req->setUri($uri);
    $this->assertEquals('txt', $this->req->getType(), 'Type did not match');
  }
  
  function testSetAndGetRequestTypeTxt() {
    $uri = 'test/testing/stupid.txt';
    $this->req->setUri($uri);
    $this->assertEquals('text/plain', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeDefault() {
    $uri = 'test/testing/interesting';
    $this->req->setUri($uri);
    $this->assertEquals('text/html', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeDefaultSlash() {
    $uri = 'test/testing/interesting/';
    $this->req->setUri($uri);
    $this->assertEquals('text/html', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypePhp() {
    $uri = 'test/testing/cool.php';
    $this->req->setUri($uri);
    $this->assertEquals('text/html', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeHtml() {
    $uri = 'test/testing/cool.html';
    $this->req->setUri($uri);
    $this->assertEquals('text/html', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeHtm() {
    $uri = 'test/testing/cool.htm';
    $this->req->setUri($uri);
    $this->assertEquals('text/html', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeRss() {
    $uri = 'test/testing/cool.rss';
    $this->req->setUri($uri);
    $this->assertEquals('application/xml', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeXhtml() {
    $uri = 'test/testing/cool.xhtml';
    $this->req->setUri($uri);
    $this->assertEquals('application/xhtml+xml', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeXhr() {
    $uri = 'test/testing/cool.xhr';
    $this->req->setUri($uri);
    $this->assertEquals('text/plain', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeJson() {
    $uri = 'test/testing/cool.json';
    $this->req->setUri($uri);
    $this->assertEquals('application/json', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
}


?>
