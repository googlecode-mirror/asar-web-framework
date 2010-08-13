<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

class Asar_ResponseExporterTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->exporter = new Asar_ResponseExporter;
  }
  
  function testExportOutputsContentOfResponse() {
	  $response = new Asar_Response;
	  $response->setContent('The quick brown fox.');
	  ob_start();
	  $this->exporter->exportResponse($response);
	  $content = ob_get_clean();
	  $this->assertEquals(
	    'The quick brown fox.', $content
    );
	}
	
	function testExportResponseHeadersUsesHeaderFunctionWrapper() {
	  $exporter = $this->getMock('Asar_ResponseExporter', array('header'));
	  $exporter->expects($this->exactly(3))
	    ->method('header');
    $exporter->expects($this->at(0))
      ->method('header')
      ->with($this->equalTo('Content-Type: text/plain'));
    $exporter->expects($this->at(1))
      ->method('header')
      ->with(
      $this->equalTo('Content-Encoding: gzip')
      );
    $exporter->expects($this->at(2))
      ->method('header')
      ->with($this->equalTo('HTTP/1.1 404 Not Found'));
	  $response = new Asar_Response;
	  $response->setHeader('Content-Type', 'text/plain');
	  $response->setHeader('Content-Encoding', 'gzip');
	  $response->setStatus(404);
	  $exporter->exportResponse($response);
	}
  
}