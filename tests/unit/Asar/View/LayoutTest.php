<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Asar_View_LayoutTest extends Asar_Test_Helper { 
	
	private $default_title = 'Asar Web Framework';
	private static $template_name = 'inc.php';
	private static $template_path;
	private static $template_contents = '
<h2>This is in included template</h2>
<p><?= $var ?></p>';
	
	function setUp()
	{
		$this->T = new Asar_Template_Html;
		self::newFile(self::$template_name,self::$template_contents);
	    self::$template_path = self::getPath(self::$template_name);
		$this->T->setTemplate(self::$template_path);
		$this->T->setLayout('Asar/View/Layout.html.php');
		$this->T['var'] = 'Hello World!';
	}
	
	function testCallingLayoutTemplate() {
		$result = $this->T->fetch();
		$this->assertContains($this->default_title, $result, 'Did not find expected title in result');
	}
	
	function testSettingADifferentTitle()
	{
		$this->T->setLayoutVar('title', 'Something Else');
		$result = $this->T->fetch();
		$this->assertContains('Something Else - '. $this->default_title, $result, 'Unable to set title in result');
	}
	
	function testObtainingContent()
	{
		$result = $this->T->fetch();
		$this->assertContains('Hello World!', $result, 'Did not set the content of layout');
	}
}