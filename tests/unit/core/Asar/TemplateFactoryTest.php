<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

class Asar_TemplateFactoryTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->engine = get_class($this) . '_DummyTemplateClass';
    if (!class_exists($this->engine)) {
      //$this->getMock('Asar_Template_Interface', array(), array(), $this->engine);
      eval('
        class '. $this->engine . ' implements Asar_Template_Interface {
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
    $this->F = new Asar_TemplateFactory;
  }
  
  function testBasicTemplateCreation() {
    $this->F->registerTemplateEngine('php', $this->engine);
    $template = $this->F->createTemplate('foo.php');
    $this->assertType($this->engine, $template);
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
      array(new stdClass),
      array(100)
    );
  }
  
  function testTemplateCreationPassesTemplateFileToTemplate() {
    $this->F->registerTemplateEngine('php', $this->engine);
    $template = $this->F->createTemplate('bar.php');
    $this->assertEquals('bar.php', $template->getTemplateFile());
  }
  
  function testSettingAnotherTemplateEngine() {
    $engine2 = get_class($this->getMock('Asar_Template_Interface'));
    $this->F->registerTemplateEngine('x', $engine2);
    $this->F->registerTemplateEngine('php', $this->engine);
    $template = $this->F->createTemplate('foo.x');
    $this->assertType($engine2, $template);
  }
  
  function testGettingRegisteredEngines() {
    $engine2 = get_class($this->getMock('Asar_Template_Interface'));
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