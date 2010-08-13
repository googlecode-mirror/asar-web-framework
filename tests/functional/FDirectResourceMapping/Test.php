<?php
require_once realpath(dirname(__FILE__) . '/../../config.php');
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(realpath(__FILE__)));

class FDirectResourceMapping_Test extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->client = new Asar_Client;
    $f = new Asar_ApplicationFactory;
    $this->app = $f->getApplication('Example1');
  }
  
  
  function testGetIndexShouldReturnAResponseObject() {
    $response = $this->client->GET($this->app, array('path' => '/'));
    $this->assertTrue(
       $response instanceof Asar_Response,
      'The returned object from GET request is not an ' .
      'Asar_Response object but '. get_class($response) . '.'
    );
  }
  
  function testGetIndexShouldReturnHelloWorldString() {
    $this->assertEquals(
      'Hello World!',
      $this->client->GET($this->app, array('path' => '/'))->getContent(), 
      'The content of Response did not match expectation.'
    );
  }
  
  function testGetIndexShouldReturnHttpStatus200WhenOk() {
    $this->assertEquals(
      200, $this->client->GET($this->app, array('path' => '/'))->getStatus(),
      'The Http Status code is not 200 for Okay responses.'
    );
  }
  
  function testGetIndexShouldReturnHtmlContentType() {
    $this->assertEquals(
      0, strpos(
        $this->client->GET(
          $this->app, array('path' => '/')
        )->getHeader('Content-Type'),
        'text/html'
      ),
      'The Content-Type header is not "text/html".'
    );
  }
  
  function testGetWhatShouldReturnResponseFromWhatResource() {
    $this->assertEquals(
      "What's your name?",
      $this->client->GET($this->app, array('path' => '/what'))->getContent(), 
      'The content of Response did not match expectation.'
    );
  }
  
  function testGetAnUnknownResourceShouldReturnResposeWith404Status() {
    //TODO: Do more status code testing in a separate functional test
    $this->assertEquals(
      404, $this->client->GET(
        $this->app, array('path' => '/non-existent-resource')
      )->getStatus(),
      'The status of the Response should be 404 for non-existent resource'
    );
  }
  
  function testPostRequestAtWhatSends() {
    $this->assertEquals(
      'Hello Foo!',
      $this->client->POST(
        $this->app, array(
          'path' => '/what', 'content' => array('name' => 'Foo')
        )
      )->getContent(),
      'The application did not properly process the POST request.'
    );
  }
  
 }
