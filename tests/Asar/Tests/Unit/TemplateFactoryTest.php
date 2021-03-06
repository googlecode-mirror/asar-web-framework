<?php

namespace Asar\Tests\Unit;

require_once realpath(dirname(__FILE__). '/../../../config.php');

use Asar\TemplateFactory;

class TemplateFactoryTest extends \Asar\Tests\TestCase {
  
  function setUp() {
    $this->engine = $this->generateAppName('_DummyTemplateClass');
    if (!class_exists($this->engine)) {
      eval('
        class '. $this->engine . ' implements \Asar\Template\TemplateInterface {
          private $file;
          function setTemplateFile($file) {
            $this->file = $file;
          }
          
          function getTemplateFile() {
            return $this->file;
          }
          
          function render($vars=array()) {}
          
          function getLayoutVars() {}
          
          function getConfig($key) {}
          
          function setConfig($key, $value) {}
        }
      ');
    }
    $this->F = new TemplateFactory;
  }
  
  function testBasicTemplateCreation() {
    $this->F->registerTemplateEngine('php', $this->engine);
    $template = $this->F->createTemplate('foo.php');
    $this->assertInstanceOf($this->engine, $template);
  }
  
  /**
   * @dataProvider dataTemplateCreationArgumentNotPathWillReturnNull
   */
  function testTemplateCreationArgumentNotPathWillReturnNull($path) {
    $this->F->registerTemplateEngine('php', $this->engine);
    $this->assertNull($this->F->createTemplate($path));
  }
  
  function dataTemplateCreationArgumentNotPathWillReturnNull() {
    return array(
      array(false),
      array(null),
      array(array()),
      array(new \stdClass),
      array(100)
    );
  }
  
  function testTemplateCreationPassesTemplateFileToTemplate() {
    $this->F->registerTemplateEngine('php', $this->engine);
    $template = $this->F->createTemplate('bar.php');
    $this->assertEquals('bar.php', $template->getTemplateFile());
  }
  
  function testSettingAnotherTemplateEngine() {
    $engine2 = get_class($this->getMock('Asar\Template\TemplateInterface'));
    $this->F->registerTemplateEngine('x', $engine2);
    $this->F->registerTemplateEngine('php', $this->engine);
    $template = $this->F->createTemplate('foo.x');
    $this->assertInstanceOf($engine2, $template);
  }
  
  function testGettingRegisteredEngines() {
    $engine2 = get_class($this->getMock('Asar\Template\TemplateInterface'));
    $this->F->registerTemplateEngine('x', $engine2);
    $this->F->registerTemplateEngine('php', $this->engine);
    $engines = $this->F->getRegisteredTemplateEngines();
    $this->assertEquals($this->engine, $engines['php']);
    $this->assertEquals($engine2, $engines['x']);
  }
  
  function testTemplateCreationUnregisteredEngineWillReturnNull() {
    $this->F->registerTemplateEngine('php', $this->engine);
    $this->assertNull($this->F->createTemplate('foo.xtn'));
  }
  
  function testTemplatePathHasNoExtensionReturnsNull() {
    $this->F->registerTemplateEngine('php', $this->engine);
    $this->assertNull(
      $this->F->createTemplate('template_file_with_no_file_extnsion')
    );
  }
}
