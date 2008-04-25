<?php
/**
 * Tests for Debugging output
 *
 * @package functional_test_debugging
 * @version $Id:$
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.php';

/**
 * Asar_BasicFTest, basic functional tests
 *
 * Tests basic use cases such as going to web root
 *
 * @package functional_test_debugging
 **/
class Asar_DebuggingFTest extends Asar_Test_Helper
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
        Asar::setMode(Asar::MODE_DEVELOPMENT);
        $this->client  = new Asar_Client;
        $this->app     = new App_Application;
        // This should setup the default request properties:
        // Method: GET
        // Path: /
        $this->request = new Asar_Request;
    }
    
    /**
     * Reset
     *
     * @return void
     **/
    public function tearDown()
    {
        Asar::setMode(Asar::MODE_PRODUCTION);
    }
    
    /**
     * Execution time must be exposed
     *
     * @return void
     **/
    public function testGettingExecutionTime()
    {
        $response = $this->request->sendTo($this->app);
        $this->assertContains('Execution Time', $response->getContent(), 'The response does not contain expected debugging information');
    }
    
} // END class Asar_BasicFTest
