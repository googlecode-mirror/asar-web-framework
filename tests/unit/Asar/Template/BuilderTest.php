<?php
require_once realpath(dirname(__FILE__). '/../../../config.php');

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
  
  function testBuilderReturnsCorrectTemplateObject() {
    $file = Asar::constructPath($this->tpldir, 'Index.GET.html.php');
    self::newFile('Index.GET.html.php', '');
    $tpl = $this->B->getTemplate('GET', 'html');
    $this->assertTrue(
      $tpl instanceof Asar_Template, 'Failed to create Asar_Template object.'
    );
    $this->assertEquals(
      $file, $tpl->getTemplateFile()
    );
  }
  
  function testBuilderReturnsCorrectTemplateObject2() {
    $file = Asar::constructPath($this->tpldir, 'Index', 'GET.html.php');
    self::newFile(Asar::constructPath('Index', 'GET.html.php'), '');
    $tpl = $this->B->getTemplate('GET', 'html');
    $this->assertEquals(
      $file, $tpl->getTemplateFile()
    );
  }
  
  function testBuilderThrowsExceptionWhenNoTemplateFileIsFound() {
    try {
      $tpl = $this->B->getTemplate('GET', 'html');
    } catch (Exception $e) {
      $this->assertTrue(
        $e instanceof Asar_Template_Builder_Exception,
        'Exception must be Asar_Template_Builder_Exception and not ' .
        get_class($e) . '.'
      );
      $this->assertEquals(
        'Unable to build template for ' . get_class($this->R) . ' with '.
          'GET html request.',
        $e->getMessage()
      );
      return;
    }
    $this->fail('Did not throw exception when no template file.');
  }
  
  function testBuilderReturnsCorrectTemplateObjectPOST() {
    $file = Asar::constructPath($this->tpldir, 'Index.POST.html.php');
    self::newFile('Index.POST.html.php', '');
    $tpl = $this->B->getTemplate('POST', 'html');
    $this->assertEquals($file, $tpl->getTemplateFile());
  }
  
  function testBuilderReturnsCorrectTemplateObjectTxtType() {
    $file = Asar::constructPath($this->tpldir, 'Index.GET.txt.php');
    self::newFile('Index.GET.txt.php', '');
    $tpl = $this->B->getTemplate('GET', 'txt');
    $this->assertEquals($file, $tpl->getTemplateFile());
  }
  
}
