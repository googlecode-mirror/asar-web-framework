<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Test_Controller_Index extends Asar_Controller {
	
	protected $map = array(
		'path' => 'Another',
		'next' => 'Next'
	);
	
	function GET() {
		return 'hello there';
	}
	
	function POST() {
		return 'I am alright';
	}
	
	function PUT() {
		return 'Put it on';
	}
	
	function DELETE() {
		return 'Deleted!';
	}
}

class Test_Controller_Another extends Asar_Controller {
	function GET() {
		return 'hello world';
	}
	
	function POST() {
		return $this->request->getParams();
	}
}

class Test_Controller_Next extends Asar_Controller {
	protected $map = array(
		'proceed' => 'Proceed',
		'follow'  => 'Follow'
	);
	
	function GET() {
		return 'context path = "'.$this->context->getPath().'"';
	}
	
	function POST() {
		return get_class($this->context);
	}
}

class Test_Controller_Proceed extends Asar_Controller {
	function GET() {
		return 'context path = "'.$this->context->getPath().'"';
	}
	
	function POST() {
		return $this->getPath();
	}
	
	function PUT() {
		return $this->getDepth();
	}
}

class Test_Controller_Follow extends Asar_Controller {

	function GET() {
		$this->view['var'] = 'Followed GET';
	}
}

class Test_Controller_Forwarding extends Asar_Controller {
	protected $forward = 'Forwarded';
	
	function GET() {
		return 'AAAA';
	}
	
	function POST() {
		$this->view['output'] = 'This is the way';
	}
}

class Test_Controller_Forwarded extends Asar_Controller {
	
	function GET() {
		return 'BBBB';
	}
}

class Test_Controller_With_No_Methods extends Asar_Controller {}



class Asar_ControllerTest extends Asar_Test_Helper {
  
	protected function setUp() {
		$this->C = new Test_Controller_Index;
		$this->R = new Asar_Request;
		$this->R->setPath('/');
	}

	function testPassingARequestWithMethodGetInvokesMethod() {
		$this->R->setMethod(Asar_Request::GET);
		$this->assertEquals('hello there',
							$this->R->sendTo($this->C)->__toString(),
							'Controller did not handle request');
	}
	
	
	function testPassingARequestWithMethodGETShouldReturnStatusSuccessWhenRequestIsOkay() {
		$this->R->setMethod(Asar_Request::GET);
		$this->assertEquals(200, $this->R->sendTo($this->C)->getStatus(), 'Controller did not return proper status code');
	}
	
	
	function testPassingARequestWithMethodPostInvokesMappedMethod() {
		$this->R->setMethod(Asar_Request::POST);
		$this->assertEquals('I am alright', $this->R->sendTo($this->C)->__toString(), 'Controller did not handle request');
	}
	
	function testPassingARequestWithMethodPutInvokesMappedMethod() {
		$this->R->setMethod(Asar_Request::PUT);
		$this->assertEquals('Put it on', $this->R->sendTo($this->C)->__toString(), 'Controller did not handle request');
	}
	
	function testPassingARequestWithMethodDELETEInvokesMappedMethod() {
		$this->R->setMethod(Asar_Request::DELETE);
		$this->assertEquals('Deleted!', $this->R->sendTo($this->C)->__toString(), 'Controller did not handle request');
	}
	
	function testRequestingAResourceWithHeadMethodShouldNotReturnAnyContent() {
		$this->R->setMethod(Asar_Request::HEAD);
		$this->assertEquals('', $this->R->sendTo($this->C)->__toString());
	}
	
	function testRequestingAMappedResourceButUndefinedMethodMustReturnA405StatusResponse() {
		$this->R->setMethod(Asar_Request::PUT);
		$this->assertEquals(405, $this->R->sendTo(new Test_Controller_Another())->getStatus());
	}
	
	function testRequestingUndefinedGETMethod() {
		$this->R->setMethod(Asar_Request::GET);
		$this->assertEquals(405, $this->R->sendTo(new Test_Controller_With_No_Methods())->getStatus());
	}
	
	function testRequestingUndefinedPOSTMethod() {
		$this->R->setMethod(Asar_Request::POST);
		$this->assertEquals(405, $this->R->sendTo(new Test_Controller_With_No_Methods())->getStatus());
	}
	
	function testRequestingUndefinedDELETEMethod() {
		$this->R->setMethod(Asar_Request::DELETE);
		$this->assertEquals(405, $this->R->sendTo(new Test_Controller_With_No_Methods())->getStatus());
	}
	
