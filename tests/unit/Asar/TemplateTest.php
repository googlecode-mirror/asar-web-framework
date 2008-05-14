<?php
/**
 * Created on Jul 3, 2007
 * 
 * @author     Wayne Duran
 */

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once ('Asar.php');


class Asar_TemplateTest_TestHelper {
	public static function upperCase($str) {
		return strtoupper($str);
	}
}

class Asar_TemplateTest_TestHelper2 {
	public static function lowerCase($str) {
		return strtolower($str);
	}
}


/**
 * Test class for Template.
 * Generated by PHPUnit_Util_Skeleton on 2007-07-02 at 01:03:33.
 */
class Asar_TemplateTest extends Asar_Test_Helper {

    protected $cleanUpList = array();
    
    private static $template_path;
	private static $template_name = 'inc.php';
	private static $template_contents = '
<h2>This is in included template</h2>
<p><?= $var ?></p>
<p><strong><?= $this[\'var2\'] ?></strong></p>
';

    
    public static function main() {
        require_once "PHPUnit/TextUI/TestRunner.php";
        $suite  = new PHPUnit_Framework_TestSuite("TemplateTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    protected function setUp() {
    	$this->T = new Asar_Template;
		self::newFile(self::$template_name,self::$template_contents);
		self::$template_path = self::getPath(self::$template_name);
    }

    protected function tearDown() {
    	$this->T = null;
    	Asar_Template::clearHelperRegistry();
    }
    
    /**
     * @todo: Create test to check if 'short tags' are enabled
     */    
    public function testAlternativeSettingTemplateToUse() {
    	$this->T->setTemplate(self::$template_path);
    	$this->T->set('var', 'Testing');
    	$this->T->set('var2', 'TestingAgain');
    	
    	$haystack = $this->T->fetch();
    	$this->assertContains('<p>Testing</p>', $haystack, 'Unable to set variable for file'.$haystack);
    	$this->assertContains('<p><strong>TestingAgain</strong></p>', $haystack, 'Unable to set variable for file');
    }
     
    public function testThrowingErrorWhenMissingTemplateUsed() {
    	$this->setExpectedException('Asar_Template_Exception');
    	$this->T->fetch('NonexistentTemplateFile');
    	$this->assertContains('NonexistingTemplateFile', $e->getMessage(), 'Asar_Template_Exception does not properly indicate which template it tried to include');
    }
    
    public function testReturnsNullWhenMissingTemplateUsed() {
		$this->setExpectedException('Asar_Template_Exception');    	
		$haystack = $this->T->fetch('NonexistentTemplateFile');
    	$this->assertEquals(null, $haystack, 'Template did not return null when including non-existentTemplateFile');
    }

    public function testSettingVariables() {
    	$this->T->set('var', 'Testing');
    	$this->T->set('var2', 'TestingAgain');
    	
    	$haystack = $this->T->fetch(self::$template_path);
    	
    	$this->assertContains('<p>Testing</p>', $haystack, 'Unable to set variable for file');
    	$this->assertContains('<p><strong>TestingAgain</strong></p>', $haystack, 'Unable to set variable for file');
    }
    
    public function testMultiSetVariables() {
    	$this->T->setVars(array('var' => 'Testing',
					            'var2'=> 'TestingAgain'));
		
    	$haystack = $this->T->fetch(self::$template_path);
    	
    	$this->assertContains('<p>Testing</p>', $haystack, 'Unable to set variable for file');
    	$this->assertContains('<p><strong>TestingAgain</strong></p>', $haystack, 'Unable to set variable for file');
    }

	public function testMultiSetVariablesUsingSetOnly() {
    	$this->T->set(array('var' => 'Nesting',
					        'var2'=> 'NestingAgain'));
		
    	$haystack = $this->T->fetch(self::$template_path);
    	
    	$this->assertContains('<p>Nesting</p>', $haystack, 'Unable to set variable for file');
    	$this->assertContains('<p><strong>NestingAgain</strong></p>', $haystack, 'Unable to set variable for file');
    }
    
    public function testArrayTypeSettingAndGetting() {
    	$this->T['var'] = 'Testing';
		$this->T['var2'] = 'TestingAgain';
		
    	$haystack = $this->T->fetch(self::$template_path);
    	
    	$this->assertContains('<p>Testing</p>', $haystack, 'Unable to set variable for file');
    	$this->assertContains('<p><strong>TestingAgain</strong></p>', $haystack, 'Unable to set variable for file');
    	
    	$this->assertEquals('Testing', $this->T['var'], 'Unexpected value for template variable');
    	$this->assertEquals('TestingAgain', $this->T['var2'], 'Unexpected value for template variable');
    }
    
    public function test__ToString() {
    	$ver = explode( '.', phpversion() );
		$ver_num = $ver[0] . $ver[1] . $ver[2];
    	if ($ver_num < 520) {
    		$this->markTestSkipped(
              'This test will only run correctly in PHP versions not less than 5.2.x'
            );
    	}
    	
    	$this->T['var'] = 'Testing';
		$this->T['var2'] = 'TestingAgain';
    	$this->T->setTemplate(self::$template_path);
    	$this->assertContains('<p>Testing</p>', $this->T.'', 'Unable to set variable for file');
    	$this->assertContains('<p><strong>TestingAgain</strong></p>', $this->T.'', 'Unable to set variable for file');
    	
    }
    
    public function testEcho__ToString() {
    	
    	$this->T['var'] = 'Testing';
		$this->T['var2'] = 'TestingAgain';
    	$this->T->setTemplate(self::$template_path);
		ob_start();
			echo $this->T;
		$haystack = ob_get_contents();
		ob_end_clean();
    	$this->assertContains('<p>Testing</p>', $haystack, 'Unable to set variable for file');
    	$this->assertContains('<p><strong>TestingAgain</strong></p>', $haystack, 'Unable to set variable for file');
    	
    }
    
    public function testUninitializedVariables() {
    	$testr = 'And If This Ain\'t Love, Why Does it Feel So Good?';
    	
    	$this->T['var'] = $testr;
    	
    	$haystack = $this->T->fetch(self::$template_path);
    	
    	$this->assertContains('<p>'.$testr.'</p>', $haystack, 'Unable to set variable for file');
    	$this->assertContains('<p><strong></strong></p>', $haystack, 'Unable to set an empty string to an unitialized variable for file');
    	
    }
    
    public function testRegisterHelper() {
    	Asar_Template::registerHelper('Asar_TemplateTest_TestHelper');
    	
    	$testf = 'temp/regtest.php';
    	$testf_content = '<h4><?= $this->upperCase($psst) ?></h4>';
    	self::newFile($testf, $testf_content);
    	
    	$teststring = 'Karasa';
    	
    	$this->T['psst'] = $teststring;
    	$this->assertEquals('<h4>'.strtoupper($teststring).'</h4>', $this->T->fetch(self::getPath($testf)), 'Unable to invoke the registered helper method');
    }
    
    
	/**
	 * Test for calling unregistered Method
	 * 
	 * @todo How to get the message from exception
	 */
	public function testCallingUnregisteredMethod() {
		$testf = 'temp/regtest.php';
		$testf_content = '<h4><?= $this->upperCase($psst) ?></h4>';
		self::newFile($testf, $testf_content);
		$teststring = 'Karasa';
		
		$this->T['psst'] = $teststring;
		
		$this->setExpectedException('Asar_Template_Exception');
		ob_start();
		$b = $this->T->fetch(self::getPath($testf));
		ob_end_clean();
		
	}
    
    public function testRegisterManyHelpers() {
    	Asar_Template::registerHelper('Asar_TemplateTest_TestHelper');
    	Asar_Template::registerHelper('Asar_TemplateTest_TestHelper2');
    	
    	$testf = 'temp/regtest.php';
    	$testf_content = '<h4><?= $this->upperCase($psst) ?></h4><p><?= $this->lowerCase($psst)?></p>';
    	self::newFile($testf, $testf_content);
    	
    	$teststring = 'Karasa';
    	
    	$this->T['psst'] = $teststring;
    	$this->assertEquals('<h4>'.strtoupper($teststring).'</h4><p>'.strtolower($teststring).'</p>', $this->T->fetch(self::getPath($testf)), 'Unable to invoke the registered helper method');
    }
	
	function testGettingTemplateThatWasSet()
	{
		$this->T->setTemplate('yo.html');
		$this->assertEquals('yo.html', $this->T->getTemplate(), 'Unable to get the template set using setTemplate()');
	}
	
	function testGetController()
	{
		$controller = $this->getMock('Asar_Controller');
		$this->T->setController($controller);
		$this->assertSame($controller, $this->T->getController(), 'Unable to set controller');
	}
	
}
