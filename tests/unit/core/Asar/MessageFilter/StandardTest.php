<?php
require_once realpath(dirname(__FILE__) . '/../../../../config.php');

class Asar_MessageFilter_StandardTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->config = new Asar_Config(array(
      'site_protocol' => 'http',
      'site_domain'   => 'example.domain.com'
    ));
    $this->filter = new Asar_MessageFilter_Standard($this->config);
  }
  
  function testRedirectResponseToProperlyFormattingTheLocationHeaderValue() {
    $response = new Asar_Response(array(
      'headers' => array('Location' => '/foo/bar')
    ));
    $this->assertEquals(
      'http://example.domain.com/foo/bar',
      $this->filter->filterResponse($response)->getHeader('Location')
    );
  }
  
  function testSkipFormattingTheLocationHeaderValueWhenItIsCorrect() {
    $response = new Asar_Response(array('headers' => array(
      'Location' => 'http://somewhere.com/foo/bar'
    )));
    $this->assertEquals(
      'http://somewhere.com/foo/bar',
      $this->filter->filterResponse($response)->getHeader('Location')
    );
  }
  
  function testFilterRequestReturnsRequest() {
    $request = new Asar_Request;
    $this->assertInstanceOf('Asar_Request', $this->filter->filterRequest($request));
  }
  
  function testFilteringInternalHeadersFromRequest() {
    $headers = array(
      'Asar-InternalBoo' => 'foo',
      'Asar-Internal' => 2,
      'Asar-Internal-Foo' => 'bar'
    );
    $request = new Asar_Request(array('headers' => $headers));
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
    $response = new Asar_Response(array('headers' => $headers));
    foreach (array_keys($headers) as $key) {
      $this->assertEquals(
        null, $this->filter->filterResponse($response)->getHeader($key)
      );
    }
  }
  
}
