<?php
require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Asar_Template_RendererTest extends PHPUnit_Framework_TestCase {
  
  
  function setUp() {
    $this->R = new Asar_Template_Renderer();
  }
  /*
  function testProcessResponse() {
    $template = $this->getMock('Asar_Response', array('
  }*/
  
  function testInstantiatingATemplate() {
    $this->markTestIncomplete('Needs implementation');
  }
  
  function testPassingTemplateParameters() {
    $params = array(
      'var1' => 1,
      'var2' => 2,
      'var3' => 'three',
      'var4' => array(1, 2, 3, 'key'=>'value')
    );
    
    $this->R->setTemplateParams($params);
    $this->assertEquals($params, $this->R->getTemplateParams(), 'Template Parameters were not passed properly');
  }
  
  function testGettingTemplateParametersPrematurelyReturnsAnEmptyArray() {
    $this->R->getTemplateParams();
    $this->assertEquals(array(), $this->R->getTemplateParams(), 'Unexpected value obtained from getTemplateParams(). Must return an empty array when accessed before setting any parameters at all.');
  }
  
  function testPassingATemplateNameInstantiatesATemplateObject() {
    //$tpl = $this->R->createTemplate(array('template' => 
    $this->markTestIncomplete();
  }
}

?>