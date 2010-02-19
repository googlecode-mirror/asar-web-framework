<?php
require_once realpath(dirname(__FILE__). '/../../config.php');

class Asar_InterpreterTest extends Asar_Test_Helper {

  function setUp() {
    $this->I = new Asar_Interpreter;
  }
  
  function testInterpretForSendsARequestToApp() {
    $app = $this->getMock('Asar_Requestable', array('handleRequest'));
    $app->expects($this->once())
      ->method('handleRequest')
      ->with($this->isInstanceOf('Asar_Request'))
      ->will($this->returnValue(new Asar_Response));
    $this->I->interpretFor($app);
  }
	
	function testImportRequest() {
	  $this->assertTrue(
	    $this->I->importRequest() instanceof Asar_Request,
	    'ImportRequest() did not create an Asar_Request object.'
    );
	}
	
	function testImportRequestBaseRequestCreationOnEnvironmentVariables() {
	  $_SERVER['REQUEST_METHOD'] = 'POST';
	  $_SERVER['REQUEST_URI']  = '/a_page';
	  $R = $this->I->importRequest();
	  $this->assertEquals(
	    'POST', $R->getMethod(),
	    'Did not set the request method to server values.'
    );
    $this->assertEquals(
      '/a_page', $R->getPath(),
      'Did not set the request path to server value.'
    );
	}
	
	function testImportRequestBaseRequestCreationOnEnvironmentVariables2() {
	  $_SERVER['REQUEST_METHOD'] = 'GET';
	  $_SERVER['REQUEST_URI']  = '/another/path';
	  $R = $this->I->importRequest();
	  $this->assertEquals(
	    'GET', $R->getMethod(),
	    'Did not set the request method to server values.'
    );
    $this->assertEquals(
      '/another/path', $R->getPath(),
      'Did not set the request path to server value.'
    );
	}
	
	function testImportRequestSettingPathWithQueryVariables() {
	  $_SERVER['REQUEST_URI'] = '/a/path/to/page2?foo=bar&prog=ram';
	  $this->assertEquals(
	    '/a/path/to/page2', $this->I->importRequest()->getPath(),
	    'Did not properly set the request path with query variables.'
    );
	}
	
	function testImportRequestSetsContentOnRequestIfMethodIsPost() {
	  $_SERVER['REQUEST_METHOD'] = 'POST';
	  $_POST = array(
	    'foo' => 'bar',
	    'boo' => 'far',
	    'goo' => 'mar'
	  );
	  $R = $this->I->importRequest();
	  $this->assertEquals(
	    'POST', $R->getMethod(),
	    'Did not set request method to post.'
    );
	  $this->assertEquals(
	    $_POST, $R->getContent(),
	    'ImportRequest() did not set the post variables for POST request.'
    );
	}
	
	function testImportRequestDoesNotSetContentOnRequestIfMethodIsGet() {
	  $_SERVER['REQUEST_METHOD'] = 'GET';
	  $_POST = array(
	    'foo' => 'bar',
	    'boo' => 'far',
	    'goo' => 'mar',
	    'mammal' => 'cat',
	  );
	  $R = $this->I->importRequest();
	  $this->assertEquals(
	    null, $R->getContent(),
	    'ImportRequest() must not set the post variables for GET request.'
    );
	}
	
	function testImportRequestSetsHeaders() {
	  $_SERVER = array(
	    'REQUEST_METHOD'     => 'GET',
	    "HTTP_HOST"      => 'localhost',
	    "HTTP_USER_AGENT"	   => 'Mozilla/5.0 (X11; U; Linux i686;)',
	    "HTTP_ACCEPT"	     => 'text/html',
	    "HTTP_ACCEPT_LANGUAGE" => 'tl,en-us;q=>0.7,en;q=0.3',
	    "HTTP_ACCEPT_ENCODING" => 'gzip,deflate',
	    "HTTP_ACCEPT_CHARSET"  => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
	    "HTTP_KEEP_ALIVE"    => '300',
	    "HTTP_CONNECTION"    => 'keep-alive',
	    'REQUEST_URI'      => 'somewhere_over_the_rainbow'
    );
	  
	  $R = $this->I->importRequest();
	  foreach ($_SERVER as $key => $value) {
	    if (strpos($key, 'HTTP_') === 0) {
	      //TODO: not safe for headers that contain multiple instances
	      // of 'HTTP_' e.g. HTTP_RR_HTTP_baad. Rewrite recommended.
	      $use = str_replace('HTTP_', '', $key);
	      $this->assertEquals(
	        $value, $R->getHeader($use),
	        "Unable to find '$use' header in request."
        );
	    } else {
	      if ($key == 'REQUEST_METHOD') {
	        $this->assertEquals(
	          $value, $R->getMethod(),
	          "Did not set request method to $value."
          );
        } elseif($key == 'REQUEST_URI') {
          $this->assertEquals(
            $value, $R->getPath(),
            "Did not set request path to $value."
          );
        }
      }
    }  
	}
	
