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
  
  function testFilteringRedirectResponseToProperlyFormattingTheLocationHeaderValue() {
    $response = new Asar_Response(array('status' => 302, 'headers' => array('Location' => '/foo/bar')));
    $this->assertEquals(
      'http://example.domain.com/foo/bar',
      $this->filter->filterResponse($response)->getHeader('Location')
    );
  }
  
}