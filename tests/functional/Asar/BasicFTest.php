<?php
/**
 * Basic Test, A Functional Test for testing basic setup
 *
 * This Test suite uses the App dummy Application which can be found
 * at tests/functional/App.
 *
 * @package functional_test_basic
 * @version $Id:$
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.php';

/**
 * Asar_BasicFTest, basic functional tests
 *
 * Tests basic use cases such as going to web root
 *
 * @package functional_test_basic
 **/
class Asar_BasicFTest extends Asar_Test_Helper
{
    
    /**
     * Test client
     *
     * @var Asar_Client
     **/
    protected $client;
    
    /**
     * Test Application
     *
     * @var App_Application
     **/
    protected $app;
    
    /**
     * Request object to using during tests
     *
     * @var Asar_Request
     **/
    protected $request;
    
    /**
     * Sets up $client and $server private variables
     *
     * @return void
     **/
    public function setUp()
    {
        $this->client  = new Asar_Client;
        $this->app     = new App_Application;
        // This should setup the default request properties:
        // Method: GET
        // Path: /
        $this->request = new Asar_Request;
    }
    
    /**
     * Test going to Index controller using GET Method must return an
     * Asar_Response object.
     *
     * @return void
     **/
    public function testGetIndexShouldReturnResponseObject()
    {
        $response = $this->request->sendTo($this->app);
        $this->assertTrue($this->request->sendTo($this->app) instanceof Asar_Response, 'The request did not return an Asar_Response object');
    }
    
    /**
     * Test going to Index controller using GET Method must return a
     * 200 Response Ok as its HTTP Status
     *
     * @return void
     **/
    public function testGetIndexShouldReturnA200ResponseCode()
    {
        $response = $this->request->sendTo($this->app);
        $this->assertEquals(200, $response->getStatus(), 'The response status code was not 200');
    }
    
    /**
     * Asar_Index_Controller should contain the text
     * '<h1>Hello world!</h1>' implying that the application has
     * properly called the appropriate template which in this case
     * is 'App/View/Index/GET.html.php'
     *
     * @return void
     **/
    public function testGetIndexShouldReturnExpectedString()
    {
        $response = $this->request->sendTo($this->app);
        $this->assertContains('<h1>Hello world!</h1>', $response->getContent(), 'The response returned an unexpected content');
    }
    
    /**
     * The layout template must be found after a
     * successful request to App_Index_Controller
     *
     * @return void
     **/
    public function testLayoutTemplateMustBeParsedWithSuccessfulGetRequestToIndexController()
    {
        $response = $this->request->sendTo($this->app);
        $this->assertContains('<title>Test Application</title>', $response->getContent(), 'The response expected string from Layout');
    }
} // END class Asar_BasicFTest