	function testInterpretForUsesImportRequestToPassRequestToApp() {
	  $app = $this->getMock('Asar_Requestable', array('handleRequest'));
	  $app->expects($this->once())
	    ->method('handleRequest')
	    ->with($this->equalTo(
	      $this->I->importRequest()
	    ))
	    ->will($this->returnValue(new Asar_Response));
    $this->I->interpretFor($app);
	}
	
	
	function testExportResponseOutputsContentOfResponse() {
	  $response = new Asar_Response;
	  $response->setContent('The quick brown fox.');
	  ob_start();
	  $this->I->exportResponse($response);
	  $content = ob_get_clean();
	  $this->assertEquals(
	    'The quick brown fox.', $content,
	    'ExportResponse() did not send the contents of response to buffer.'
    );
	}
	
	
	function testExportResponseHeadersUsesHeaderFunctionWrapper() {
	  $I = $this->getMock('Asar_Interpreter', array('header'));
	  $I->expects($this->exactly(3))
	    ->method('header');
    $I->expects($this->at(0))
      ->method('header')
      ->with($this->equalTo('Content-Type: text/plain'));
    $I->expects($this->at(1))
      ->method('header')
      ->with(
      $this->equalTo('Content-Encoding: gzip')
      );
    $I->expects($this->at(2))
      ->method('header')
      ->with($this->equalTo('HTTP/1.1 404 Not Found'));
	  $response = new Asar_Response;
	  $response->setHeader('Content-Type', 'text/plain');
	  $response->setHeader('Content-Encoding', 'gzip');
	  $response->setStatus(404);
	  $I->exportResponseHeaders($response);
	}
	
	function testExportResponseHeadersUsesHeaderFunctionWrapper2() {
	  $I = $this->getMock('Asar_Interpreter', array('header'));
	  $I->expects($this->exactly(2))
	    ->method('header');
    $I->expects($this->at(0))
      ->method('header')
      ->with($this->equalTo('Content-Type: text/html'));
    $I->expects($this->at(1))
      ->method('header')
      ->with($this->equalTo('HTTP/1.1 200 OK'));
	  $response = new Asar_Response;
	  $response->setHeader('Content-Type', 'text/html');
	  $response->setStatus(200);
	  $I->exportResponseHeaders($response);
	}
	
	function testExportResponseHeadersUsesResonseStatusMessages() {
    $status_tests = array(200, 201, 100, 300, 303, 400, 404, 500);
    $response = new Asar_Response;
    foreach ($status_tests as $status) {
      $response->setStatus($status);
      $I = $this->getMock('Asar_Interpreter', array('header'));
      $I->expects($this->any())
        ->method('header')
        ->with($this->equalTo(
          "HTTP/1.1 $status " . Asar_Response::getStatusMessage($status)
        ));
      $I->exportResponseHeaders($response);
    }
	  
	}
	
	
	function testExportResponseUsesExportResponseHeaders() {
	  $response = new Asar_Response;
	  $response->setContent('<ul><li>One</li><li>Two</li><li>Three</li></ul>');
	  $I = $this->getMock('Asar_Interpreter', array('exportResponseHeaders'));
	  //TODO:See if you can use identicalTo instead of equalTo
	  $I->expects($this->once())
	    ->method('exportResponseHeaders')
	    ->with($this->equalTo($response));
	  ob_start();
	  $I->exportResponse($response);
	  ob_end_clean();
	}
	
	function testInterpretForUsesExportResponse() {
	  $response = new Asar_Response;
	  $response->setHeader('Foo', 'bar');
	  $app = $this->getMock('Asar_Requestable', array('handleRequest'));
	  $app->expects($this->once())
	    ->method('handleRequest')
	    ->will($this->returnValue($response));
    $I = $this->getMock('Asar_Interpreter', array('exportResponse'));
    $I->expects($this->once())
	    ->method('exportResponse')
	    ->with($this->equalTo($response));
    $I->interpretFor($app);
	}
	
}
