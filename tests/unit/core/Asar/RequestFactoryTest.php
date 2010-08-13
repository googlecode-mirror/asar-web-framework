<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

class Asar_RequestFactoryTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->factory = new Asar_RequestFactory();
  }
  
  function testCreatingRequest() {
    $SERVER = array();
    $SERVER['REQUEST_METHOD'] = 'POST';
	  $SERVER['REQUEST_URI']  = '/a_page';
	  $request = $this->factory->createRequest($SERVER);
	  $this->assertType('Asar_Request', $request);
    $this->assertEquals('POST', $request->getMethod());
    $this->assertEquals('/a_page', $request->getPath());
  }
  
  function testCreatingRequestWithParams() {
    $SERVER = array();
    $SERVER['REQUEST_METHOD'] = 'DELETE';
	  $SERVER['REQUEST_URI']  = '/foo';
	  
	  $GET = array(
	    'foo' => 'bar',
	    'boo' => 'far'
	  );
	  $request = $this->factory->createRequest($SERVER, $GET);
	  $this->assertType('Asar_Request', $request);
    $this->assertEquals('DELETE', $request->getMethod());
    $this->assertEquals('/foo', $request->getPath());
    $this->assertEquals($GET, $request->getParams());
  }
  
  function testCreatingRequestWithPostVars() {
    $SERVER = array();
    $SERVER['REQUEST_METHOD'] = 'POST';
	  $SERVER['REQUEST_URI']  = '/foo';
	  $POST = array(
	    'foo' => 'bar',
	    'boo' => 'far'
	  );
	  $request = $this->factory->createRequest($SERVER, array(), $POST);
	  $this->assertType('Asar_Request', $request);
    $this->assertEquals($POST, $request->getContent());
  }
  
  function testCreatingRequestDoesNotSetContentWhenMethodIsPost() {
    $SERVER = array();
    $SERVER['REQUEST_METHOD'] = 'GET';
	  $SERVER['REQUEST_URI']  = '/foo';
	  $POST = array(
	    'foo' => 'bar',
	    'boo' => 'far'
	  );
	  $request = $this->factory->createRequest($SERVER, array(), $POST);
	  $this->assertType('Asar_Request', $request);
    $this->assertEquals(null, $request->getContent());
  }
  
  function testCreatingRequestSetsHeaders() {
    $SERVER = array(
      'REQUEST_METHOD'       => 'GET',
      "HTTP_HOST"            => 'localhost',
      "HTTP_USER_AGENT"      => 'Mozilla/5.0 (X11; U; Linux i686;)',
      "HTTP_ACCEPT"          => "text/html,application/xml;q=0.9,*/*;q=0.8",
      "HTTP_ACCEPT_LANGUAGE" => 'tl,en-us;q=>0.7,en;q=0.3',
      "HTTP_ACCEPT_ENCODING" => 'gzip,deflate',
      "HTTP_ACCEPT_CHARSET"  => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
      "HTTP_KEEP_ALIVE"    => '300',
      "HTTP_CONNECTION"    => 'keep-alive',
      'REQUEST_URI'      => 'somewhere_over_the_rainbow'
    );
	  $headers = $this->factory->createRequest($SERVER)->getHeaders();
    $this->assertEquals('localhost', $headers['Host']);
    $this->assertEquals('Mozilla/5.0 (X11; U; Linux i686;)', $headers['User-Agent']);
    $this->assertEquals("text/html,application/xml;q=0.9,*/*;q=0.8", $headers['Accept']);
    $this->assertEquals('tl,en-us;q=>0.7,en;q=0.3', $headers['Accept-Language']);
    $this->assertEquals('gzip,deflate', $headers['Accept-Encoding']);
    $this->assertEquals('ISO-8859-1,utf-8;q=0.7,*;q=0.7', $headers['Accept-Charset']);
    $this->assertEquals('300', $headers['Keep-Alive']);
    $this->assertEquals('keep-alive', $headers['Connection']);
  }
}