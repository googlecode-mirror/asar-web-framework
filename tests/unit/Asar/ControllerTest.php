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
}


class Asar_ControllerTest extends PHPUnit_Framework_TestCase {
  
	protected function setUp() {
		$this->C = new Test_Controller_Index();
	}
	
	public function testPassingARequestWithMethodGetInvokesMappedMethod() {
		$req = new Asar_Request();
		$req->setMethod(Asar_Request::GET);
		$this->assertEquals('hello there', $req->sendTo($this->C)->__toString(), 'Controller did not handle request');
	}
	
	
	public function testPassingARequestWithMethodPostInvokesMappedMethod() {
		$req = new Asar_Request();
		$req->setMethod(Asar_Request::POST);
		$this->assertEquals('I am alright', $req->sendTo($this->C)->__toString(), 'Controller did not handle request');
	}
	
	public function testPassingARequestWithMethodPutInvokesMappedMethod() {
		$req = new Asar_Request();
		$req->setMethod(Asar_Request::PUT);
		$this->assertEquals('Put it on', $req->sendTo($this->C)->__toString(), 'Controller did not handle request');
	}
	
	public function testPassingARequestWithMethodDeleteInvokesMappedMethod() {
		$req = new Asar_Request();
		$req->setMethod(Asar_Request::DELETE);
		$this->assertEquals('Deleted!', $req->sendTo($this->C)->__toString(), 'Controller did not handle request');
	}
	
	public function testRequestingAMappedResourceButUndefinedMethodMustReturnA405StatusResponse() {
		$req = new Asar_Request();
		$req->setMethod(Asar_Request::POST);
		$this->assertEquals(405, $req->sendTo(new Test_Controller_Another())->getStatusCode());
	}
	
	public function testRequestingAResourceWithHeadMethodShouldReturnTheSameResponseExceptContentWhenGetMethodIsDefinedForThatResource() {
		$req = new Asar_Request();
		$req->setMethod(Asar_Request::HEAD);
		$this->assertEquals(200, $req->sendTo($this->C)->getStatusCode());
	}
	
	public function testUsingHeadAsRequestMethodMustNotReturnAnyContent() {
		$req = new Asar_Request();
		$req->setMethod(Asar_Request::HEAD);
		$this->assertEquals('', $req->sendTo($this->C)->__toString(), 'Returned content for HEAD!');
	}
	
	public function testRequestingWithSubPaths() {
		$req = new Asar_Request();
		$req->setMethod(Asar_Request::GET);
		$req->setUri('/path/');
		$this->assertEquals('hello world', $req->sendTo($this->C)->__toString(), 'Controller did not handle request');
	}
	
	/*
	
	
	/*
	public function testRequestingAnUnmappedResourceResultsIn404StatusResponse() {
		$req = new Asar_Request();
		$req->setMethod(Asar_Request::GET);
		$req->setUri('/non-existent-path/');
		$this->assertEquals(404, $req->sendTo($this->C)->getStatusCode());
	}
	
	function testMakeSureControllerActionHasAccessToRequestObject() {
		$req = new Asar_Request();
		$testcontent = array(
		 'peach' => 'presses',
		 'stupid' => 'dog'
		);
		$req->setParams($testcontent);
		$response = $req->sendTo($this->C, array('action' => 'supercool'));
		$this->assertEquals($testcontent, $response->getContent(), 'Unexpected Result');
	}
  
  function testExposeRequestParamsAsParamsPropertyInController() {
    $req = new Asar_Request();
    $testcontent = array(
     'bull' => 'dung',
     'cat' => 'fight'
    );
    $req->setParams($testcontent);
    $response = $req->sendTo($this->C, array('action' => 'action1'));
    $this->assertEquals($testcontent, $response->getContent(), 'Unexpected Result');
  }
  
  function testForwardingToInternalAction() {
    $req = new Asar_Request();
    $testcontent = array(
     'bull' => 'cram',
     'cat' => 'fits'
    );
    $req->setParams($testcontent);
    $response = $req->sendTo($this->C, array('action' => 'action2'));
    $this->assertEquals($testcontent, $response->getContent(), 'Unexpected Result');
  }*/
  
}

?>