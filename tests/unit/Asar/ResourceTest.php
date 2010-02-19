<?php
require_once realpath(dirname(__FILE__). '/../../config.php');

class Asar_ResourceTest extends Asar_Test_Helper {

  private static $prefCount = 0;

  function setUp() {
    $this->R = new Asar_Resource;
  }
  
  function testShouldBeAbleToSetRequestAttribute() {
    $this->request = new Asar_Request;
    $this->R = $this->getMock('Asar_Resource', array('GET'));
    $this->R->expects($this->once())
      ->method('GET')
      ->will($this->returnCallBack(array($this, 'checkRequestAttribute')));
    $this->R->handleRequest($this->request);
  }
  
  function checkRequestAttribute() {
    $this->assertSame(
      $this->request,
      $this->readAttribute($this->R, 'request'),
      'Unable to set request attribute in resource object.'
    );
  }
  
  function testResponseShouldProcessGetRequest() {
    $request = new Asar_Request;
    $request->setMethod('GET');
    $R = $this->getMock('Asar_Resource', array('GET'));
    $R->expects($this->once())
      ->method('GET')
      ->will($this->returnValue('hello world'));
    $response = $R->handleRequest($request);
    $this->assertEquals(
      'hello world', $response->getContent(),
      'Unable to set content for response'
    );
  }
  
  function testResponseShouldBe200WhenNormalProcessingOfGetRequest() {
    $request = new Asar_Request;
    $request->setMethod('GET');
    $R = $this->getMock('Asar_Resource', array('GET'));
    $R->expects($this->once())
      ->method('GET')
      ->will($this->returnValue('hello world'));
    $response = $R->handleRequest($request);
    $this->assertEquals(
      200, $response->getStatus(),
      'Unable to set 200 status for response'
    );
  }
  
  function testResponseContentTypeShouldBeHtmlByDefaultGetRequest() {
    $request = new Asar_Request;
    $request->setMethod('GET');
    $R = $this->getMock('Asar_Resource', array('GET'));
    $R->expects($this->once())
      ->method('GET')
      ->will($this->returnValue('hello world'));
    $response = $R->handleRequest($request);
    $this->assertContains(
      'text/html', $response->getHeader('Content-Type'),
      'Content-type of response does not contain "text/html"'
    );
    $this->assertEquals(
      0, strpos($response->getHeader('Content-Type'), 'text/html'),
      'Unable to set default content-type of "text/html" for response'
    );
  }
  
  function testExecutePOSTMethodWhenPostRequest() {
    $request = new Asar_Request;
    $request->setMethod('POST');
    $R = $this->getMock('Asar_Resource', array('POST'));
    $R->expects($this->once())
      ->method('POST')
      ->will($this->returnValue('hello post world'));
    $response = $R->handleRequest($request);
    $this->assertEquals(
      'hello post world', $response->getContent(),
      'Unable to set content for response'
    );
    $this->assertEquals(
      200, $response->getStatus(),
      'Unable to set content for response'
    );
  }
  
  function testExecutingPOSTMethodSetsContentOfPostGlobalVariable() {
    $request = new Asar_Request;
    $request->setMethod('POST');
    $request->setContent(array('foo'=>'bar', 'one' => 1));
    $R = $this->getMock('Asar_Resource', array('POST'));
    $response = $R->handleRequest($request);
    $this->assertEquals(
      'bar', $_POST['foo'],
      'Unable to set post variable'
    );
    $this->assertEquals(
      1, $_POST['one'],
      'Unable to set post variable'
    );
    $_POST = array();
  }
  
  function testResourceWithoutDefinedHttpMethodShouldReturn405HttpStatus() {
    $cname = get_class($this) . '_ResourceStatus405';
    eval('
      class '. $cname . ' extends Asar_Resource {}
    ');
    $R = Asar::instantiate($cname);
    $request = new Asar_Request;
    foreach (array('GET', 'POST', 'PUT', 'DELETE') as $method) {
      $request->setMethod($method);
      $response = $R->handleRequest($request);
      $this->assertEquals(
        405, $response->getStatus(),
        'The HTTP Response Status is not 405 for undefined method ' .
          $method . '.'
      );
    }
  }
  
  function testResourceShouldSendResponse500StatusWhenMethodRaisesException() {
    $R = $this->getMock('Asar_Resource', array('GET'));
    $R->expects($this->once())
      ->method('GET')
      ->will($this->returnCallBack(array($this, 'checkRaisingException')));
    $response = $R->handleRequest(new Asar_Request);
    $this->assertEquals(
      500, $response->getStatus(),
      'The HTTP Response Status is not 500 for method throwing an Exception'
    );
  }
  
  function checkRaisingException() {
    throw new Exception;
  }
  
  function testResourceShouldShowErrorMessageByDefaultFor500Response() {
    $R = $this->getMock('Asar_Resource', array('GET'));
    $R->expects($this->once())
      ->method('GET')
      ->will($this->returnCallBack(array($this, 'checkRaisingExceptionMsg')));
    $response = $R->handleRequest(new Asar_Request);
    $this->assertContains(
      'The error message.', $response->getContent(),
      'The Resource did not set the error message in content by default.'
    );
  }
  
  function checkRaisingExceptionMsg() {
    throw new Exception('The error message.');
  }
  
  function testResourceTriggersErrorWhenTryingToAccessUnknownProperty() {
    set_error_handler(array($this, 'temporaryErrorHandler'));
    $this->R->something;
    restore_error_handler();
    $this->assertNotNull(
      $this->getObject('error'),
      'Accessing unknown property did not trigger any error.'
    );
    $error = $this->getObject('error');
    $this->assertContains(
      'Unknown property \'something\' via __get()', $error[1],
      'Wrong error message sent.'
    );
    
  }
  
  function temporaryErrorHandler($err_no, $err_str) {
    $this->saveObject(
      'error', array($err_no, $err_str)
    );
  }
  
  function testResourceAttemptsToRenderTemplateWhenTemplateIsSet() {
    $this->R->template = $this->getMock('Asar_Template_Interface', array());
    $this->R->template->expects($this->once())
      ->method('render');
    $this->R->handleRequest(new Asar_Request);
  }
  
  function testResourceAttemptsToRenderTemplateWhenTemplateIsSetPOST() {
    $this->R = new Asar_Resource;
    $this->R->template = $this->getMock('Asar_Template_Interface', array());
    $this->R->template->expects($this->once())
      ->method('render');
    $req = new Asar_Request;
    $req->setMethod('POST');
    $this->R->handleRequest($req);
  }
  
}
