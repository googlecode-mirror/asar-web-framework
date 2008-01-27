<?php

require_once 'Asar.php';


class Asar_Client_DefaultTest extends Asar_Test_Helper {
	
	protected function setUp()
	{
		$this->client = new Asar_Client_Default;
		
		$_SERVER['REQUEST_URI'] = '/basic/var1/var2.txt?enter=true&center=1&stupid&crazy=beautiful';
		$_SERVER['QUERY_STRING'] = 'enter=true&center=1&stupid&crazy=beautiful';
	    $_SERVER['REQUEST_METHOD'] = 'GET';
	    $_GET['enter']  = 'true';
	    $_GET['center'] = '1';
	    $_GET['stupid'] = '';
	    $_GET['crazy']  = 'beautiful';
		
		$this->expected_params = array(
	      'enter'  => 'true',
	      'center' => '1',
	      'stupid' => '',
	      'crazy'  => 'beautiful'
	    );
	}
	
	function testSendRequest() {
    	$request = $this->client->createRequest();
	    $this->assertEquals('Asar_Request', get_class($request), 'Invalid object type. Must be \'Asar_Request\'.');
	}
	
	function testSendRequestReadsServerRequestMethod() {
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$request = $this->client->createRequest();
		$this->assertEquals(Asar_Request::POST, $request->getMethod(), 'Method mismatch');
	}
	
	function testSendRequestGetsUriFromRequestUriButSubtractTheQueryString() {
		$request = $this->client->createRequest();
		$this->assertEquals('/basic/var1/var2.txt', $request->getUri(), 'Unable to set Uri Properly');
	}
	
	function testSendRequestGetsUriFromRequestUri() {
		$_SERVER['REQUEST_URI'] = '/basic/var1/var3.txt';
		$request = $this->client->createRequest();
		$this->assertEquals('/basic/var1/var3.txt', $request->getUri(), 'Unable to set Uri Properly');
	}
	
	function testSendRequestReadsRedirectUrlWhenAvailable() {
		$_SERVER['REDIRECT_URL'] = '/funny/';
		$request = $this->client->createRequest();
		$this->assertEquals('/funny/', $request->getUri(), 'Unable to set Uri Properly');
	}
	
	function testCreatingRequestSetsParamsFromGetEnvironmentVariable() {
		$request = $this->client->createRequest();
		$this->assertEquals($this->expected_params, $request->getParams(), 'Parameters were not set');
	}
}
?>