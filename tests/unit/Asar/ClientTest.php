<?php

require_once 'Asar.php';

class Test3_Application extends Asar_Application {
	
	function __construct() {}
	
	function processRequest(Asar_Request $request, array $arguments = NULL) {
		return new Asar_Response;
	}
}

class Asar_ClientTest extends PHPUnit_Framework_TestCase {
	private $temporary_storage = array();
	
	
	function setUp() {
		$this->DC = new Asar_Client();
	}
	
	private function arrayCopy(&$from, &$to) {
		// clear destination array first
		$to = array();
		foreach ($from as $key => $value) {
			$to[$key] = $value;
		}
	}
	
	
	function arrayMatch($arr1, $arr2) {
		if (count($arr1) !== count($arr2)) {
			return false;
		}
		foreach($arr1 as $key => $val) {
			if ($val !== $arr2[$key]) {
				return false;
			}
		}
		return true;
	}
	
	function testCreateRequest() {
		$address = 'people/get/asartalo/tags/reallyStupid/';
		$arguments = array(
			'method'  => 'GET',
			'headers' => array(
				'Accept'          => 'text/html',
				'Accept-Encoding' => 'gzip,deflate'
			),
			'params' => array(
				'var1'   => 'val1',
				'var2'	 => 'val2',
				'enter'  => 'true',
				'center' => '1',
				'stupid' => '',
				'crazy'  => 'beautiful'
			)
		);
		$r = $this->DC->createRequest($address, $arguments);
		$this->assertEquals(Asar_Request::GET, $r->getMethod(), 'Method mismatch');
		$this->assertEquals($address, $r->getUri(), 'Address mismatch');
		$this->assertTrue($this->arrayMatch($arguments['params'], $r->getParams()), 'Parameters did not match');
	}
	
	function testSendRequest() {
		$expected_params = array(
			'var1'   => 'val1'
		);
		
		$this->DC->createRequest(
			'basic/enactment/var1/val1/var2/val2.txt?enter=true$center=1&stupid&crazy=beautiful',
			array(
				'params' => $expected_params,
				'method' => 'GET',
				'type'   => 'txt'
			)
		);
		$this->assertTrue($this->DC->sendRequestTo(new Test3_Application) instanceof Asar_Response, 'Response is not an instance of Asar_Response');
		$this->assertTrue($this->DC->getResponse() instanceof Asar_Response, 'Unable to set response property for client');
	}
	
	function testSetAndGetName() {
		$testname = 'A really cool name for a client';
		$this->DC->setName($testname);
		$this->assertEquals($testname, $this->DC->getName(), 'Client name did not match expected value');
	}
	/*
	function testSendingRequestAcceptsAResponse() {
		
	}*/
	
}
?>
