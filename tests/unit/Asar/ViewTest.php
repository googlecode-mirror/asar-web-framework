<?php

require_once 'Asar.php';
require_once 'PHPUnit/Framework.php';




/**
 * Test class for Template.
 * Generated by PHPUnit_Util_Skeleton on 2007-07-02 at 01:03:33.
 */
class Asar_ViewTest extends Asar_Test_Helper {

	private static $template_path;
	private static $template_name = 'inc.php';
	private static $template_contents = '
<h2>This is in included template</h2>
<p><?= $var ?></p>
';

    protected function setUp() {
    	$this->V = new Asar_View;
		self::newFile(self::$template_name,self::$template_contents);
		self::$template_path = self::getPath(self::$template_name);
    }
    
    function testSettingAVariableAndGettingItsOutputInATemplate() {
        $this->V->var = 'Hello there';
        $this->V->setTemplate(self::$template_path);
        $contents = $this->V->render();
        $this->assertContains('<p>Hello there</p>', $contents,
                              'Did not find set variable inside template'
        );
    }
    
    function testSettingAnotherVariableAndGettingItsOutputInATemplate() {
        $this->V->var = 'We are the world';
        $this->V->setTemplate(self::$template_path);
        $contents = $this->V->render();
        $this->assertContains('<p>We are the world</p>', $contents,
                              'Did not find set variable inside template'
        );
    }
    
    function testSettingAVariableUsingMethodAndGettingItsOutputInATemplate() {
        $this->V->set('var', 'Hello there');
        $this->V->setTemplate(self::$template_path);
        $contents = $this->V->render();
        $this->assertContains('<p>Hello there</p>', $contents,
                              'Did not find set variable inside template'
        );
    }
    
    function testSettingMultipleVariablesWithAttributeInterface() {
        // Prepare the template file
        self::newFile('multivar.php',
            '<html>
            <head><title><?= $var1 ?></title></head>
            <body>
                <h1><?= $var2 ?></h1>
                <p><?= $var3 ?></p>
            </body>
            </html>'
        );
        
        $this->V->var1 = 'This is a title';
        $this->V->var2 = 'An Unsuspecting heading';
        $this->V->var3 = 'The quick brown fox jumps over the lazy dog.';
        
        $this->V->setTemplate(self::getPath('multivar.php'));
        $contents = $this->V->render();
        
        $this->assertContains('<head><title>This is a title</title></head>',
                              $contents,
                              'Did not find first variable set'
        );
        $this->assertContains('<h1>An Unsuspecting heading</h1>',
                              $contents,
                              'Did not find second variable set'
        );
        $this->assertContains('<p>The quick brown fox jumps over the lazy dog.</p>',
                              $contents,
                              'Did not find third variable set'
        );
    }
    
    function testSettingMultipleVariables() {
        // Prepare the template file
        self::newFile('multivar.php',
            '<html>
            <head><title><?= $var1 ?></title></head>
            <body>
                <h1><?= $var2 ?></h1>
                <p><?= $var3 ?></p>
            </body>
            </html>'
        );
        
        $this->V->set(array(
            'var1' => 'This is a test template',
            'var2' => 'A heading',
            'var3' => 'The quick brown fox jumps over the lazy dog.'
        ));
        
        $this->V->setTemplate(self::getPath('multivar.php'));
        $contents = $this->V->render();
        
        $this->assertContains('<head><title>This is a test template</title></head>',
                              $contents,
                              'Did not find first variable set'
        );
        $this->assertContains('<h1>A heading</h1>',
                              $contents,
                              'Did not find second variable set'
        );
        $this->assertContains('<p>The quick brown fox jumps over the lazy dog.</p>',
                              $contents,
                              'Did not find third variable set'
        );
    }
    
    function testAsarViewImplementsAsarViewInterface() {
        $this->assertTrue($this->V instanceof Asar_View_Interface,
                          'View did not implement Asar_View_Interface'
        );
    }
    
    
}