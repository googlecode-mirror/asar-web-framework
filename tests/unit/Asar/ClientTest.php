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
		$this->client = new Asar_Client();
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
		$path = '/people/get/asartalo/tags/reallyStupid';
		$arguments = array(
			'scheme'    => 'http',
			'authority' => 'example.host.com',
			'path'      => $path,
			'method'    => 'GET',
			'headers'   => array(
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
		$r = $this->client->createRequest($arguments);
		$this->assertEquals(Asar_Request::GET, $r->getMethod(), 'Method mismatch');
		$this->assertEquals($path, $r->getPath(), 'Address mismatch');
		$this->assertEquals('http', $r->getUriScheme(), 'Scheme mismatch');
		$this->assertEquals('example.host.com', $r->getUriAuthority(), 'URI Authority mismatch');
		$this->assertTrue($this->arrayMatch($arguments['params'], $r->getParams()), 'Parameters did not match');
		$this->assertEquals($r, $this->client->getRequest(), 'Client did not set internal request property');
	}
	
	function testCreatingARequestWithNoArgumentsPassedSendsDefaultPathAsIndex() {
		$r = $this->client->createRequest();
		$this->assertEquals('/', $r->getPath(), 'Path mismatch');
	}
	
	function testSendRequestSendsNewlyCreatedRequestObjectToApplication() {
		$address = '/people/get/asartalo/tags/reallyStupid/';
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
		$this->client->createRequest($address, $arguments);
		$this->assertTrue($this->client->sendRequestTo(new Test3_Application) instanceof Asar_Response, 'Response is not an instance of Asar_Response');
		$this->assertTrue($this->client->getResponse() instanceof Asar_Response, 'Unable to set response property for client');
	}
	
	
	function testSetAndGetName() {
		$testname = 'A really cool name for a client';
		$this->client->setName($testname);
		$this->assertEquals($testname, $this->client->getName(), 'Client name did not match expected value');
	}
	
	function testSettingContentForCreateRequest()
	{
		$arguments = array(
			'scheme'    => 'http',
			'authority' => 'example.host.com',
			'method' => 'POST',
			'path' => '/',
			'content' => array(
				'key1' => 'avalue',
				'2key' => 'anothervalue'
			)
		);
		$contents = $this->client->createRequest($arguments)->getContent();
		$this->assertEquals('avalue', $contents['key1'], 'The first value was not found on request content');
		$this->assertEquals('anothervalue', $contents['2key'], 'The second value was not found on request content');
	}
	
}
