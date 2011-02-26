<?php
require_once realpath(dirname(__FILE__) . '/../../../../config.php');

class Asar_MessageFilter_DevelopmentTest extends PHPUnit_Framework_TestCase {
  
  private $html = "<html>\n<head>\n</head>\n<body>\n</body>\n</html>";
  
  function setUp() {
    $this->config = new Asar_Config(array());
    $this->debug  = new Asar_Debug;
    $this->printer = $this->getMock('Asar_DebugPrinter_Interface');
    $this->filter = new Asar_MessageFilter_Development($this->config, $this->debug);
    $this->filter->setPrinter('html', $this->printer);
    $this->response = new Asar_Response;
    $this->response->setContent($this->html);
  }
  
  function testDevFilterDelegatesPrintingDebugInfoToPrinter() {
    $this->response->setHeader('Asar-Internal-Debug', $this->debug);
    $this->printer->expects($this->once())
      ->method('printDebug')
      ->with($this->debug, $this->html);
    $this->filter->filterResponse($this->response);
  }
  
  function testDevFilterDelegatesToAnotherPrinter() {
    $this->response->setHeader('Content-Type', 'text/plain');
    $this->response->setHeader('Asar-Internal-Debug', $this->debug);
    $this->printer->expects($this->never())
      ->method('printDebug');
    $this->filter->filterResponse($this->response);
  }
  
  function testDevFilterDelegatesToMatchedPrinter() {
    $this->response->setHeader('Content-Type', 'text/plain');
    $this->response->setHeader('Asar-Internal-Debug', $this->debug);
    $text_printer = $this->getMock('Asar_DebugPrinter_Interface');
    $this->filter->setPrinter('txt', $text_printer);
    $text_printer->expects($this->once())
      ->method('printDebug')
      ->with($this->debug, $this->html);
    $this->filter->filterResponse($this->response);
  }
  
  function testDevFilterUsesOutputFromPrinter() {
    $this->response->setHeader('Asar-Internal-Debug', $this->debug);
    $this->printer->expects($this->once())
      ->method('printDebug')
      ->will($this->returnValue('foo'));
    $this->assertEquals(
      'foo', $this->filter->filterResponse($this->response)->getContent()
    );
  }
  
  function testDevFilterAddsDebugToRequestInternalHeader() {
    $request = new Asar_Request;
    $debug = $this->filter->filterRequest($request)->getHeader('Asar-Internal-Debug');
    $this->assertInstanceof('Asar_Debug', $debug);
  }
  
  function testReturnsDebugFromConstruction() {
    $request = new Asar_Request;
    $this->assertSame(
      $this->debug,
      $this->filter->filterRequest($request)->getHeader('Asar-Internal-Debug')
    );
  }
  
  function testAddsExecutionTimeToDebug() {
    $this->response->setHeader('Asar-Internal-Debug', $this->debug);
    $this->filter->filterResponse($this->response);
    $str = $this->debug->get('Execution Time');
    if (strpos($str, 'E') > 0) {
      $this->assertRegExp('/[0-9]+(.)?[0-9]*E-[0-9]+ microseconds/', $str);
    } else {
      $this->assertRegExp('/[0-9]+(.)?[0-9]* microseconds/', $str);
    }
  }
  
  function testAddsMemoryUsageToDebug() {
    $this->response->setHeader('Asar-Internal-Debug', $this->debug);
    $this->filter->filterResponse($this->response);
    $str = $this->debug->get('Memory Used');
    $this->assertRegExp('/[0-9]+.[0-9]{2}(M|K)B/', $str);
  }
  
  function testAddsApplicationNameIfApplicationInternalHeaderIsSet() {
    $request = new Asar_Request;
    $request->setHeader('Asar-Internal-Application-Name', 'FooApp');
    $filtered_request = $this->filter->filterRequest($request);
    $this->assertEquals('FooApp', $this->debug->get('Application'));
  }
  
}
