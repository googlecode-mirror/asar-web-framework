<?php
require_once realpath(dirname(__FILE__). '/../../../config.php');

// This class is for testing purposes only
class Asar_Template_Builder_DummyEngine implements Asar_Template_Interface {
  function render() {}
  
  function __set($variable, $value = null) {}
  
  function set($variable, $value = null) {}
  
  function setTemplateFile($file) {}
  
  function setLayoutFile($file) {}
  
  function getLayout() {}
  
  function getTemplateFile() {}
  
  function getTemplateFileExtension() {
    return 'tbd';
  }
}

class Asar_Template_BuilderTest extends Asar_Test_Helper {
  
  private static $prefCount = 0;
  
  function setUp() {
    $this->tpldir = self::getTempDir();
    $this->R = $this->_createResourceMock($this->_generatePrefix());
    $this->R->setConfiguration(array(
      'default_representation_dir' => $this->tpldir
    ));
    $this->request = new Asar_Request;
    $this->B = Asar_Template_Builder::getBuilder($this->R);
  }
  
  private function _createResourceMock($prefix, $name = 'Index') {
    return $this->getMock(
    'Asar_Resource', array('GET'), array(),
    $prefix . '_Resource_' . $name
    );
  }
  
  private function _generatePrefix() {
    return get_class($this). '_App' . (++self::$prefCount);
  }
  
  function testGettingBuilder() {
    $this->assertTrue(
      $this->B instanceof Asar_Template_Builder,
      'Unable to get Asar_Template_Builder object.'
    );
  }
  
  function testBuilderReturnsCorrectTemplateObject(array $options = array()) {
    extract(array_merge(array(
      'fname' => 'Index.GET.html.php', 'method' => 'GET', 'type' => 'html',
      'rname' => 'Index', 'layout' => false, 'engine' => 'Asar_Template'),
      $options
    ));
    $file = Asar::constructPath($this->tpldir, $fname);
    self::newFile($fname, '');
    echo "\n--$file";
    if ($layout) {
      self::newFile($layout, '');
    }
    if ($engine != 'Asar_Template') {
      $this->B->setEngine($engine);
    }
    $tpl = $this->B->getTemplate($method, $type);
    $this->assertType($engine, $tpl);
    $this->assertEquals($file, $tpl->getTemplateFile());
    if ($layout) {
      $layout_file = Asar::constructPath($this->tpldir, $layout);
      $this->assertEquals($layout_file, $tpl->getLayout()->getTemplateFile());
    }
  }
  
  function testBuilderReturnsCorrectTemplateObject2() {
    $this->testBuilderReturnsCorrectTemplateObject(
      array('fname' => Asar::constructPath('Index', 'GET.html.php'))
    );
  }
  
  function testBuilderReturnsCorrectTemplateObjectPOST() {
    $this->testBuilderReturnsCorrectTemplateObject(
      array('fname' => 'Index.POST.html.php', 'method' => 'POST')
    );
  }
  
  function testBuilderReturnsCorrectTemplateObjectTxtType() {
    $this->testBuilderReturnsCorrectTemplateObject(
      array('fname' => 'Index.GET.txt.php', 'type' => 'txt')
    );
  }
  
  function testBuilderThrowsExceptionWhenNoTemplateFileIsFound() {
    $this->setExpectedException(
      'Asar_Template_Builder_Exception', 
      'Unable to build template for ' . get_class($this->R) . ' with '.
          'GET html request.'
    );
    $tpl = $this->B->getTemplate('GET', 'html');
  }
  
  function testBuilderSetsLayoutWhenItExists() {
    $this->testBuilderReturnsCorrectTemplateObject(
      array('layout' => 'Layout.html.php')
    );
  }
  
  function testBuilderSetsLayoutXmlType() {
    $this->testBuilderReturnsCorrectTemplateObject(array(
      'fname' => 'Index.GET.xml.php', 'layout' => 'Layout.xml.php',
      'type' => 'xml'
    ));
  }
  
  function testBuilderWithADifferentTemplateEngine() {
    $b = new Asar_Template_Builder_DummyEngine;
    echo "\n======\n", $b->getTemplateFileExtension(), "\n=====";
    $this->testBuilderReturnsCorrectTemplateObject(array(
      'fname' => 'Index.GET.html.tbd',
      'engine' => 'Asar_Template_Builder_DummyEngine'
    ));
  }
  
}
