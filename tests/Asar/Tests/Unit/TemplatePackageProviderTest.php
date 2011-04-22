<?php

namespace Asar\Tests\Unit;

require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\TemplatePackageProvider;
use \Asar\Request;

class TemplatePackageProviderTest extends \Asar\Tests\TestCase {
  
  function setUp() {
    $this->locator  = $this->quickMock('Asar\TemplateLocator');
    $this->factory  = $this->quickMock('Asar\TemplateFactory');
    $this->tlf = new TemplatePackageProvider(
      $this->locator, $this->factory
    );
  }
  
  function testPassesResourceNameAndRequestToLocator() {
    $request = new Request;
    $this->locator->expects($this->once())
      ->method('locateFor')
      ->with('A\Resource', $request);
    $this->tlf->getTemplatesFor('A\Resource', $request);
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
    $this->tlf->getTemplatesFor('A_Resource', new Request);
  }
  
  function testReturnsArrayWithTemplateAsTemplateElement() {
    $tpl = $this->getMock('Asar\Template\TemplateInterface');
    $this->factory->expects($this->at(0))
      ->method('createTemplate')
      ->will($this->returnValue($tpl));
    $templates = $this->tlf->getTemplatesFor('A\Resource', new Request);
    $this->assertInternalType('array', $templates);
    $this->assertEquals($tpl, $templates['template']);
  }
  
  function testLocatingLayout() {
    $request = new Request;
    $this->locator->expects($this->once())
      ->method('locateFor')
      ->will($this->returnValue('foo.xml.php'));
    $this->locator->expects($this->once())
      ->method('locateLayoutFor')
      ->with('foo.xml.php');
    $this->tlf->getTemplatesFor('A\Resource', $request);
  }
  
  function testGettingMimeType() {
    $request = new Request;
    $this->locator->expects($this->once())
      ->method('locateFor')
      ->will($this->returnValue('foo.xml.php'));
    $this->locator->expects($this->once())
      ->method('getMimeTypeFor')
      ->with('foo.xml.php');
    $this->tlf->getTemplatesFor('A\Resource', $request);
  }
  
  function testFactoryUsesLayoutPathFromLocator() {
    $layout_path = 'foo';
    $this->locator->expects($this->once())
      ->method('locateLayoutFor')
      ->will($this->returnValue($layout_path));
    $this->factory->expects($this->at(1))
      ->method('createTemplate')
      ->with($layout_path);
    $this->tlf->getTemplatesFor('A\Resource', new Request);
  }
  
  function testReturnsArrayWithTemplateAsLayoutElement() {
    $tpl = $this->getMock('Asar_Template_Interface');
    $this->factory->expects($this->at(1))
      ->method('createTemplate')
      ->will($this->returnValue($tpl));
    $templates = $this->tlf->getTemplatesFor('A\Resource', new Request);
    $this->assertEquals($tpl, $templates['layout']);
  }
  
  function testGettingMimeTypeFromResults() {
    $request = new Request;
    $this->locator->expects($this->once())
      ->method('getMimeTypeFor')
      ->will($this->returnValue('text/html'));
    $templates = $this->tlf->getTemplatesFor('A\Resource', $request);
    $this->assertEquals('text/html', $templates['mime-type']);
  }
  
}
