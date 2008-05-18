<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Asar_Controller_DefaultTest extends Asar_Test_Helper {
	
	function setUp()
	{
		$this->C = new Asar_Controller_Default;
		$this->response = new Asar_Response;
		$this->R = new Asar_Request;
		$this->R->setContent($this->response);
	}
	
	function testDefaultControllerCanSetResponse()
	{
		$this->R->sendTo($this->C);
		$this->assertSame($this->response, self::readAttribute($this->C, 'response'), 'Unable to set the response object');
	}
	
	function rightMessage($message = null, $heading = null)
	{
		if (!$message) {
			return;
		}
		$this->assertNotSame(null, $this->response->getContent(), 'The response content is null!');
		$this->assertContains($message,
			$this->response->getContent(), 'Unable to find the message: "' . $message .'"');
		$this->assertContains($heading,
			$this->response->getContent(), 'id not send the right heading message: "' . $heading . '"');
		
		$template = (self::readAttribute($this->C, 'view'));
		$this->assertEquals($heading, $template['heading'], 'Did not set that heading message on the heading variable of template');
	}
	
	function rightDefaultTemplates() {
		$template = (self::readAttribute($this->C, 'view'));
		$this->assertEquals('Asar/View/Default/ALL.html.php', $template->getTemplate(), 'Did not use Asar/View/Default/ALL.html.php.');
		$this->assertEquals('Asar/View/Layout.html.php', $template->getLayout(), 'Did not use Asar/View/Layout.html.php. Bummer...');
		
		$this->assertContains('id="asar_main_content"', $this->response->getContent(), 'Did not really make use of Asar/View/Default/ALL.html.php. Bummer...');
	}
	
	
	function testWhenTheStatusIs404Load404Message()
	{
		$this->response->setStatus(404);
		$this->R->sendTo($this->C);
		$this->rightMessage(
			'Sorry, we were unable to find the resource you were looking for. Please check that you got the address or URL correctly. If that is the case, please email the administrator. Thank you and please forgive the inconvenience.',
			'File Not Found (404)'
		);
		$this->rightDefaultTemplates();
	}
	
	function testWhenTheStatusIs405Load405Message()
	{
		$this->response->setStatus(405);
		$this->R->sendTo($this->C);
		$this->rightMessage(
			'The HTTP Method \'GET\' is not allowed for this resource.',
			'Method Not Allowed (405)'
		);
		$this->rightDefaultTemplates();
	}
	
	function testDifferentHttpStatusMethodShouldSetTheRightMessage()
	{
		$methods = array(
			'POST', 'GET', 'HEAD', 'DELETE', 'PUT'
		);
		$method = $methods[rand(0,4)];
		$this->R->setMethod($method);
		$this->response->setStatus(405);
		$this->R->sendTo($this->C);
		$this->rightMessage(
			"The HTTP Method '$method' is not allowed for this resource.",
			"Method Not Allowed (405)"
		);
	}
	
	function testWhenTheStatusIs500Load500Message()
	{
		$this->response->setStatus(500);
		$this->R->sendTo($this->C);
		$this->rightMessage(
			'The application has encountered some problems. Please email the administrator.',
			'Internal Server Error (500)'
		);
		$this->rightDefaultTemplates();
	}
}