	function testRequestingUndefinedHEADMethod() {
		$this->R->setMethod(Asar_Request::HEAD);
		$this->assertEquals(405, $this->R->sendTo(new Test_Controller_With_No_Methods())->getStatus());
	}
	
	function testUsingHeadAsRequestMethodMustNotReturnAnyContent() {
		$this->R->setMethod(Asar_Request::HEAD);
		$response = $this->R->sendTo($this->C);
		$this->assertEquals(200, $response->getStatus(), 'Get method was not called');
		$this->assertEquals('', $response->__toString(), 'Returned content for HEAD!');
	}
	
	function testUsingGETAsRequestMethodMustReturnAnyContent() {
		$this->R->setMethod(Asar_Request::GET);
		$this->assertNotEquals('', $this->R->sendTo($this->C)->__toString(), 'Returned content for HEAD!');
	}
	
	function testRequestingWithSubPaths() {
		$this->R->setMethod(Asar_Request::GET);
		$this->R->setPath('/path/');
		$this->assertEquals('hello world', $this->R->sendTo($this->C)->__toString(), 'Controller did not handle request');
	}
	
	function testGettingContext() {
		$this->R->setMethod(Asar_Request::POST);
		$this->R->setPath('/next/');
		$this->assertEquals('Test_Controller_Index', $this->R->sendTo($this->C)->__toString(), 'Unable to obtain context path');
	}
	
	function testGettingPathDepthWhenIndex() {
		$this->R->setMethod(Asar_Request::PUT);
		$this->R->setPath('/');
		$this->assertEquals('0', $this->R->sendTo(new Test_Controller_Proceed)->__toString(), 'Controller was unable to obtain Path Depth');
	}
	
	function testGettingPathDepthWhen1LevelDeep() {
		$this->R->setMethod(Asar_Request::PUT);
		$this->R->setPath('/proceed');
		$this->assertEquals('1', $this->R->sendTo(new Test_Controller_Next)->__toString(), 'Controller was unable to obtain Path Depth');
	}
	
	function testMakeSureControllerActionHasAccessToRequestObject() {
		$this->R->setMethod(Asar_Request::POST);
		$testcontent = array(
		 'peach' => 'presses',
		 'stupid' => 'dog'
		);
		$this->R->setParams($testcontent);
		$response = $this->R->sendTo(new Test_Controller_Another);
		$this->assertEquals($testcontent, $response->getContent(), 'Unexpected Result');
	}
	
	function testGettingPathDepthWhen2LevelsDeep() {
		$this->R->setMethod(Asar_Request::PUT);
		$this->R->setPath('/next/proceed');
		$this->assertEquals('2', $this->R->sendTo($this->C)->__toString(), 'Controller was unable to obtain Path Depth');
	}
	
	function testGettingPath() {
		$this->R->setMethod(Asar_Request::POST);
		$this->assertEquals('/', $this->R->sendTo(new Test_Controller_Proceed)->__toString(), 'Controller was unable to obtain Path');
	}
	
	
	function testGettingContextPath() {
		$this->R->setMethod(Asar_Request::GET);
		$this->R->setPath('/next/');
		$this->assertEquals('context path = "/"', $this->R->sendTo($this->C)->__toString(), 'Unable to obtain context path');
	}
	
	function testGettingAnotherContextPath() {
		$this->R->setMethod(Asar_Request::GET);
		$this->R->setPath('/next/proceed/');
		$this->assertEquals('context path = "/next"', $this->R->sendTo($this->C)->__toString(), 'Unable to obtain context path');
	}
	
	function testRequestingAnUnmappedResourceResultsIn404StatusResponse() {
		$this->R->setMethod(Asar_Request::GET);
		$this->R->setPath('/non-existent-path/');
		$this->assertEquals(404, $this->R->sendTo($this->C)->getStatus());
	}
	
	function testRequestingAnUnmappedResourceButControllerHasForwardDefinedForwardsThatRequest() {
		$this->R->setMethod(Asar_Request::GET);
		$this->R->setPath('/we_are_the_champion/');
		$controller = new Test_Controller_Forwarding;
		$this->assertEquals('BBBB', $this->R->sendTo($controller)->__toString(), 'Unexpected response');
	}
	
