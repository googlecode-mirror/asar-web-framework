<?php
require_once realpath(dirname(__FILE__) . '/../../../../config.php');

use \Asar\MessageFilter\Standard as StandardMessageFilter;
use \Asar\Config;
use \Asar\Response;
use \Asar\Request;

class Asar_MessageFilter_StandardTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->config = new Config(array(
      'site_protocol' => 'http',
      'site_domain'   => 'example.domain.com'
    ));
    $this->filter = new StandardMessageFilter($this->config);
  }
  
  function testRedirectResponseToProperlyFormattingTheLocationHeaderValue() {
    $response = new Response(array(
      'headers' => array('Location' => '/foo/bar')
    ));
    $this->assertEquals(
      'http://example.domain.com/foo/bar',
      $this->filter->filterResponse($response)->getHeader('Location')
    );
  }
  
  function testSkipFormattingTheLocationHeaderValueWhenItIsCorrect() {
    $response = new Response(array('headers' => array(
      'Location' => 'http://somewhere.com/foo/bar'
    )));
    $this->assertEquals(
      'http://somewhere.com/foo/bar',
      $this->filter->filterResponse($response)->getHeader('Location')
    );
  }
  
  function testFilterRequestReturnsRequest() {
    $request = new Request;
    $this->assertInstanceOf('Asar\Request', $this->filter->filterRequest($request));
  }
  
  function testFilteringInternalHeadersFromRequest() {
    $headers = array(
      'Asar-InternalBoo' => 'foo',
      'Asar-Internal' => 2,
      'Asar-Internal-Foo' => 'bar'
    );
    $request = new Request(array('headers' => $headers));
    foreach (array_keys($headers) as $key) {
      $this->assertEquals(
        null, $this->filter->filterRequest($request)->getHeader($key)
      );
    }
  }
  
  function testRemoveInternalHeadersFromResponse() {
    $headers = array(
      'Asar-InternalBoo' => 'foo',
      'Asar-Internal' => 2,
      'Asar-Internal-Foo' => 'bar'
    );
    $response = new Response(array('headers' => $headers));
    foreach (array_keys($headers) as $key) {
      $this->assertEquals(
        null, $this->filter->filterResponse($response)->getHeader($key)
      );
    }
  }
  
}
