<?php

namespace Asar\Tests\Unit\Template\Engines;

require_once realpath(dirname(__FILE__). '/../../../../../config.php');

use \Asar\Template\Engines\PhpEngine;

class PhpEngineTest extends \Asar\Tests\TestCase {
  
  function setUp() {
    $this->TFM = $this->getTFM();
    $this->clearTestTempDirectory();
    $this->T = new PhpEngine;
  }
  
  function tearDown() {
    $this->clearTestTempDirectory();
	}
  
  function testTemplateIncludesFile() {
    $this->TFM->newFile('foo.php', 'Hello!');
    $this->T->setTemplateFile($this->TFM->getPath('foo.php'));
    $this->assertEquals('Hello!', $this->T->render());
  }
  
  function testTemplatePassingVariables() {
    $this->TFM->newFile('inc.php', '<p><?php echo $var ?></p>');
    $this->T->setTemplateFile($this->TFM->getPath('inc.php'));
    $this->assertEquals(
      '<p>Hello World!</p>', $this->T->render(array('var' => 'Hello World!'))
    );
  }
  
  function testTemplatePassingASingleString() {
    $this->TFM->newFile('bar.php', '<p><?php echo $content ?></p>');
    $this->T->setTemplateFile($this->TFM->getPath('bar.php'));
    $this->assertEquals(
      '<p>Oh Dear!</p>', $this->T->render('Oh Dear!')
    );
  }
  
  function testGettingTemplateFile() {
    $this->TFM->newFile('baz.php', '');
    $this->T->setTemplateFile($this->TFM->getPath('baz.php'));
    $this->assertEquals(
      $this->TFM->getPath('baz.php'), $this->T->getTemplateFile()
    );
  }
  
  function testTemplateThrowsExceptionWhenSettingFileThatDoesNotExist() {
    $this->setExpectedException(
      'Asar\Template\Exception\TemplateFileNotFound'
    );
    $this->T->setTemplateFile('some-non-existent-file.php');
  }
  
  function testFileNotFoundExceptionMessageIsCorrect() {
    try {
      $this->T->setTemplateFile('missing.php');
    } catch (\Exception $e) {
      $this->assertEquals(
        "The file 'missing.php' passed to the template does not exist.",
        $e->getMessage()
      );
    }
  }
  
  function testGettingConfiguration() {
    $this->assertSame(FALSE, $this->T->getConfig('no_layout'));
  }
  
  function testGettingUnknownConfigurationReturnsNull() {
    $this->assertNull($this->T->getConfig('foo'));
  }
  
  function testGettingLayoutVars() {
    $this->assertEquals(array(), $this->T->getLayoutVars());
  }
  
  function testSettingLayoutVars() {
    $this->TFM->newFile('bar.php', '<?php $this->layout["foo"] = "bar"; ?>');
    $this->T->setTemplateFile($this->TFM->getPath('bar.php'));
    $this->T->render();
    $layout_vars = $this->T->getLayoutVars();
    $this->assertArrayHasKey('foo', $layout_vars);
    $this->assertEquals('bar', $layout_vars['foo']);
  }
  
}