	function testResourcesWillAttemptToInvokeCorrespondingTemplateWhenViewIsDefined() {
		$this->R->setMethod(Asar_Request::POST);
		$controller = new Test_Controller_Forwarding;
		$old_include_path = get_include_path();
		set_include_path($old_include_path . PATH_SEPARATOR . self::getTempDir());
		$template = self::newFile('Test/View/Forwarding/POST.html.php', '<h1><?=$output?></h1>');
		$this->assertEquals('<h1>This is the way</h1>', $this->R->sendTo($controller)->__toString(), 'The template file was probably not invoked');
		set_include_path($old_include_path); // reset path
	}
	
	function testAttemptToInvokeCorrespondingTemplateGet() {
		$this->R->setMethod(Asar_Request::GET);
		$this->R->setPath('/next/follow/');
		$old_include_path = get_include_path();
		set_include_path($old_include_path . PATH_SEPARATOR . self::getTempDir());
		$template = self::newFile('Test/View/Follow/GET.html.php', '<strong><?=$var?></strong>Yadayada');
		$this->assertEquals('<strong>Followed GET</strong>Yadayada', $this->R->sendTo($this->C)->__toString(), 'The template file was probably not invoked');
		set_include_path($old_include_path); // reset path
	}
	
	/**
	 * Test Getting the Layout template
	 *
	 * @return void
	 **/
	public function testGetTheLayoutTemplateWhenItIsAvailable()
	{
	    $this->R->setMethod(Asar_Request::GET);
		$this->R->setPath('/next/follow/');
		$old_include_path = get_include_path();
		set_include_path($old_include_path . PATH_SEPARATOR . self::getTempDir());
		$template = self::newFile('Test/View/Follow/GET.html.php', '<h1><?=$var?></h1>');
		$layout   = self::newFile('Test/View/Layout.html.php', '<html><head><title>Test Layout</title></head><body><?=$contents?></body></html>');
		$this->assertEquals('<html><head><title>Test Layout</title></head><body><h1>Followed GET</h1></body></html>',
		                    $this->R->sendTo($this->C)->__toString(),
		                    'The layout file was not invoked');
		set_include_path($old_include_path); // reset path
	}
	
	/**
	 * Test for getting the appropriate representation (view) for the request 
	 *
	 * @return void
	 **/
	public function testGettingJsonTemplateForTheRequest()
	{
	    
	    $old_include_path = get_include_path();
		set_include_path($old_include_path . PATH_SEPARATOR . self::getTempDir());
		// Json Representation
		$json_template = self::newFile('Test/View/Follow/GET.json.php', '{<?=$var?>}');
		$html_template = self::newFile('Test/View/Follow/GET.html.php', '<h1><?=$var?></h1>');
		$this->R->setPath('/next/follow/');
		$this->R->setType('json');
		$this->assertEquals('{Followed GET}',
		                    $this->R->sendTo($this->C)->__toString(),
		                    'The wrong template was used. Must be json.');
	}

    /**
	 * Layout should not be included for requests for representations
	 * other than html.
	 *
	 * @return void
	 **/
	public function testLayoutShouldNotBeIncludedWhenRequestDoesNotAskForHtmlRepresentation()
	{
	    $this->R->setMethod(Asar_Request::GET);
		$this->R->setPath('/next/follow/');
        $this->R->setType('json');
		$old_include_path = get_include_path();
		set_include_path($old_include_path . PATH_SEPARATOR . self::getTempDir());
		$template = self::newFile('Test/View/Follow/GET.json.php', '{<?=$var?>}');
		$layout   = self::newFile('Test/View/Layout.html.php', '<html><head><title>Test Layout</title></head><body><?=$contents?></body></html>');
		$this->assertNotContains('<html><head><title>Test Layout</title></head><body>',
		                    $this->R->sendTo($this->C)->__toString(),
		                    'The layout file was not invoked');
		set_include_path($old_include_path); // reset path
	}
	/*
	
  
  function testExposeRequestParamsAsParamsPropertyInController() {
    $req = new Asar_Request();
    $testcontent = array(
     'bull' => 'dung',
     'cat' => 'fight'
    );
    $this->R->setParams($testcontent);
    $response = $this->R->sendTo($this->C, array('action' => 'action1'));
    $this->assertEquals($testcontent, $response->getContent(), 'Unexpected Result');
  }
  
  function testForwardingToInternalAction() {
    $req = new Asar_Request();
    $testcontent = array(
     'bull' => 'cram',
     'cat' => 'fits'
    );
    $this->R->setParams($testcontent);
    $response = $this->R->sendTo($this->C, array('action' => 'action2'));
    $this->assertEquals($testcontent, $response->getContent(), 'Unexpected Result');
  }*/
  
}
