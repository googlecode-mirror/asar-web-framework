<?php
require_once realpath(dirname(__FILE__). '/../../config.php');

/**
 * Test class for Template.
 * Generated by PHPUnit_Util_Skeleton on 2007-07-02 at 01:03:33.
 */
class Asar_TemplateTest extends Asar_Test_Helper {

	private static $template_path;
	private static $template_name = 'inc.php';
	private static $template_contents = '
    <h2>This is in included template</h2>
    <p><?= $var ?></p>';
  private static $template2_path;
  private static $template2_name = 'template.php';
  private static $template2_contents = '
    <h1><?= $h1 ?></h1>
    <p><?= $paragraph ?></p>
    <p><?= isset($paragraph2) ? $paragraph2 : "Default text" ?></p>';
  private static $layout_path;
  private static $layout_name = 'Layout.php';
  private static $layout_contents = '
    <html>
    <head>
      <title><?= isset($title) ? $title : "Layout Title"?></title>
    </head>
    <body>
      <?= $content ?>
    </body>
    </html>';

  protected function setUp() {
  	$this->T = new Asar_Template;
		self::newFile(self::$template_name, self::$template_contents);
		self::$template_path = self::getPath(self::$template_name);
		
		self::newFile(self::$template2_name, self::$template2_contents);
		self::$template2_path = self::getPath(self::$template2_name);
		//echo self::$template2_path;
		self::newFile(self::$layout_name, self::$layout_contents);
		self::$layout_path = self::getPath(self::$layout_name);
  }
  
  function testSettingAVariableAndGettingItsOutputInATemplate() {
    $this->T->var = 'Hello there';
    $this->T->setTemplateFile(self::$template_path);
    $contents = $this->T->render();
    $this->assertContains(
      '<p>Hello there</p>', $contents,
      'Did not find set variable inside template'
    );
  }

  
  function testGettingTheTemplateFileSet() {
    $this->T->setTemplateFile(self::$template_path);
    $this->assertEquals(
      self::$template_path,
      $this->T->getTemplateFile(),
      'Template path was not obtained using getTemplateFile()'
    );
  }
  
  function testSettingAnotherVariableAndGettingItsOutputInATemplate() {
    $this->T->var = 'We are the world';
    $this->T->setTemplateFile(self::$template_path);
    $contents = $this->T->render();
    $this->assertContains(
      '<p>We are the world</p>', $contents,
      'Did not find set variable inside template'
    );
  }
  
