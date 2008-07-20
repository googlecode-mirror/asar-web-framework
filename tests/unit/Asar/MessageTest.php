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
	
	function testGettingAParamThatDoesNotExistMustReturnNull()
	{
		$this->assertSame(null, $this->req->getParam('non-existent-param'), 'The non-existent parameter must return null');  
	}
	
	function testInvokingToStringMethodOfMessageReturnsContent() {
		$content = 'hello world';
		$this->req->setContent($content);
		$this->assertEquals($content, $this->req->__toString(), 'Invoking toString method did not return something properly');
	}
	
	function testAnotherInvokingToStringMethodOfMessageReturnsContent() {
		$content = 'this is a good place to die';
		$this->req->setContent($content);
		$this->assertEquals($content, $this->req->__toString(), 'Invoking toString method did not return something properly');
	}
	
  	
	function testGettingStringContentOfMessageWithArrayedContentGetsConcatenatedStrings() {
		$contents = array(
			'action' => 'Stupid',
			'var1'   => 'Anything',
			'var2'   => 'Ahahahaha',
			'var3'   => 'Yo!',
			'lover'  => 'Fell in love with a girl'
		);
		
		$this->req->setContent($contents);
		$expected = implode("\n", $contents);
		$this->assertEquals($expected, $this->req->__toString(), 'Invoking toString did not return concatenated strings');
	}
	
	function testGettingStringContentOfMessageWhenContentIsNullReturnsAnEmptyString() {
		$this->req->setContent(null);
		ob_start();
		echo $this->req;
		$test = ob_get_clean();
		$this->assertEquals('', $test, 'Did not return an empty string as expected');
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
    $this->assertEquals('application/rss+xml', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeXhtml() {
    $this->req->setType('xhtml');
    $this->assertEquals('application/xhtml+xml', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeXhr() {
    $this->req->setType('xhr');
    // @todo make sure xhr is the right mime-type ('text/plain' or 'text/javascript') 
    $this->assertEquals('text/plain', $this->req->getMimeType(), 'Mime-type did not match');
  }
  
  function testSetAndGetRequestTypeJson() {
    $this->req->setType('json');
    $this->assertEquals('application/json', $this->req->getMimeType(), 'Mime-type did not match');
  }
    
    function testGettingSupportedMimeTypes()
    {
        $types = array(
    		'html'   => 'text/html',
    		'htm'    => 'text/html',
    		'php'    => 'text/html',
    		'rss'    => 'application/rss+xml',
    		'xml'    => 'application/xml',
    		'xhtml'  => 'application/xhtml+xml',
    		'txt'    => 'text/plain',
    		'xhr'    => 'text/plain',
    		'css'    => 'text/css',
    		'js'     => 'text/javascript',
    		'json'   => 'application/json',
    		);
    	$this->assertEquals($types, Asar_Message::getSupportedTypes(), 'The list of supported types is not as expected');
    }
    
    function testGettingHtmlTypeFromMimeType()
    {
        $this->req->setMimeType('text/html');
        $this->assertEquals('html', $this->req->getType(), 'The type is not html after setting mime-type to "text/html"!');
    }
    
    function testGettingRssTypeForMimeTypeRSS()
    {
        $this->req->setMimeType('application/rss+xml');
        $this->assertEquals('rss', $this->req->getType(), 'The type is not rss after setting mime-type to "application/rss+xml"!');
    }
    
    function testGettingXmlTypeForMimeTypeXml()
    {
        $this->req->setMimeType('application/xml');
        $this->assertEquals('xml', $this->req->getType(), 'The type is not xml after setting mime-type to "application/xml"!');
    }
    
    function testGettingTxtTypeForMimeTypeTxt()
    {
        $this->req->setMimeType('text/plain');
        $this->assertEquals('txt', $this->req->getType(), 'The type is not txt after setting mime-type to "text/plain"!');
    }
    
    function testGettingCssTypeForMimeTypeCss()
    {
        $this->req->setMimeType('text/css');
        $this->assertEquals('css', $this->req->getType(), 'The type is not css after setting mime-type to "text/css"!');
    }
    
    function testGettingJsTypeForMimeTypeJs()
    {
        $this->req->setMimeType('text/javascript');
        $this->assertEquals('js', $this->req->getType(), 'The type is not js after setting mime-type to "text/javascript"!');
    }
    
    function testGettingJsonTypeForMimeTypeJson()
    {
        $this->req->setMimeType('application/json');
        $this->assertEquals('json', $this->req->getType(), 'The type is not json after setting mime-type to "application/json"!');
    }
    
    function testGettingNullWhenThereIsNoMimeTypeSet()
    {
        $this->assertEquals(null, $this->req->getType(), 'The type is not null when there is no mime-type set');
    }
}
