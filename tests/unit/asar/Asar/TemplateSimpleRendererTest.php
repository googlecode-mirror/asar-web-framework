<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\TemplateSimpleRenderer;

class Asar_TemplateSimpleRendererTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->tsr = new TemplateSimpleRenderer;
    $this->tpl = $this->getMock('Asar\Template\TemplateInterface');
    $this->layout = $this->getMock('Asar\Template\TemplateInterface');
  }

  private function tplExpectsRender() {
    return $this->tpl->expects($this->once())->method('render');
  }
  
  function testRenderingWithOnlyTemplateObject() {
    $vars = array('foo' => 'bar');
    $this->tplExpectsRender()->with($vars);
    $this->tsr->renderTemplate($this->tpl, $vars);
  }
  
  function testRenderingWithOnlyTemplateObjectReturnsRenderValueFromTemplate() {
    $output = "Hello world!";
    $this->tplExpectsRender()->will($this->returnValue($output));
    $this->assertEquals(
      $output, $this->tsr->renderTemplate($this->tpl, array())
    );
  }
  
  function testRenderingWithLayout() {
    $this->layout->expects($this->once())
      ->method('render')
      ->with(array('content' => 'foo'));
    $this->tplExpectsRender()->will($this->returnValue('foo'));
    $this->tsr->renderTemplate($this->tpl, array(), $this->layout);
  }
  
  function testPassingLayoutVars() {
    $this->layout->expects($this->once())
      ->method('render')
      ->with(array('content' => 'template content', 'foo' => 'bar'));
    $this->tplExpectsRender()->will($this->returnValue('template content'));
    $this->tpl->expects($this->once())
      ->method('getLayoutVars')
      ->will($this->returnValue(array('foo' => 'bar')));
    $this->tsr->renderTemplate($this->tpl, array(), $this->layout);
  }
  
  function testRenderingWithBadTemplateReturnsNull() {
    $vars = array('foo' => 'bar');
    $this->assertNull($this->tsr->renderTemplate(1, $vars));
  }
  
  function testRenderingWithBadLayoutReturnsJustRenderFromTemplate() {
    $this->tplExpectsRender()->will($this->returnValue('foo'));
    $this->assertEquals(
      'foo', $this->tsr->renderTemplate($this->tpl, array(), 'bad layout')
    );
  }
  
  function testSkipsLayoutWhenTemplateIsConfiguredNoLayout() {
    $this->layout->expects($this->never())
      ->method('render');
    $this->tpl->expects($this->once())
      ->method('render')
      ->will($this->returnValue('Inside Template'));
    $this->tpl->expects($this->once())
      ->method('getConfig')
      ->with('no_layout')
      ->will($this->returnValue(true));
    $this->tsr->renderTemplate($this->tpl, array(), $this->layout);
  }
  
}
