<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

class Asar_ResourceTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->old_post_data = $_POST;
    $this->R = new Asar_Resource;
  }
  
  function tearDown() {
    $_POST = $this->old_post_data;
  }
  
  function testResourceImplementsResourceInterface() {
    $this->assertType('Asar_Resource_Interface', $this->R);
  }
  
  function testResourceImplementsConfigInterface() {
    $this->assertType('Asar_Config_Interface', $this->R);
    $this->config = $this->getMock('Asar_Config_Interface');
    $this->config->expects($this->once())
      ->method('getConfig')
      ->will($this->returnValue(array('foo' => 'bar')));
    $this->R->importConfig($this->config);
    $this->assertEquals('bar', $this->R->getConfig('foo'));
  }
  
  function testResourceReturnsAResponse() {
    $this->assertType(
      'Asar_Response', $this->R->handleRequest(new Asar_Request)
    );
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
  
  /**
   * @dataProvider dataRunsResourceMethodBasedOnRequestMethod
   */
  function testRunsResourceMethodBasedOnRequestMethod($method) {
    $request = new Asar_Request;
    $request->setMethod($method);
    $this->R = $this->getMock('Asar_Resource', array($method));
    $this->R->expects($this->once())
      ->method($method);
    $this->R->handleRequest($request);
  }
  
  function dataRunsResourceMethodBasedOnRequestMethod() {
    return array(
      array('GET'), array('POST'), array('PUT'), array('DELETE')
    );
  }
  
  private function requestProcessingTests(
    array $req_opts = array(),
    $method = 'GET',
    $method_run_times = 'once'
  ) {
    $request = new Asar_Request($req_opts);
    $request->setMethod($method);
    $R = $this->getMock('Asar_Resource', array($method));
    $R->expects($this->$method_run_times())
      ->method($method)
      ->will($this->returnValue('hello world'));
    return $R->handleRequest($request);
  }
  
  function testResourceShouldProcessGetRequest() {
    $response = $this->requestProcessingTests();
    $this->assertEquals('hello world', $response->getContent());
  }
  
  function testResponseShouldBe200WhenNormalProcessingOfGetRequest() {
    $response = $this->requestProcessingTests();
    $this->assertEquals(200, $response->getStatus());
  }
  
  function testExecutingPOSTMethodSetsContentOfPostGlobalVariable() {
    $this->requestProcessingTests(
      array('content' => array('foo'=>'bar', 'one' => 1)), 'POST'
    );
    $this->assertEquals('bar', $_POST['foo']);
    $this->assertEquals(1, $_POST['one']);
  }
  
  function testExecutingPOSTMethodSetsContentOfPostGlobalVariableEvenIfEmpty() {
    $this->requestProcessingTests(array(), 'POST');
    $this->assertType('array', $_POST);
  }
  
  function testResourceWithoutDefinedHttpMethodShouldReturn405HttpStatus() {
    $R = $this->getMock('Asar_Resource', array('some_method'));
    $request = new Asar_Request;
    foreach (array('GET', 'POST', 'PUT', 'DELETE') as $method) {
      $request->setMethod($method);
      $response = $R->handleRequest($request);
      $this->assertEquals(405, $response->getStatus());
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
    $this->assertEquals(
      'My Exception Message.', $response->getContent()
    );
  }
  
  function checkRaisingException() {
    throw new Exception('My Exception Message.');
  }
  
  function testResourceRunsSetupMethod() {
    $cname = get_class($this) . '_ResourceRunSetup';
    eval('
      class '. $cname . ' extends Asar_Resource {
        function setUp() {
          $_POST["foo"] = "bar";
        }
      }
    ');
    $R = new $cname;
    $this->assertTrue(array_key_exists('foo', $_POST));
    $this->assertEquals('bar', $_POST['foo']);
  }
  
  function testModifyConfigUseTemplates() {
    $cname = get_class($this) . '_DefaultUseTemplatesConfiguration';
    eval('
      class '. $cname . ' extends Asar_Resource {
        function setUp() {
          $this->config["use_templates"] = false;
        }
      }
    ');
    $R = new $cname;
    $this->assertFalse($R->getConfig('use_templates'));
  }
  
  function testModifyConfigValue() {
    $cname = get_class($this) . '_ChangeConfigValues';
    eval('
      class '. $cname . ' extends Asar_Resource {
        function GET() {
          $this->setConfig("foo", "bar");
        }
      }
    ');
    $R = new $cname;
    $R->GET();
    $this->assertEquals( "bar", $R->getConfig('foo'));
  }
  
  function testGetConfigOnUnknownConfigReturnsNull() {
    $this->assertSame(NULL, $this->R->getConfig('foo'));
  }
  
  function testDefineOwnDefaultContentType() {
    $cname = get_class($this) . '_DefaultCTypeConfiguration';
    eval('
      class '. $cname . ' extends Asar_Resource {
        function setUp() {
          $this->config["default_content_type"] = "text/plain";
        }
      }
    ');
    $R = new $cname;
    $this->assertEquals('text/plain', $R->getConfig('default_content_type'));
  }
  
  function testReturnResponsetContentTypeFromConfig() {
    $cname = get_class($this) . '_DefaultCTypeConfiguration2';
    eval('
      class '. $cname . ' extends Asar_Resource {
        function setUp() {
          $this->config["default_content_type"] = "application/xml";
        }
      }
    ');
    $R = new $cname;
    $this->assertEquals(
      'application/xml', 
      $R->handleRequest(new Asar_Request(array(
        'headers' => array('Accept' => 'application/xml')
      )))->getHeader('Content-Type')
    );
  }
  
  function testDefineOwnDefaultLanguage() {
    $cname = get_class($this) . '_DefaultLangConfiguration';
    eval('
      class '. $cname . ' extends Asar_Resource {
        function setUp() {
          $this->config["default_language"] = "tl";
        }
      }
    ');
    $R = new $cname;
    $this->assertEquals('tl', $R->getConfig('default_language'));
  }
  
  function testReturnResponsetContentLanguageFromConfig() {
    $cname = get_class($this) . '_DefaultLangConfiguration2';
    eval('
      class '. $cname . ' extends Asar_Resource {
        function setUp() {
          $this->config["default_language"] = "de";
        }
      }
    ');
    $R = new $cname;
    $this->assertEquals(
      'de', 
      $R->handleRequest(new Asar_Request(array(
        'headers' => array('Accept' => 'de')
      )))->getHeader('Content-Language')
    );
  }
  
  function testGetPath() {
    $cname = get_class($this) . '_GetPath';
    eval('
      class '. $cname . ' extends Asar_Resource {
        function GET() {
          return $this->getPath();
        }
      }
    ');
    $R = new $cname;
    $this->assertEquals(
      '/foo/bar/baz',
      $R->handleRequest(new Asar_Request(array(
        'path' => '/foo/bar/baz'
      )))->getContent()
    );
  }
  
  function testGetPermaPath() {
    $cname = get_class($this) . '_GetPermaPath_Resource_Baz_Bar_Foo';
    eval('
      class '. $cname . ' extends Asar_Resource {}
    ');
    $R = new $cname;
    $this->assertEquals('/baz/bar/foo', $R->getPermaPath());
  }
  
  function testForwardTo() {
    $cname = get_class($this) . '_Forwarding';
    eval('
      class '. $cname . ' extends Asar_Resource {
        function GET() {
          return $this->forwardTo("Some_Resource");
        }
      }
    ');
    $R = new $cname;
    $request = new Asar_Request(array('path'=>'/foo/bar'));
    try {
      $R->handleRequest($request);
    } catch (Asar_Resource_Exception_ForwardRequest $e) {
      $this->assertEquals('Some_Resource', $e->getMessage());
      $payload = $e->getPayload();
      $this->assertEquals($request, $payload['request']);
      return TRUE;
    }
    $this->fail('Did not throw Asar_Resource_Exception_ForwardRequest.');
  }
  
}
