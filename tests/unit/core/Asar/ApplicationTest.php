<?php
require_once realpath(dirname(__FILE__). '/../../config.php');
require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Asar_ApplicationTest extends Asar_Test_Helper {

  function setUp() {
    $this->router = $this->getMock('Asar_Resource_Router');
    $this->app = new Asar_Application($this->router);
  }
  
  function testApplicationImplementsAsarRequestableInterface() {
    $this->assertTrue(
      $this->app instanceof Asar_Requestable,
      'App object does not implement Asar_Requestable interface.'
    );
  }
  
  function testHandleRequestShouldAlwaysReturnAResponseObject() {
    $this->assertTrue(
      $this->app->handleRequest(new Asar_Request) instanceof Asar_Response,
      'The app object did not return with an Asar_Response object'
    );
  }
  
  function testApplicationShouldRunInitializationMethodOnConstruction() {
    $app = $this->getMock('Asar_Application', array('setUp'), array(), '', false);
    $app->expects($this->once())
      ->method('setUp');
    $app->__construct($this->router);
  }
  
  function testGetMap() {
    $this->app->setMap('/foo', 'Bar');
    $this->assertEquals('Bar', $this->app->getMap('/foo'));
  }
  
  function testSetAnIndex() {
    $index = $this->getMock('Asar_Requestable', array());
    $this->app->setIndex($index);
    $this->assertSame($index, $this->app->getMap('/'));
  }
  
  function testSettingRouterObject() {
    $router = new Asar_Resource_Router;
    $this->app->setRouter($router);
    $this->assertSame(
      $router, $this->readAttribute($this->app, 'router')
    );
  }
  
  function testHandleRequestPassesRoutingToRouterIfNonIsMapped() {
    $request = new Asar_Request;
    $path = '/a/path/not/known';
    $request->setPath($path);
    $router = $this->getMock('Asar_Resource_Router', array('getRoute'));
    $router->expects($this->once())
      ->method('getRoute')
      ->with($this->equalTo($this->app), $this->equalTo($path));
    $this->app->setRouter($router);
    $this->app->handleRequest($request);
  }
  
  function testHandleRequestDoesNotPassRoutingToRouterIfItIsMapped() {
    $request = new Asar_Request;
    $path = '/mapped/path';
    $request->setPath($path);
    $router = $this->getMock('Asar_Resource_Router', array('getRoute'));
    $router->expects($this->never())
      ->method('getRoute');
    $this->app->setMap($path, $this->getMock('Asar_Requestable'));
    $this->app->setRouter($router);
    $this->app->handleRequest($request);
  }
  
  function testHandleRequestUsesReturnValueFromRouter() {
    $request = new Asar_Request;
    $path = '/mapped/path';
    $request->setPath($path);
    eval ('
      class Asar_ApplicationTest_Route_Resource extends Asar_Resource {
        function handleRequest(Asar_Request_Interface $request) {
          $response = new Asar_Response;
          $response->setContent("watatatata");
          return $response;
        }
      }
    ');
    $router = $this->getMock('Asar_Resource_Router', array('getRoute'));
    $router->expects($this->once())
      ->method('getRoute')
      ->will($this->returnValue('Asar_ApplicationTest_Route_Resource'));
    $this->app->setRouter($router);
    $this->assertEquals(
      'watatatata', $this->app->handleRequest($request)->getContent()
    );
  }
  
  function testApplicationShouldPassRequestToResource() {
    $request = new Asar_Request;
    $resource = $this->getMock('Asar_Resource', array('handleRequest'));
    $resource->expects($this->once())
      ->method('handleRequest')
      ->with($request);
    $this->app->setIndex($resource);
    $this->app->handleRequest($request);
  }
  
  function testApplicationShouldReturnResponseFromResource() {
    $request = new Asar_Request;
    $resource = $this->getMock('Asar_Resource', array('handleRequest'));
    $expected_response = new Asar_Response;
    $resource->expects($this->any())
      ->method('handleRequest')
      ->will($this->returnValue($expected_response));
    $this->app->setIndex($resource);
    $response = $this->app->handleRequest($request);
    $this->assertSame(
      $expected_response, $response,
      'Application did not return the response from Resource'
    );
  }
  /*
  function testSetMapInstantiatesRequestableObjectFromString() {
    eval('
      class Asar_ApplicationTest_App5 extends Asar_Application {
        protected function setUp() {
          $this->setAppPrefix("Asar_ApplicationTest_App5");
          $this->setMap("/rest", "Rest");
        }
      }
      
      class Asar_ApplicationTest_App5_Resource_Rest extends Asar_Resource {}
    ');
    $app = new Asar_ApplicationTest_App5;
    $this->assertTrue(
       $app->getMap('/rest') instanceof Asar_ApplicationTest_App5_Resource_Rest,
      'setMap() was not able to set resource mapping.'
    );
  }
  
  function testSetMapAttemptsToInstantiateRequestableDirectlyFromString() {
    eval('
      class Asar_ApplicationTest_A_Different_Resource extends Asar_Resource {}
    ');
    $this->app->setMap("/wee", "Asar_ApplicationTest_A_Different_Resource");
    $this->assertTrue(
       $this->app->getMap('/wee') instanceof Asar_ApplicationTest_A_Different_Resource,
      'setMap() was not able to set resource mapping directly.'
    );
  }
  
  function testSetMapAttemptsToMapResourceDirectly() {
    $resource = $this->getMock('Asar_Resource');
    $this->app->setMap('/waa', $resource);
    $this->assertSame(
       $resource, $this->app->getMap('/waa'),
      'setMap() was not able to set resource mapping directly.'
    );
  }
  
  function testAppShouldPassRequestToMappedResourceProperly() {
    $index = $this->getMock('Asar_Requestable', array('handleRequest'));
    $page  = $this->getMock('Asar_Requestable', array('handleRequest'));
    $request = new Asar_Request;
    $request->setPath('/page');
    $index->expects($this->never())
      ->method('handleRequest');
    $page->expects($this->once())
      ->method('handleRequest')
      ->with($request);
    $this->app->setIndex($index);
    $this->app->setMap('/page', $page);
    $this->app->handleRequest($request);
  }
  
  function testShouldReturn404ResponseWhenRequestPathIsUnknown() {
    $request = new Asar_Request;
    $request->setPath('/a-path-yet-to-be-known');
    $response = $this->app->handleRequest($request);
    $this->assertEquals(
      404, $response->getStatus(),
      'Application did not return 404 status when resource is unknown'
    );
  }
  
  function testShouldSetDefaultMessageForRequestPathIsUnknown() {
    $request = new Asar_Request;
    $request->setPath('/unknown/path');
    $response = $this->app->handleRequest($request);
    $this->assertContains(
      'File Not Found (404)', $response->getContent(),
      'Application response did not say what type of error it was for 404'
    );
    $this->assertContains(
      'Sorry, we were unable to find the resource you were looking for. '.
      'Please check that you got the address or URL correctly. If '.
      'that is the case, please email the administrator. Thank you '.
      'and please forgive the inconvenience.',
      $response->getContent(),
      'Application did not return a proper 404 message'
    );
  }
  
  function testShouldSetDefaultMessageFor405ResponseStatus($method = 'POST') {
    $request = new Asar_Request;
    $request->setMethod($method);
    $response = new Asar_Response;
    $response->setStatus(405);
    $index = $this->getMock('Asar_Requestable', array('handleRequest'));
    $index->expects($this->once())
      ->method('handleRequest')
      ->will($this->returnValue($response));
    $this->app->setIndex($index);
    $r = $this->app->handleRequest($request);
    $this->assertEquals(
      405, $r->getStatus(),
      'Status should be 405'
    );
    $this->assertContains(
      'Method Not Allowed (405)', $r->getContent(),
      'Application response did not say what type of error it was for 405'
      );
      $this->assertContains(
        "The HTTP Method '$method' is not allowed for this resource.",
          $r->getContent(),
          'Application did not return a proper 405 message'
      );
  }
  
  function testShouldSetAppropriate405Messages() {
    $methods = array('GET', 'PUT', 'DELETE');
    foreach ($methods as $method) {
      $this->testShouldSetDefaultMessageFor405ResponseStatus($method);
    }
  }
  
  function testShouldSetDefaultMessageFor406ResponseStatus() {
    $request = new Asar_Request;
    $response = new Asar_Response;
    $response->setStatus(406);
    $index = $this->getMock('Asar_Requestable', array('handleRequest'));
    $index->expects($this->once())
      ->method('handleRequest')
      ->will($this->returnValue($response));
    $this->app->setIndex($index);
    $r = $this->app->handleRequest($request);
    $this->assertEquals( 406, $r->getStatus() );
    $this->assertContains(
      'Not Acceptable (406)', $r->getContent()
      );
      $this->assertContains(
        'An appropriate representation of the requested ' .
        'resource could not be found.',
        $r->getContent()
      );
  }
  
  function testShouldSetDefaultMessageFor500ResponseStatus() {
    $request = new Asar_Request;
    $response = new Asar_Response;
    $response->setStatus(500);
    $index = $this->getMock('Asar_Requestable', array('handleRequest'));
    $index->expects($this->once())
      ->method('handleRequest')
      ->will($this->returnValue($response));
    $this->app->setIndex($index);
    $r = $this->app->handleRequest($request);
    $this->assertEquals(
      500, $r->getStatus(),
      'Status should be 500'
    );
    $this->assertContains(
      'Internal Server Error (500)', $r->getContent(),
      'Application response did not say what type of error it was for 500'
    );
    $this->assertContains(
      'The Server has encountered some problems.', $r->getContent(),
      'Application did not return a proper 500 message.'
    );
  }
  
  function testResourceRespondsContentWithStatus500ApplicationAttemptsToRenderIt() {
    $request = new Asar_Request;
    $response = new Asar_Response;
    $response->setStatus(500);
    $response->setContent('The error message from resource.');
    $index = $this->getMock('Asar_Requestable', array('handleRequest'));
    $index->expects($this->once())
      ->method('handleRequest')
      ->will($this->returnValue($response));
    $this->app->setIndex($index);
    $r = $this->app->handleRequest($request);
    $this->assertEquals(
      500, $r->getStatus(),
      'Status should be 500'
    );
    $this->assertContains(
      'The resource returned: The error message from resource.', $r->getContent(),
        'Application response include error message from resource.'
    );
  }
  
  function testApplicationSetsDefaultRepresentationDirectory() {
    // 'Representation' roughly translates to 'View' in MVC
    // or 'templates' in template-based strategies.
    // We test that the application sets the Representation directory
    // relative to the file where the application was defined.
    eval('
      class Asar_ApplicationTest_App9 extends Asar_Application {
        protected function setUp() {
          $this->config["default_representation_dir"] = "/Foo/Bar";
        }
      }
    ');
    
    $index = $this->getMock('Asar_Requestable', 
      array('handleRequest', 'setConfiguration')
    );
    
    $app = new Asar_ApplicationTest_App9;
    $index->expects($this->once())
      ->method('setConfiguration')
      ->will($this->returnCallback(array($this, '_dummySetConfiguration')));
    $app->setIndex($index);
    $app->handleRequest(new Asar_Request);
    $config = self::getObject('config');
    $this->assertEquals(
      '/Foo/Bar',
      $config['default_representation_dir'],
      'Unable to set the representation directory relative to Application'
    );
  }
  
  function _dummySetConfiguration($config) {
    self::saveObject('config', $config);
  }
  
  function testApplicationSetsContextForResource() {
    $index = $this->getMock('Asar_Requestable', 
      array('handleRequest', 'setConfiguration'), array()
    );
    $index->expects($this->once())
      ->method('setConfiguration')
      ->will($this->returnCallback(array($this, '_dummySetConfiguration')));
    $this->app->setIndex($index);
    $this->app->handleRequest(new Asar_Request);
    $config = self::getObject('config');
    $this->assertSame(
      $this->app, $config['context'],
      'Unable to set the context of the resource on Application'
    );
  }
  
  function testApplicationMarksResolvedResourceWhenInDebugMode() {
    $index = $this->getMock('Asar_Requestable');
    $this->app->setIndex($index);
    Asar::setMode(Asar::MODE_DEBUG);
    $this->app->handleRequest(new Asar_Request);
    $debug = Asar::getDebugMessages();
    $this->assertEquals(get_class($index), $debug['Resource']);
    Asar::setMode(Asar::MODE_DEVELOPMENT);
    Asar::clearDebugMessages();
  }*/
  
} //388
