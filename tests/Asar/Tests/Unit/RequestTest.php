<?php

namespace Asar\Tests\Unit;

require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\Request;
use \Asar\Utility\String;

class RequestTest extends \Asar\Tests\TestCase {
  
  function setUp() {
    $this->R = new Request;
  }
  
  function testRequestShouldImplementAsarRequestInterface() {
    $this->assertInstanceOf('Asar\Request\RequestInterface', $this->R);
  }
  
  function testRequestShouldBeAbleToSetPath() {
    $this->R->setPath('/path/to/page');
    $this->assertEquals(
      '/path/to/page', $this->R->getPath(),
      'Unable to set path on Request object'
    );
  }
  
  function testRequestDefaultsToIndexPath() {
    $this->assertEquals(
      '/', $this->R->getPath(),
      'Path does not default to index ("/").'
    );
  }
  
  function testRequestShouldBeAbleToSetMethod() {
    $this->R->setMethod('POST');
    $this->assertEquals(
      'POST', $this->R->getMethod(),
      'Unable to set method on Request object'
    );
  }
  
  function testRequestShouldDefaultToGetMethodOnInitialization() {
    $this->assertEquals(
      'GET', $this->R->getMethod(),
      'Method does not default to GET on Initialization'
    );
  }
  
  function testSettingRequestParameters() {
    $this->R->setParams(array('foo' => 'bar', 'fruit' => 'apple'));
    $params = $this->R->getParams();
    $this->assertEquals(
      'bar', $params['foo'],
      'Foo param in request params not found'
    );
    $this->assertEquals(
      'apple', $params['fruit'],
      'Fruit param in request params not found'
    );
  }
  
  function testRequestShouldDefaultToHtmlContentType() {
    $this->assertEquals(
      'text/html', $this->R->getHeader('Accept'),
      'Content-type does not default to "text/html" on initialization.'
    );
  }
  
  function testRequestSettingContentOnCreation() {
    $R = new Request(array('content' => 'foo bar'));
    $this->assertEquals('foo bar', $R->getContent());
  }
  
  function testRequestSettingPathOnCreation() {
    $R = new Request(array('path' => '/foo'));
    $this->assertEquals('/foo', $R->getPath());
  }
  
  function testRequestSettingMethodOnCreation() {
    $R = new Request(array('method' => 'PUT'));
    $this->assertEquals('PUT', $R->getMethod());
  }
  
  function testSettingMultiplePropertiesOnCreation() {
    $R = new Request(array(
      'method' => 'POST',
      'content' => 'churva',
      'headers' => array('foo' => 'bar')
    ));
    $this->assertEquals('POST', $R->getMethod());
    $this->assertEquals('churva', $R->getContent());
    $this->assertEquals('bar', $R->getHeader('foo'));
  }
  
  function testSettingParamsCreation() {
    $R = new Request(array(
      'params' => array('foo' => 'bar')
    ));
    $this->assertEquals(array('foo' => 'bar'), $R->getParams());
  }
  
  function testGettingASingleParameter() {
    $R = new Request(array(
      'params' => array('foo' => 'bar')
    ));
    $this->assertEquals('bar', $R->getParam('foo'));
  }
  
  function testGettingASingleParameterDefaultsToNullForUndefinedValues() {
    $R = new Request;
    $this->assertSame(Null, $R->getParam('foo'));
  }
  
  function testExportRawHttpRequestString() {
    $headers = array('Accept' => 'text/html', 'Connection' => 'Close' );
    $R = new Request(array(
      'path'    => '/a/path/to/a/resource.html',
      'headers' => $headers
    ));
    $str = $R->export();
    $this->assertStringStartsWith(
      "GET /a/path/to/a/resource.html HTTP/1.1\r\n", $str,
      'Did not find request line in generated Raw HTTP Request string.'
    );
    $headers['Accept'] = 'text\/html';
    $this->_testHeaders($headers, $str);
    $this->assertStringEndsWith(
      "\r\n\r\n", $str,
      'Raw HTTP Request string should end in "\r\n\r\n".'
    );
  }
  
  private function _testHeaders($headers, $str) {
    foreach ($headers as $key => $value) {
      $pattern = "/\r\n" . 
        str_replace(
          array('.', '-'), array('\.', '\-'),
          String::dashCamelCase($key)
        ) . 
        ": $value\r\n/";
      $this->assertRegExp(
        $pattern, $str,
        "Did not find the $key header that was set."
      );
    }
  }
  
  function testExportWithPostValues() {
    $R = new Request(array(
      'method'  => 'POST',
      'path'    => '/post/processor',
      'content' => array(
        'foo' => 'bar', 'goo[]' => 'jazz', 'good' => 'bad='
      )
    ));
    $expected = 'foo=bar&' . urlencode('goo[]') . '=jazz&good=bad' .
      urlencode('=');
    $headers = array(
      'Content-Type' => 'application\/x\-www\-form\-urlencoded',
      'Content-Length' => strlen($expected)
    );
    $str = $R->export();
    $this->assertStringStartsWith(
      "POST /post/processor HTTP/1.1\r\n", $str,
      'Incorrect request line in generated Raw HTTP Request string.'
    );
    $this->_testHeaders($headers, $str);
    $this->assertStringEndsWith(
      "\r\n\r\n$expected", $str,
      'Raw HTTP Request string should end in "\r\n\r\n' . $expected . '".'
    );
  }
  
  function testExportGetShouldHaveNoContent() {
    $R = new Request(array(
      'path'    => '/a/get/path',
      'content' => array('foo' => 'bar')
    ));
    $not_expected = 'foo=bar';
    
    $str = $R->export();
    $this->assertStringStartsWith(
      "GET /a/get/path HTTP/1.1\r\n", $str
    );
    $this->assertNotContains(
      "\r\nContent-Type: application/x-www-form-urlencoded\r\n", $str
    );
    $this->assertNotContains(
      "\r\nContent-Length: " . strlen($not_expected) . "\r\n", $str
    );
    $this->assertStringEndsWith("\r\n\r\n", $str);
  }
  
  function testExportRequestWithParamsUrlEncodesParamValues() {
    $R = new Request(array(
      'path'    => '/handler',
      'params' => array(
        'foo' => 'bar', 'goo[]' => 'jazz', 'good' => 'bad='
      )
    ));
    $expected = 'foo=bar&' . urlencode('goo[]') . '=jazz&good=bad' .
      urlencode('=');
    $str = $R->export();
    $this->assertStringStartsWith(
      "GET /handler?$expected HTTP/1.1\r\n", $str, $str
    );
  }
  
}
