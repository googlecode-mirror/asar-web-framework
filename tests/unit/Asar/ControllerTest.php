<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Test_Controller extends Asar_Controller {
	protected $map = array(
		'/' => 'index',
		'/path1' => 'method1'
	);
	
	public function GET_index() {
		return 'hello world';
	}
	
	public function GET_method1() {
		return 'hello there';
	}
	
	public function POST_method1() {
		return 'I am alright';
	}
	
	public function PUT_method1() {
		return 'Put it on';
	}
	
	public function DELETE_method1() {
		return 'Deleted!';
	}
}

class Test_Controller_Without_Index extends Asar_Controller {}

class Asar_ControllerTest extends PHPUnit_Framework_TestCase {
  
	protected function setUp() {
		$this->C = new Test_Controller();
	}
	
	public function testPassingARequestWithNoSpecifiedPathButWithMethodGetInvokesIndexWithGetMethod() {
		$req = new Asar_Request();
		$req->setMethod(Asar_Request::GET);
		$this->assertEquals('hello world', $req->sendTo($this->C)->__toString(), 'Controller did not handle request');
	}
	
	public function testPassingARequestWithSpecifiedPathWithMethodGetInvokesMappedMethod() {
		$req = new Asar_Request();
		$req->setMethod(Asar_Request::GET);
		$req->setUri('/path1');
		$this->assertEquals('hello there', $req->sendTo($this->C)->__toString(), 'Controller did not handle request');
	}
	
	public function testPassingARequestWithSpecifiedPathWithMethodPostInvokesMappedMethod() {
		$req = new Asar_Request();
		$req->setMethod(Asar_Request::POST);
		$req->setUri('/path1');
		$this->assertEquals('I am alright', $req->sendTo($this->C)->__toString(), 'Controller did not handle request');
	}
	
	public function testPassingARequestWithSpecifiedPathWithMethodPutInvokesMappedMethod() {
		$req = new Asar_Request();
		$req->setMethod(Asar_Request::PUT);
		$req->setUri('/path1');
		$this->assertEquals('Put it on', $req->sendTo($this->C)->__toString(), 'Controller did not handle request');
	}
	
	public function testPassingARequestWithSpecifiedPathWithMethodDeleteInvokesMappedMethod() {
		$req = new Asar_Request();
		$req->setMethod(Asar_Request::DELETE);
		$req->setUri('/path1');
		$this->assertEquals('Deleted!', $req->sendTo($this->C)->__toString(), 'Controller did not handle request');
	}
	
	public function testRequestingAnUnmappedResourceResultsIn404StatusResponse() {
		$req = new Asar_Request();
		$req->setMethod(Asar_Request::GET);
		$req->setUri('/non-existent-path');
		$this->assertEquals(404, $req->sendTo($this->C)->getStatusCode());
	}
	
	public function testRequestingAMappedResourceButUndefinedMethodMustReturnA405StatusResponse() {
		$req = new Asar_Request();
		$req->setMethod(Asar_Request::POST);
		$req->setUri('/');
		$this->assertEquals(405, $req->sendTo($this->C)->getStatusCode());
	}
	
	public function testRequestingAMappedResourceWithHeadMethodShouldReturnTheSameResponseExceptContentWhenGetMethodIsDefinedForThatResource() {
		$req = new Asar_Request();
		$req->setMethod(Asar_Request::HEAD);
		$req->setUri('/');
		$this->assertEquals(200, $req->sendTo($this->C)->getStatusCode());
	}
	/*
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