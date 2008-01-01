<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Test_Controller_Index extends Asar_Controller {
	
	protected $map = array(
		'path' => 'Another'
	);
	
	public function GET() {
		return 'hello there';
	}
	
	public function POST() {
		return 'I am alright';
	}
	
	public function PUT() {
		return 'Put it on';
	}
	
	public function DELETE() {
		return 'Deleted!';
	}
}

class Test_Controller_Another extends Asar_Controller {
	public function GET() {
		return 'hello world';
	}
	
	public function POST() {
		return $this->request->getParams();
	}
}



class Asar_ControllerTest extends PHPUnit_Framework_TestCase {
  
	protected function setUp() {
		$this->C = new Test_Controller_Index;
		$this->R = new Asar_Request;
		$this->R->setPath('/');
	}
	
	public function testPassingARequestWithMethodGetInvokesMappedMethod() {
		$this->R->setMethod(Asar_Request::GET);
		$this->assertEquals('hello there', $this->R->sendTo($this->C)->__toString(), 'Controller did not handle request');
	}
	
	
	public function testPassingARequestWithMethodPostInvokesMappedMethod() {
		$this->R->setMethod(Asar_Request::POST);
		$this->assertEquals('I am alright', $this->R->sendTo($this->C)->__toString(), 'Controller did not handle request');
	}
	
	public function testPassingARequestWithMethodPutInvokesMappedMethod() {
		$this->R->setMethod(Asar_Request::PUT);
		$this->assertEquals('Put it on', $this->R->sendTo($this->C)->__toString(), 'Controller did not handle request');
	}
	
	public function testPassingARequestWithMethodDeleteInvokesMappedMethod() {
		$this->R->setMethod(Asar_Request::DELETE);
		$this->assertEquals('Deleted!', $this->R->sendTo($this->C)->__toString(), 'Controller did not handle request');
	}
	
	public function testRequestingAMappedResourceButUndefinedMethodMustReturnA405StatusResponse() {
		$this->R->setMethod(Asar_Request::PUT);
		$this->assertEquals(405, $this->R->sendTo(new Test_Controller_Another())->getStatusCode());
	}
	
	public function testRequestingAResourceWithHeadMethodShouldReturnTheSameResponseExceptContentWhenGetMethodIsDefinedForThatResource() {
		$this->R->setMethod(Asar_Request::HEAD);
		$this->assertEquals(200, $this->R->sendTo($this->C)->getStatusCode());
	}
	
	public function testUsingHeadAsRequestMethodMustNotReturnAnyContent() {
		$this->R->setMethod(Asar_Request::HEAD);
		$this->assertEquals('', $this->R->sendTo($this->C)->__toString(), 'Returned content for HEAD!');
	}
	
	public function testRequestingWithSubPaths() {
		$this->R->setMethod(Asar_Request::GET);
		$this->R->setPath('/path/');
		$this->assertEquals('hello world', $this->R->sendTo($this->C)->__toString(), 'Controller did not handle request');
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
	
	public function testRequestingAnUnmappedResourceResultsIn404StatusResponse() {
		$this->R->setMethod(Asar_Request::GET);
		$this->R->setPath('/non-existent-path/');
		$this->assertEquals(404, $this->R->sendTo($this->C)->getStatusCode());
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

?>