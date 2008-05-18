<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar/Application.php';


class Test2_Application extends Asar_Application {}
class Test2_Controller_Index extends Asar_Controller{
	function GET() {
		return 'Hello World';
	}
}

class Asar_ApplicationTest_Application extends Asar_Application {}

class Asar_ApplicationTest extends PHPUnit_Framework_TestCase {
  
    protected function setUp() {
        Asar::setMode(Asar::MODE_PRODUCTION);
        $this->app = new Test2_Application();
    }
    
    protected function tearDown()
    {
        Asar::setMode(Asar::MODE_PRODUCTION);
    }
  
  
  
	public function testInvokingMustFirstRunIndexController()
	{
		$req = new Asar_Request();
		$req->setPath('/');
		$response = $req->sendTo($this->app);
		$this->assertEquals('Hello World', $response->__toString(), 'Application did not invoke index controller' );
	}
  
  function testLoadingController() {
    $test     = 'cheap';
    $expected = 'Test2_Controller_Cheap';
    try {
      $this->app->loadController($test);
      $this->fail('Must not reach execution');
    } catch (Exception $e) {
      $this->assertEquals('Asar_Exception', get_class($e), 'Wrong exception thrown');
      $this->assertEquals("Class definition file for the class $expected does not exist.",
                          $e->getMessage(),
                          'Framework did not attempt to load controller');
    }
  }
  
  
  function testLoadingModel() {
    $test     = 'cheap';
    $expected = 'Test2_Model_Cheap';
    try {
      $this->app->loadModel($test);
      $this->assertTrue(False, 'Must not reach execution');
    } catch (Exception $e) {
      $this->assertEquals('Asar_Exception', get_class($e), 'Wrong exception thrown');
      $this->assertEquals("Class definition file for the class $expected does not exist.",
                          $e->getMessage(),
                          'Framework did not attempt to load model');
    }
  }
  
  
  function testLoadingFilter() {
    $test     = 'cheap';
    $expected = 'Test2_Filter_Cheap';
    try {
      $this->app->loadFilter($test);
      $this->assertTrue(False, 'Must not reach execution');
    } catch (Exception $e) {
      $this->assertEquals('Asar_Exception', get_class($e), 'Wrong exception thrown');
      $this->assertEquals("Class definition file for the class $expected does not exist.",
                          $e->getMessage(),
                          'Framework did not attempt to load filter');
    }
  }
  
  
  function testLoadingHelper() {
    $test     = 'cheap';
    $expected = 'Test2_Helper_Cheap';
    try {
      $this->app->loadHelper($test);
      $this->assertTrue(False, 'Must not reach execution');
    } catch (Exception $e) {
      $this->assertEquals('Asar_Exception', get_class($e), 'Wrong exception thrown');
      $this->assertEquals("Class definition file for the class $expected does not exist.",
                          $e->getMessage(),
                          'Framework did not attempt to load helper');
    }
  }
  
  
  function testLoadingViewWithoutAction() {
    $test     = 'cheap';
    $expected = 'Test2/View/cheap.php';
    $this->assertEquals($expected, $this->app->loadView($test), 'Returned view file is wrong');
  }
  
  
  function testLoadingViewWithAction() {
    $test_controller     = 'cheap';
    $test_action         = 'shot';
    $expected            = 'Test2/View/cheap/shot.php';
    $this->assertEquals($expected, $this->app->loadView($test_controller, $test_action), 'Returned view file is wrong');
  }
  
  /*
  function testGetRouter() {
    $expected = 'Test2_Router';
    
  }*/
  
    function testProcessingRequest() {
        $req = new Asar_Request();
        $testarray = array(
                            'dirt' => 'cheap',
                            'suck' => 'lick',
                            'sup'  => 'Stupid Utility Padyaks',
                            'ban'  => 1
                          );
        $req->setContent( $testarray );
        $req->setMethod(Asar_Request::GET);
        $response = $req->sendTo($this->app);
        $this->assertTrue($response instanceof Asar_Response, 'Returned value is not an instance of Asar_Response');
        $this->assertEquals('Hello World', $response->__toString(), 'Unexpected value for content');
    }
    
    /**
     * Display execution times when in development mode
     *
     * @return void
     **/
    public function testAddingExecutionTimeWhenInDevelopmentMode()
    {
        Asar::setMode(Asar::MODE_DEVELOPMENT);
        $req = new Asar_Request();
		$req->setPath('/');
		$req->sendTo($this->app);
        $debug = Asar::getDebugMessages();
        $this->assertArrayHasKey('Execution Time', $debug, 'Did not find execution time in debug message');
        $this->assertRegExp('/[0-9]+\.[0-9]+[E]?[-]?[0-9]* seconds/', $debug['Execution Time'], 'The message is not the execution time in seconds');
    }
    
    function testApplicationMustInheritFromAsarRequestHandler() {
        $reflection = new ReflectionClass('Asar_Application');
        $this->assertTrue($reflection->isSubclassOf('Asar_Request_Handler'), 'Asar_Application must be a child class of Asar_Request_Handler');
    }
    
    
    function testApplicationShouldReturn404ResponseWhenThereIsNoRootControllerDefined()
    {
        $app = new Asar_ApplicationTest_Application;
        $req = new Asar_Request;
        $response = $req->sendTo($app);
        $this->assertTrue($response instanceof Asar_Response, 'The application did not return an Asar_Response object');
        $this->assertEquals(404, $response->getStatus(), 'The status should be 404 when there is no root controller defined');
    }
    
    function testApplicationShouldSetAsarFilterCommonResponseFilterWhenInDevelopmentMode()
    {
        Asar::setMode(Asar::MODE_DEVELOPMENT);
        $app = new Asar_ApplicationTest_Application;
        $req = new Asar_Request();
        $response = $req->sendTo($app);
        $test = $app->getResponseFilters();
        $this->assertFalse(empty($test), 'The filter response must not be empty at least');
        $this->assertContains(array('Asar_Filter_Common', 'filterResponse'), $test, 'The Asar_Filter_Common::filterResponse was not set as a filter');
    }
    
    function testApplicationShouldSetAsarFilterCommonRequestFilterContentNegotiation()
    {
        $app = new Asar_ApplicationTest_Application;
        $test = $app->getRequestFilters();
        $this->assertFalse(empty($test), 'The filter response must not be empty at least');
        $this->assertContains(array('Asar_Filter_Common', 'filterRequestTypeNegotiation'), $test, 'The Asar_Filter_Common::filterRequestTypeNegotiation was not set as a filter');
    }

	function testResponseStatusIs404SendProper404Message()
	{
		$req = new Asar_Request();
		$req->setPath('/non-existent-path');
		$response = $req->sendTo($this->app);
		$this->assertContains('Sorry, we were unable to find the resource you were looking for. Please check that you got the address or URL correctly. If that is the case, please email the administrator. Thank you and please forgive the inconvenience.',
			$response->__toString(), 'Application did not return a proper 404 message' );
	}
	
	function testResponseStatusIs405SendProper405Message()
	{
		$req = new Asar_Request();
		$req->setMethod('POST');
		$response = $req->sendTo($this->app);
		$this->assertContains("The HTTP Method 'POST' is not allowed for this resource.",
			$response->__toString(), 'Application did not return a proper 405 message' );
	}
}
