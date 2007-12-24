<?php

require_once 'Asar.php';


class Asar_Client_DefaultTest extends Asar_Test_Helper {
	
	protected function setUp()
	{
		$this->client = new Asar_Client_Default;
		
		$_SERVER['REQUEST_URI'] = '/basic/var1/var2.txt?enter=true$center=1&stupid&crazy=beautiful';
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
	    //$this->assertEquals($expected_params, $request->getParams(), 'Unable to get params');
	    //$this->assertEquals('txt', $request->getType(), 'Unable to get type');
	}
	
	function testSendRequestReadsServerRequestMethod() {
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$request = $this->client->createRequest();
		$this->assertEquals(Asar_Request::POST, $request->getMethod(), 'Method mismatch');
	}
}
?>