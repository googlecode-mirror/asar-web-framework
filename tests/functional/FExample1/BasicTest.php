<?php
require_once realpath(dirname(__FILE__) . '/../../config.php');
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(realpath(__FILE__)));

class FExample1_BasicTest extends PHPUnit_Framework_TestCase
{
    
    public function setUp()
    {
        $this->client = new Asar_Client;
        $this->app = new Example1_Application;
        $this->client->setServer($this->app);
    }
    
    
    public function testGetIndexShouldReturnAResponseObject()
    {
        $response = $this->client->GET('/');
        $this->assertTrue(
             $response instanceof Asar_Response,
            'The returned object from GET request is not an ' .
            'Asar_Response object but '. get_class($response) . '.'
        );
    }
    
    public function testGetIndexShouldReturnHelloWorldString()
    {
        $this->assertEquals(
            'Hello World!',
            $this->client->GET('/')->getContent(), 
            'The content of Response did not match expectation.'
        );
    }
    
    public function testGetIndexShouldReturnHttpStatus200WhenOk()
    {
        $this->assertEquals(
            200, $this->client->GET('/')->getStatus(),
            'The Http Status code is not 200 for Okay responses.'
        );
    }
    
    public function testGetIndexShouldReturnHtmlContentType()
    {
        $this->assertEquals(
            0, strpos($this->client->GET('/')->getHeader('Content-Type'), 'text/html'),
            'The Content-Type header is not "text/html".'
        );
    }
    
    public function testGetWhatShouldReturnResponseFromWhatResource()
    {
        $this->assertEquals(
            "What's your name?",
            $this->client->GET('/what')->getContent(), 
            'The content of Response did not match expectation.'
        );
    }
    
    public function testGetAnUnknownResourceShouldReturnResposeWith404Status()
    {
        //TODO: Do more status code testing in a separate functional test
        $this->assertEquals(
            404, $this->client->GET('/non-existent-resource')->getStatus(),
            'The status of the Response should be 404 for non-existent resource'
        );
    }
    
    public function testPostRequestAtWhatSends()
    {
        $this->assertEquals(
            'Hello Foo!',
            $this->client->POST('/what', array('name' => 'Foo'))->getContent(),
            'The application did not properly process the POST request.'
        );
    }
    
    
}
