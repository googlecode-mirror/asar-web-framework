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
	
	function testGetIsTheDefaultMethod() {
		$this->assertEquals(Asar_Request::GET, $this->req->getMethod(), 'Request method did not match');
	}
 
	function testSetAndGetRequestMethodPost() {
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

	function testSettingGetRequestMethodWithString() {
		$this->req->setMethod('GET');
		$this->assertEquals(Asar_Request::GET, $this->req->getMethod(), 'Request method did not match');
	}

	function testSettingPostRequestMethodWithString() {
		$this->req->setMethod('POST');
		$this->assertEquals(Asar_Request::POST, $this->req->getMethod(), 'Request method did not match');
	}

	function testSettingHeadRequestMethodWithString() {
		$this->req->setMethod('HEAD');
		$this->assertEquals(Asar_Request::HEAD, $this->req->getMethod(), 'Request method did not match');
	}

	function testSettingPutRequestMethodWithString() {
		$this->req->setMethod('PUT');
		$this->assertEquals(Asar_Request::PUT, $this->req->getMethod(), 'Request method did not match');
	}

	function testSettingDeleteRequestMethodWithString() {
		$this->req->setMethod('DELETE');
		$this->assertEquals(Asar_Request::DELETE, $this->req->getMethod(), 'Request method did not match');
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

	function testGettingContext() {
		$respondent = $this->getMock('Asar_Requestable', array('processRequest'));
		$this->req->sendTo($respondent);
		$this->assertSame($respondent, $this->req->getContext(), 'Getting Context failed');
	}
	 
	 function testSettingAndGettingHost() {
	 	$uri = 'www.hostexample.com';
	 	$this->req->setHost($uri);
	 	$this->assertEquals($uri, $this->req->getHost(), 'Unable to obtain correct host');
	 }
 
	 function testSettingAndGettingPath() {
	 	$path = '/test/testing/stupid.txt';
	 	$this->req->setPath($path);
	 	$this->assertEquals($path, $this->req->getPath(), 'Unable to set path');
	 }
 
	 function testSettingAndGettingPathAgain() {
	 	$path = '/beat/right/change.txt';
	 	$this->req->setPath($path);
	 	$this->assertEquals($path, $this->req->getPath(), 'Unable to set path');
	 }

	function testGettingPathAsArray() {
		$path = '/this/is/a/test/right.txt';
		$this->req->setPath($path);
		$this->assertEquals(
			array('/', 'this', 'is', 'a', 'test', 'right.txt'),
			$this->req->getPathArray(),
			'Unable to get path as array'
		);
	}
	
	function testGettingPathAsArrayWithTrailingSlash() {
		$path = '/this/is/a/test/right/';
		$this->req->setPath($path);
		$this->assertEquals(
			array('/', 'this', 'is', 'a', 'test', 'right'),
			$this->req->getPathArray(),
			'Unable to get path as array'
		);
	}
	
	function testSettingPathWithDoubleSlashWillResultInExceptionThrown() {
		$this->setExpectedException('Asar_Request_Exception');
		$path = '/this/is/a//test/right/';
		$this->req->setPath($path);
	}
	
	function testSettingPathWithoutSlashAtBeginningThrowsException() {
		$this->setExpectedException('Asar_Request_Exception');
		$path = 'this/is/a/test/right/';
		$this->req->setPath($path);
	}
  
  function testSetAndGetType() {
    $path = '/test/testing/stupid.txt';
    $this->req->setPath($path);
    $this->assertEquals('txt', $this->req->getType(), 'Type did not match');
  }
  
  function testSetAndGetRequestTypeTxt() {
    $path = '/test/testing/stupid.txt';
    $this->req->setPath($path);
    $this->assertEquals('text/plain', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeDefault() {
    $path = '/test/testing/interesting';
    $this->req->setPath($path);
    $this->assertEquals('text/html', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeDefaultSlash() {
    $path = '/test/testing/interesting/';
    $this->req->setPath($path);
    $this->assertEquals('text/html', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypePhp() {
    $path = '/test/testing/cool.php';
    $this->req->setPath($path);
    $this->assertEquals('text/html', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeHtml() {
    $path = '/test/testing/cool.html';
    $this->req->setPath($path);
    $this->assertEquals('text/html', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeHtm() {
    $path = '/test/testing/cool.htm';
    $this->req->setPath($path);
    $this->assertEquals('text/html', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeRss() {
    $path = '/test/testing/cool.rss';
    $this->req->setPath($path);
    $this->assertEquals('application/xml', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeXhtml() {
    $path = '/test/testing/cool.xhtml';
    $this->req->setPath($path);
    $this->assertEquals('application/xhtml+xml', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeXhr() {
    $path = '/test/testing/cool.xhr';
    $this->req->setPath($path);
    $this->assertEquals('text/plain', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeJson() {
    $path = '/test/testing/cool.json';
    $this->req->setPath($path);
    $this->assertEquals('application/json', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
}


?>
