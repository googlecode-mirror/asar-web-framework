<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Test_Controller extends Asar_Controller {
  function cool() {
    $this->response->setContent('cool');
  }
  
  function index() {
    $this->response->setContent('index');
  }
  
  function supercool() {
  	$params = $this->request->getParams();
  	$this->response->setContent($params);
  }
  
  function action1() {
  	$this->response->setContent($this->params);
  }
  
  function action2() {
    $this->forwardTo('action1');
  }
}

class Test_Controller_Without_Index extends Asar_Controller {}

class Asar_ControllerTest extends PHPUnit_Framework_TestCase {
  
  protected function setUp() {
    $this->C = new Test_Controller();
  }
  
  function testSendingToControllerWithoutActionArgumentMustInvokeDefaultAction() {
  	$req = new Asar_Request();
  	$response = $req->sendTo($this->C);
  	$this->assertEquals('index', $response->getContent(), 'The response returned an unexpected value for content');
  }
  
  function testSendingToControllerWithoutDefaultAction() {
    $req = new Asar_Request();
    $b = new Test_Controller_Without_Index();
    try {
      $req->sendTo($b);
    } catch (Exception $e) {
    	$this->assertTrue($e instanceof Asar_Controller_ActionNotFound_Exception, 'Wrong exception thrown');
    }
  }
  
  function testSendingToControllerWithTheRightAction() {
    $req = new Asar_Request();
    $response = $req->sendTo($this->C, array('action' => 'cool'));
    $this->assertEquals('cool', $response->getContent(), 'The response returned an unexpected value for content');
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
  }
  
}

?>