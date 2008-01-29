<?php

require_once 'Asar.php';

class AsarClientDefaultTest_Application extends Asar_Application {}
class AsarClientDefaultTest_Controller_Index extends Asar_Controller {
	function GET() {
		return 'Yes';
	}
}

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
	
	function testCreateRequestReadsServerRequestMethod() {
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$request = $this->client->createRequest();
		$this->assertEquals(Asar_Request::POST, $request->getMethod(), 'Method mismatch');
	}
	
	function testCreateRequestGetsUriFromRequestUriButSubtractTheQueryString() {
		$request = $this->client->createRequest();
		$this->assertEquals('/basic/var1/var2.txt', $request->getUri(), 'Unable to set Uri Properly');
	}
	
	function testCreateRequestGetsUriFromRequestUri() {
		$_SERVER['REQUEST_URI'] = '/basic/var1/var3.txt';
		$request = $this->client->createRequest();
		$this->assertEquals('/basic/var1/var3.txt', $request->getUri(), 'Unable to set Uri Properly');
	}
	
	function testCreateRequestReadsRedirectUrlWhenAvailable() {
		$_SERVER['REDIRECT_URL'] = '/funny/';
		$request = $this->client->createRequest();
		$this->assertEquals('/funny/', $request->getUri(), 'Unable to set Uri Properly');
	}
	
	function testCreatingRequestSetsParamsFromGetEnvironmentVariable() {
		$request = $this->client->createRequest();
		$this->assertEquals($this->expected_params, $request->getParams(), 'Parameters were not set');
	}
	
	function testSendingRequestSendsResponseContentToOutputBuffer() {
		$_SERVER['REQUEST_URI'] = '/';
		$request = $this->client->createRequest();
		ob_start();
		$this->client->sendRequestTo($request, new AsarClientDefaultTest_Application);
		$test = ob_get_clean();
		$this->assertEquals('Yes', $test, 'Response content was not outputed');
	}
}
?>