<?php
require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';
require_once 'Asar/Request/Handler.php';



class Test_Handler_Filters {
    public static $random_key;
    public static $random_value;
    
    static function setupRandomValues() {
        $RSG = Asar_Utility_RandomStringGenerator::instance();
        self::$random_key = $RSG->getAlphaNumeric(8);
        self::$random_value = $RSG->getAlphaNumeric(40);
    }
    
    static function requestFilter(Asar_Request $request) {
        $request->setParams(array('param_key' => 'param_value'));
    }
    
    static function requestFilterRandom(Asar_Request $request) {
        self::setupRandomValues();
        $request->setParams(array(self::$random_key => self::$random_value));
    }
    
    static function requestFilterChangeRequestType(Asar_Request $request) {
        $request->setType('php');
    }
    
    static function responseFilter(Asar_Response $response) {
        $response->setStatus(202);
    }
    
    static function responseFilterChangeParam(Asar_Response $response) {
        $response->setParams(array('rparam_key' => 'rparam_value'));
    }
}


class Test_Handler extends Asar_Request_Handler {
    
    protected $request_filters = array(
        array('Test_Handler_Filters', 'requestFilter')
    );
    
    protected $response_filters = array(
        array('Test_Handler_Filters', 'responseFilter')
    );
    
    // This method is created for the purpose of this test only
    function setRequestFilters($array) {
        $this->request_filters = $array;
    }
    
    // This method is created for the purpose of this test only
    function setResponseFilters($array) {
        $this->response_filters = $array;
    }
    
    function processRequest(Asar_Request $request, array $arguments = NULL) {
        $response = new Asar_Response;
        $response->setContent('The quick brown jump foxes over the dog lazy');
        return $response;
    }
}

class Asar_Request_HandlerTest extends PHPUnit_Framework_TestCase {
	
	
	protected function setUp() {
		$this->handler = new Test_Handler;
		$this->req  = new Asar_Request;
		$this->RSG = Asar_Utility_RandomStringGenerator::instance();
	}
 
	
	function testHandlerMustReturnAResponse() {
	    $response = $this->req->sendTo($this->handler);
        $this->assertEquals('Asar_Response', get_class($response), 'The handleRequest method should return a Response object.');
	}
	
	function testHandlerMustCallProcessRequest() {
        $this->handler = $this->getMock('Asar_Request_Handler', array('processRequest'));
        $this->handler->expects($this->once())
                   ->method('processRequest')
                   ->with($this->req);
        $this->req->sendTo($this->handler);
	}
	
	function testHandlerMustProcessRequestAndCreateAppropriateResponse() {
	    $response = $this->req->sendTo($this->handler);
	    $this->assertEquals('The quick brown jump foxes over the dog lazy', $response->getContent(),
	        'The request handler did not process the response'
	    );
	}
	
	function testHandlerMustCallProcessRequestAndPassSameArguments() {
	    $this->handler = $this->getMock('Asar_Request_Handler', array('processRequest'));
	    $arguments = array('churva' => 'echos');
        $this->handler->expects($this->once())
                   ->method('processRequest')
                   ->with($this->req, $arguments);
        $this->req->sendTo($this->handler, $arguments);
	}
	
	function testRequestFilter() {
        $this->req->sendTo($this->handler);
        $params = $this->req->getParams();
        $this->assertEquals('param_value', $params['param_key'], 'The test param value returned is unexpected. Filtering did not work');
	}
	
	function testRequestFilterRandom() {
	    $this->handler->setRequestFilters(array(
	        array('Test_Handler_Filters', 'requestFilterRandom')
	    ));
        $this->req->sendTo($this->handler);
        $params = $this->req->getParams();
        $this->assertTrue(array_key_exists(Test_Handler_Filters::$random_key, $params), 'The key could not be found');
        $this->assertEquals(Test_Handler_Filters::$random_value, 
            $params[Test_Handler_Filters::$random_key], 
            'The test random param value returned is unexpected. Filtering did not work');
	}
	
	function testManyRequestFilters() {
	    $this->handler->setRequestFilters(array(
	        array('Test_Handler_Filters', 'requestFilter'),
    	    array('Test_Handler_Filters', 'requestFilterRandom'),
	        array('Test_Handler_Filters', 'requestFilterChangeRequestType')
	    ));
        $this->req->sendTo($this->handler);
        $params = $this->req->getParams();
        $this->assertEquals(
            Test_Handler_Filters::$random_value, 
            $params[Test_Handler_Filters::$random_key], 
            'The test random param value returned is unexpected. Filtering did not work'
        );
        $this->assertEquals(
            'param_value',
            $params['param_key'],
            'The test param value returned is unexpected. Filtering did not work.'
        );
        $this->assertEquals(
            'php', $this->req->getType(),
            'The test type value returned is unexpected. Filtering did not work.'
        );
	}
	
	function testResponseFilter() {
	    $test_str = 'The quick brown jump foxes over the dog lazy';
	    $response = $this->req->sendTo($this->handler);
	    $this->assertEquals(
	        202, $response->getStatus(),
	        'The response status is unexpected. Filtering response did not work.'
	    );
	}
	
	function testManyResponseFilters() {
	    $this->handler->setResponseFilters(array(
            array('Test_Handler_Filters', 'responseFilterChangeParam'),
	        array('Test_Handler_Filters', 'responseFilter')
	    ));
	    $test_str = 'The quick brown jump foxes over the dog lazy';
	    $response = $this->req->sendTo($this->handler);
	    $this->assertEquals(
	        202, $response->getStatus(),
	        'The response status is unexpected. Filtering response did not work.'
	    );
	    $params = $response->getParams();
	    $this->assertEquals(
	        'rparam_value', $params['rparam_key'],
	        'The response param value was not found. Filtering to this point did not succeed.'
	    );
	}
	
	function testHandlerDefinitionRequiresProcessRequest() {
	    $reflection = new ReflectionClass('Asar_Request_Handler');
        $this->assertTrue($reflection->hasMethod('processRequest'), 'The method processRequest must be found in the class definition of Asar_Request_Handler');
        $processRequest = $reflection->getMethod('processRequest');
        $this->assertTrue($processRequest->isProtected(), 'The method processRequest must be set as protected.');
        $this->assertTrue($processRequest->isAbstract(), 'The method processRequest must be abstract so child classes must be required to define them');
	}
	
	function testHandlerDefinitionMustFinalizeHandleRequestMethod() {
	    $reflection = new ReflectionClass('Asar_Request_Handler');
	    $handleRequest = $reflection->getMethod('handleRequest');
	    $this->assertTrue($handleRequest->isFinal(), 'The method handleRequest must be final in Asar_Request_Handler class definitioin');
	}
	
	function testGettingFilters()
	{
	    $req_filter = array('Collection_of_Filters', 'requestFilterRandom');
	    $res_filter = array('Collection_of_Filters', 'responseFilterRandom');
	    $this->handler->setRequestFilters(array(
	        $req_filter
	    ));
	    $this->handler->setResponseFilters(array(
	        $res_filter
	    ));
        $this->assertEquals(array($req_filter), $this->handler->getRequestFilters(), 'Unable to obtain request filters');
        $this->assertEquals(array($res_filter), $this->handler->getResponseFilters(), 'Unable to obtain response filters');
	}
	
}