  function testSettingAVariableUsingMethodAndGettingItsOutputInATemplate() {
    $this->T->set('var', 'Hello there');
    $this->T->setTemplateFile(self::$template_path);
    $contents = $this->T->render();
    $this->assertContains(
      '<p>Hello there</p>', $contents,
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
    
    $this->T->var1 = 'This is a title';
    $this->T->var2 = 'An Unsuspecting heading';
    $this->T->var3 = 'The quick brown fox jumps over the lazy dog.';
    
    $this->T->setTemplateFile(self::getPath('multivar.php'));
    $contents = $this->T->render();
    
    $this->assertContains(
      '<head><title>This is a title</title></head>', $contents,
      'Did not find first variable set'
    );
    $this->assertContains(
      '<h1>An Unsuspecting heading</h1>', $contents,
      'Did not find second variable set'
    );
    $this->assertContains(
      '<p>The quick brown fox jumps over the lazy dog.</p>', $contents,
      'Did not find third variable set'
    );
  }
  
  function testSettingMultipleVariables() {
    // Prepare the template file
    $this->T->set(array(
      'h1' => 'This is a test template',
      'paragraph' => 'A Foo Bar',
      'paragraph2' => 'The quick brown fox jumps over the lazy dog.'
    ));
    
    $this->T->setTemplateFile(self::$template2_path);
    $contents = $this->T->render();
    
    $this->assertContains(
      '<h1>This is a test template</h1>', $contents,
      'Did not find first variable set'
    );
    $this->assertContains(
      '<p>A Foo Bar</p>', $contents,
      'Did not find second variable set'
    );
    $this->assertContains(
      '<p>The quick brown fox jumps over the lazy dog.</p>', $contents,
      'Did not find third variable set'
    );
  }
  
  function testAsarTemplateImplementsAsarTemplateInterface() {
    $this->assertTrue(
      $this->T instanceof Asar_Template_Interface,
      'View did not implement Asar_Template_Interface'
    );
  }
  
	function testGettingTemplateFileExtension() {
		$this->assertEquals(
			'php', $this->T->getTemplateFileExtension(),
			'Asar_View did not return a proper file extension'
		);
	}
	
	function testSettingLayoutWrapsLayoutAroundTemplate() {
    $this->T->set(array(
      'h1' => 'Another heading',
      'paragraph' => 'Dummy text. Lorem ipsum dolor.'
    ));
    $this->T->setTemplateFile(self::$template2_path);
    $this->T->setLayout(self::$layout_path);
    $contents = $this->T->render();
    
    $this->assertContains(
      '<html>', $contents,
      'Did not find "<html>" tag on template render.'
    );
    
    $html = new Asar_Utility_XML($contents);
    
    $this->assertEquals(
      'Another heading',
      $html->body->h1->stringValue(),
      'Did not find paragraph value in template render.'
    );
    $this->assertEquals(
      'Dummy text. Lorem ipsum dolor.',
      $html->body->p->stringValue(),
      'Did not find paragraph value in template render.'
    );
    $this->assertEquals(
      'Layout Title',
      $html->head->title->stringValue(),
      'Did not find title value in template render.'
    );
    
	}
	
	function testForcingNotToRenderLayout() {
	  $this->T->set(array(
      'h1' => 'Another heading',
      'paragraph' => 'Dummy text. Lorem ipsum dolor.'
    ));
    $this->T->setTemplateFile(self::$template2_path);
    $this->T->setLayout(self::$layout_path);
    $this->T->noLayout();
    $contents = $this->T->render();
    
    $this->assertNotContains(
      '<html>', $contents,
      'Found "<html>" tag on template render.'
    );
    
    $this->assertContains(
      '<h1>Another heading</h1>', $contents,
      'Did not find heading value in template render.'
    );
	}
	
	function testLayoutMustNotRenderIfTheLayoutFileDoesNotExist() {
	  $this->T->set(array(
      'h1' => 'Yet another heading',
      'paragraph' => 'Dummy text. Lorem ipsum dolor.'
    ));
    $this->T->setTemplateFile(self::$template2_path);
    $this->T->setLayout('/non-existent/layout_file.php');
    $contents = $this->T->render();
    
    $this->assertNotContains(
      '<html>', $contents,
      'Layout was rendered.'
    );
    
    $this->assertContains(
      '<h1>Yet another heading</h1>', $contents,
      'Did not find heading value in template render.'
    );
	}
	
	function testAccessingLayoutVariable() {
	  $this->T->set(array('h1' => 'Foo', 'paragraph' => 'Bar'));
	  $this->T->setTemplateFile(self::$template2_path);
	  $this->T->setLayout(self::$layout_path);
	  $this->T->getLayout()->title = 'FooBar Title';
	  $html = new Asar_Utility_XML($this->T->render());
	  $this->assertContains(
	    'FooBar Title',
	    $html->head->title->stringValue(),
	    'Unable to access layout and set its value.'
    );
	}
	
	function testAccessingLayoutVariableBeforeSettingLayoutFile() {
	  $this->T->set(array('h1' => 'Foo', 'paragraph' => 'Bar'));
	  $this->T->setTemplateFile(self::$template2_path);
	  $this->T->getLayout()->title = 'FooBar Title';
	  $this->T->setLayout(self::$layout_path);
	  $html = new Asar_Utility_XML($this->T->render());
	  $this->assertContains(
	    'FooBar Title',
	    $html->head->title->stringValue(),
	    'Unable to access layout and set its value.'
    );
  }
  
  function testAttemptingToRenderANonExistentFileWillRaiseException() {
    $this->T->setTemplateFile('a/non/existent/file.php');
    try {
      $this->T->render();
      $this->fail('Did not raise exception');
    } catch (Asar_Exception $e) {
      $this->assertEquals(
        'Asar_Template_Exception_FileNotFound', get_class($e)
      );
      $this->assertEquals(
        'Asar_Template::render() failed. The file \'a/non/existent/file.php\' '.
        'does not exist.', $e->getMessage()
      );
    }
  }
  
  function testLogTemplatesUsedWhenInDebugMode() {
	  Asar::setMode(Asar::MODE_DEBUG);
	  $this->T->set(array('h1' => 'Foo', 'paragraph' => 'Bar'));
	  $this->T->setTemplateFile(self::$template2_path);
	  $this->T->setLayout(self::$layout_path);
	  $this->T->getLayout()->title = 'FooBar Title';
	  $this->T->render();
	  $debug = Asar::getDebugMessages();
	  $this->assertTrue(
	    array_key_exists('Templates Used', $debug),
	    '"Templates Used" key was not found in debug messages.'
	  );
	  $this->assertEquals(
	    $debug['Templates Used'], array(
	       self::$template2_path, self::$layout_path
	    )
	  );
	  Asar::setMode(Asar::MODE_DEVELOPMENT);
  }

}
