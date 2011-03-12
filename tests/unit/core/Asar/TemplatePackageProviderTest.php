<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

class Asar_TemplatePackageProviderTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->locator  = $this->getMock(
      'Asar_TemplateLocator', array(), //array('locateFor', 'locateLayoutFor'),
      array(), '', false
    );
    $this->factory  = $this->getMock(
      'Asar_TemplateFactory', array(), //array('createTemplate'),
      array(), '', false
    );
    $this->tlf = new Asar_TemplatePackageProvider(
      $this->locator, $this->factory
    );
  }
  
  function testPassesResourceNameAndRequestToLocator() {
    $request = new Asar_Request;
    $this->locator->expects($this->once())
      ->method('locateFor')
      ->with('A_Resource', $request);
    $this->tlf->getTemplatesFor('A_Resource', $request);
  }
  
  private function locatorWillReturnValue($path) {
    return $this->locator->expects($this->once())
      ->method('locateFor')
      ->will($this->returnValue($path));
  }
  
  function testFactoryUsesReturnFromLocator() {
    $tpl_path = 'foo';
    $this->locatorWillReturnValue($tpl_path);
    $this->factory->expects($this->at(0))
      ->method('createTemplate')
      ->with($tpl_path);
    $this->tlf->getTemplatesFor('A_Resource', new Asar_Request);
  }
  
  function testReturnsArrayWithTemplateAsTemplateElement() {
    $tpl = $this->getMock('Asar_Template_Interface');
    $this->factory->expects($this->at(0))
      ->method('createTemplate')
      ->will($this->returnValue($tpl));
    $templates = $this->tlf->getTemplatesFor('A_Resource', new Asar_Request);
    $this->assertInternalType('array', $templates);
    $this->assertEquals($tpl, $templates['template']);
  }
  
  function testLocatingLayout() {
    $request = new Asar_Request;
    $this->locator->expects($this->once())
      ->method('locateFor')
      ->will($this->returnValue('foo.xml.php'));
    $this->locator->expects($this->once())
      ->method('locateLayoutFor')
      ->with('foo.xml.php');
    $this->tlf->getTemplatesFor('A_Resource', $request);
  }
  
  function testGettingMimeType() {
    $request = new Asar_Request;
    $this->locator->expects($this->once())
      ->method('locateFor')
      ->will($this->returnValue('foo.xml.php'));
    $this->locator->expects($this->once())
      ->method('getMimeTypeFor')
      ->with('foo.xml.php');
    $this->tlf->getTemplatesFor('A_Resource', $request);
  }
  
  function testFactoryUsesLayoutPathFromLocator() {
    $layout_path = 'foo';
    $this->locator->expects($this->once())
      ->method('locateLayoutFor')
      ->will($this->returnValue($layout_path));
    $this->factory->expects($this->at(1))
      ->method('createTemplate')
      ->with($layout_path);
    $this->tlf->getTemplatesFor('A_Resource', new Asar_Request);
  }
  
  function testReturnsArrayWithTemplateAsLayoutElement() {
    $tpl = $this->getMock('Asar_Template_Interface');
    $this->factory->expects($this->at(1))
      ->method('createTemplate')
      ->will($this->returnValue($tpl));
    $templates = $this->tlf->getTemplatesFor('A_Resource', new Asar_Request);
    $this->assertEquals($tpl, $templates['layout']);
  }
  
  function testGettingMimeTypeFromResults() {
    $request = new Asar_Request;
    $this->locator->expects($this->once())
      ->method('getMimeTypeFor')
      ->will($this->returnValue('text/html'));
    $templates = $this->tlf->getTemplatesFor('A_Resource', $request);
    $this->assertEquals('text/html', $templates['mime-type']);
  }
  
}
