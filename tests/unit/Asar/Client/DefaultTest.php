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
		
		$_SERVER['SERVER_NAME'] = 'www.host.example';
		$_SERVER['REQUEST_URI'] = '/basic/var1/var2.txt?enter=true&center=1&stupid&crazy=beautiful';
		$_SERVER['QUERY_STRING'] = 'enter=true&center=1&stupid&crazy=beautiful';
	    $_SERVER['REQUEST_METHOD'] = 'GET';
		$_SERVER['HTTP_ACCEPT'] = 'text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5';
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
		$_POST = array();
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
	
	function testCreateRequestGetsHostFromServerName() {
		$request = $this->client->createRequest();
		$this->assertEquals('www.host.example', $request->getHost(), 'Host mismatch');
	}
	
	function testCreateRequestGetsUriFromRequestUriButSubtractTheQueryString() {
		$request = $this->client->createRequest();
		$this->assertEquals('/basic/var1/var2.txt', $request->getPath(), 'Unable to set URI Properly');
	}
	
	function testCreateRequestGetsUriFromRequestUri() {
		$_SERVER['REQUEST_URI'] = '/basic/var1/var3.txt';
		$request = $this->client->createRequest();
		$this->assertEquals('/basic/var1/var3.txt', $request->getPath(), 'Unable to set URI Properly');
	}
	
	function testCreateRequestReadsRedirectUrlWhenAvailable() {
		$_SERVER['REDIRECT_URL'] = '/funny';
		$request = $this->client->createRequest();
		$this->assertEquals('/funny', $request->getPath(), 'Unable to set URI Properly');
	}
	
	function testCreatingRequestSetsParamsFromGetEnvironmentVariable() {
		$request = $this->client->createRequest();
		$this->assertEquals($this->expected_params, $request->getParams(), 'Parameters were not set');
	}
	
	function testDefaultClientSetsTheUriSchemeToHttp() {
		$request = $this->client->createRequest();
		$this->assertEquals('http', $request->getUriScheme(), 'URI Scheme was not set to \'http\'');
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
	
	function testExportingResponseShouldSetHttpResponseHeaderToAsarResponse() {
		$response = new Asar_Response;
		$response->setType('txt');
		$this->markTestIncomplete('Difficult to implement because code requires setting headers');
	}
	
	function testGettingRequestMethodPostShouldSetContentForPostValues()
	{
		$_POST['key'] = 'randomvalue';
		$_POST['anotherkey'] = 'anothervalue';
		$request = $this->client->createRequest();
		$postvars = $request->getContent();
		$this->assertEquals('randomvalue', $postvars['key'], 'Expected post value "randomvalue" not found in request content');
		$this->assertEquals('anothervalue', $postvars['anotherkey'], 'Did not find "anothervalue" from request content');
	}
	
	function testCreatingRequestDestroysGetGlobalVariables() {
		$this->client->createRequest();
		$this->assertEquals(0, count($_GET), 'There are still some values in $_GET global variable');
	}
}
