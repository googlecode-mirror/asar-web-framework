<?php

require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config.php';

class Asar_ErrorsFTest extends Asar_Test_Helper
{
    protected $app;
    protected $request;

    public function setUp()
    {
        $this->app     = new App_Application;
        $this->request = new Asar_Request;
    }

	function testResponseStatusIs404SendProper404Message()
	{
		$this->request->setPath('/non-existent-path');
		$response = $this->request->sendTo($this->app);
		$this->assertContains('Sorry, we were unable to find the resource you were looking for. Please check that you got the address or URL correctly. If that is the case, please email the administrator. Thank you and please forgive the inconvenience.',
			$response->__toString(), 'Application did not return a proper 404 message' );
	}
	
	function testResponseStatusIs404SendProper404Heading()
	{
		$this->request->setPath('/another-non-existent-path');
		$response = $this->request->sendTo($this->app);
		$this->assertContains('<h1>File Not Found (404)</h1>',
			$response->__toString(), 'Application did not return a proper 404 heading' );
	}
	
	function testResponseStatusIs405SendProper405Message()
	{
		$this->request->setMethod('POST');
		$response = $this->request->sendTo($this->app);
		$this->assertContains("The HTTP Method 'POST' is not allowed for this resource.",
			$response->__toString(), 'Application did not return a proper 405 message' );
	}
	
	function testResponseStatusIs405SendProper405Heading()
	{
		$this->request->setMethod('POST');
		$response = $this->request->sendTo($this->app);
		$this->assertContains("Method Not Allowed (405)",
			$response->__toString(), 'Application did not return a proper 405 heading' );
	}
	
	function testResponseStatusIs500SendProper500Message()
	{
		$this->request->setPath('/errors');
		$response = $this->request->sendTo($this->app);
		$this->assertEquals(500, $response->getStatus(), 'The response status is not 500');
		$this->assertContains('The application has encountered some problems. Please email the administrator.',
			$response->__toString(), 'Application did not return a proper 500 message' );
	}
}