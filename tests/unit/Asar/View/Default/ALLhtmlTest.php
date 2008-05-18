<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Asar_View_Default_AllhtmlTest extends Asar_Test_Helper {
	function testBasic()
	{
		$this->T = new Asar_Template_Html;
		$heading = 'This is a test heading for template';
		$message = 'This is the message.';
		$this->T['heading'] = $heading;
		$this->T['message'] = $message;
		$this->T->setTemplate('Asar/View/Default/ALL.html.php');
		$result = $this->T->fetch();
		$this->assertContains($heading, $result, 'Did not find heading on template');
		$this->assertContains($message, $result, 'Did not find message on template');
	}
}