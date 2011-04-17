<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\TemplateRenderer;
use \Asar\Request;
use \Asar\Response;

class Asar_TemplateRendererTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->tpp = $this->getMock(
      'Asar\TemplatePackageProvider', array(), array(), '', false
    );
    $this->tsr = $this->getMock(
      'Asar\TemplateSimpleRenderer', array(), array(), '', false
    );
    $this->tpl = $this->getMock('Asar\Template\TemplateInterface');
    $this->renderer = new TemplateRenderer($this->tpp, $this->tsr);
  }
  
  private function tppRetursTemplates($arg = null) {
    if (!$arg) {
      $arg = array(
        'template' => $this->getMock('Asar\Template\TemplateInterface'),
        'layout' => 'bar'
      );
    }
    $this->tpp->expects($this->any())
      ->method('getTemplatesFor')
      ->will($this->returnValue($arg));
  }
  
  function testHandleRequestPassesResourceNameAndRequestToLFactory() {
    $request = new Request;
    $response = new Response;
    $this->tpp->expects($this->once())
      ->method('getTemplatesFor')
      ->with('A_Resource', $request);
    $this->tppRetursTemplates();
    $this->renderer->renderFor('A_Resource', $response, $request);
  }
  
  function testReturns406ResponseWhenLFactoryBadTemplate() {
    $arg = array(
      'template' => 'foo', 'layout' => 'bar'
    );
    $this->tppRetursTemplates($arg);
    $response = $this->renderer->renderFor(
      'Bar', new Response, new Request
    );
    $this->assertEquals(406, $response->getStatus());
  }
  
  function testPassesReturnFromLFactoryToRenderer() {
    $response = new Response(array('content' => 'response_argument'));
    $tpl = $this->getMock('Asar\Template\TemplateInterface');
    $arg = array(
      'template' => $tpl, 'layout' => $tpl, 'mime-type' => 'text/html'
    );
    $this->tppRetursTemplates($arg);
    $this->tsr->expects($this->once())
      ->method('renderTemplate')
      ->with($tpl, $response->getContent(), $tpl);
    $this->renderer->renderFor('A_Resource', $response, new Request);
  }
  
  function testSkipsRendererWhenLfactoryReturnsBadTemplate() {
    $response = new Response(array('content' => 'response_argument'));
    $arg = array(
      'template' => 'foo', 'layout' => 'bar', 'mime-type' => 'text/html'
    );
    $this->tppRetursTemplates($arg);
    $this->tsr->expects($this->never())
      ->method('renderTemplate');
    $this->renderer->renderFor('A_Resource', $response, new Request);
  }
  
  function testReturnsResponseWithRenderedContentFromSimpleRenderer() {
    $tpl = $this->getMock('Asar\Template\TemplateInterface');
    $arg = array(
      'template' => $tpl, 'layout' => $tpl, 'mime-type' => 'text/html'
    );
    $this->tppRetursTemplates($arg);
    $content = "Hello World!";
    $this->tsr->expects($this->once())
      ->method('renderTemplate')
      ->will($this->returnValue($content));
    $response = $this->renderer->renderFor(
      'Bar', new Response, new Request
    );
    $this->assertEquals($content, $response->getContent());
  }
  
  // TODO: Maybe move content-negotiation code to locator
  function testSetsContentTypeWhenTemplateIsAvailable() {
    $tpl = $this->getMock('Asar\Template\TemplateInterface');
    $arg = array(
      'template' => $tpl, 'layout' => 'foo', 'mime-type' => 'text/plain'
    );
    $this->tppRetursTemplates($arg);
    $content = "Hello World!";
    $this->tsr->expects($this->once())
      ->method('renderTemplate')
      ->will($this->returnValue($content));
    $request = new Request;
    $response = $this->renderer->renderFor(
      'Boo', new Response, $request
    );
    $this->assertEquals(
      'text/plain', $response->getHeader('Content-Type')
    );
  }

}
