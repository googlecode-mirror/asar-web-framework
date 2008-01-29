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
		
		if (isset($_SERVER['REDIRECT_URL'])) unset($_SERVER['REDIRECT_URL']);
		
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
	
	protected function tearDown() {
		$_SERVER = array();
		$_GET = array();
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
		$this->assertEquals('/basic/var1/var2.txt', $request->getPath(), 'Unable to set Uri Properly');
	}
	
	function testCreateRequestGetsUriFromRequestUri() {
		$_SERVER['REQUEST_URI'] = '/basic/var1/var3.txt';
		$request = $this->client->createRequest();
		$this->assertEquals('/basic/var1/var3.txt', $request->getPath(), 'Unable to set Uri Properly');
	}
	
	function testCreateRequestReadsRedirectUrlWhenAvailable() {
		$_SERVER['REDIRECT_URL'] = '/funny';
		$request = $this->client->createRequest();
		$this->assertEquals('/funny', $request->getPath(), 'Unable to set Uri Properly');
	}
	
	function testCreatingRequestSetsParamsFromGetEnvironmentVariable() {
		$request = $this->client->createRequest();
		$this->assertEquals($this->expected_params, $request->getParams(), 'Parameters were not set');
	}
	
	function testCreatingRequestWhen_SERVER_REDICT_URLIsUnsetWillThrowError() {
		unset($_SERVER['REQUEST_URI']);
		$this->setExpectedException('Asar_Client_Default_Exception');
		$this->client->createRequest();
	}
	
	function testSendingRequestSendsResponseContentToOutputBuffer() {
		$_SERVER['REQUEST_URI'] = '/';
		$this->client->createRequest();
		ob_start();
		$this->client->sendRequestTo(new AsarClientDefaultTest_Application);
		$test = ob_get_clean();
		$this->assertEquals('Yes', $test, 'Response content was not outputed');
	}
}
?>