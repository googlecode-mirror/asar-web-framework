<?php
require_once realpath(dirname(__FILE__). '/../../../config.php');

class Asar_Template_BuilderTest extends Asar_Test_Helper {
  
  private static $prefCount = 0;
  
  function setUp() {
    $this->tpldir = Asar::constructPath(self::getTempDir());
    $this->R = $this->_createResourceMock($this->_generatePrefix());
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
  
  function testBuilderReturnsCorrectTemplateObject() {
    $this->R->setConfiguration(array(
      'default_representation_dir' => $this->tpldir
    ));
    $file = Asar::constructPath($this->tpldir, 'Index.GET.html.php');
    $tpl = $this->B->getTemplate('GET', 'html');
    $this->assertTrue(
      $tpl instanceof Asar_Template, 'Failed to create Asar_Template object.'
    );
    $this->assertEquals(
      $file, $tpl->getTemplateFile()
    );
  }
  
}
