<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

class Asar_ApplicationTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->resource = $this->getMock(
      'Asar_Resource_Interface', array('handleRequest')
    );
    $this->router = $this->getMock('Asar_Router_Interface', array('route'));
    $this->sm = $this->getMock(
      'Asar_Response_StatusMessages_Interface', array('getMessage')
    );
    $this->map = array('/foo_ra' => 'BarRa');
    $this->app = new Asar_Application(
      'Some_Name', $this->router, $this->sm, $this->map
    );
    $this->request = new Asar_Request;
    $this->response = new Asar_Response;
  }
  
  function testApplicationImplementsResourceInterface() {
    $this->assertType('Asar_Resource_Interface', $this->app);
  }

  function testAppRunsSetupMethodOnConstruction() {
    $old_post = $_POST;
    $cname = get_class($this) . '_SetUpTest_Application';
    eval('
      class '. $cname . ' extends Asar_Application {
        function setUp() {
          $_POST["baz"] = 2895;
        }
      }
    ');
    new $cname('Foo_Name', $this->router, $this->sm);
    $this->assertArrayHasKey('baz', $_POST);
    $this->assertEquals(2895, $_POST['baz']);
    $_POST = $old_post;
  }
  
  private function routerExpects() {
    return $this->router->expects($this->once())->method('route');
  }
  
  private function routerReturnsResource() {
    return $this->routerExpects()->will($this->returnValue($this->resource));
  }
  
  private function resourceExpects() {
    return $this->resource->expects($this->once())->method('handleRequest');
  }
  
  function testApplicationSendsApplicationNamePathAndMapToRouter() {
    $path = '/foo/bar';
    $this->request->setPath($path);
    $this->routerExpects()->with('Some_Name', $path, $this->app->getMap());
    $this->app->handleRequest($this->request);
  }
  
  function testAppPassesRequestToResourcePassedFromRouter() {
    $this->request->setPath('/foo');
    $this->routerReturnsResource();
    $this->resourceExpects()->with($this->request);
    $this->app->handleRequest($this->request);
  }
  
  function testHandleRequestUsesReturnValueFromResource() {
    $this->routerReturnsResource();
    $this->resourceExpects()->will($this->returnValue($this->response));
    $app_response = $this->app->handleRequest($this->request);
    $this->assertEquals($this->response, $app_response);
  }
  
  function testSends404ResponseWhenRouterThrowsNotFoundException() {
    $this->routerExpects()
      ->will($this->throwException(new Asar_Router_Exception_ResourceNotFound));
    $response = $this->app->handleRequest($this->request);
    $this->assertEquals(404, $response->getStatus());
  }
  
  function testSends500ResponseWhenResourceThrowsAGeneralException() {
    $this->routerReturnsResource();
    $this->resourceExpects()
      ->will($this->throwException(new Exception));
    $response = $this->app->handleRequest($this->request);
    $this->assertEquals(500, $response->getStatus());
  }
  
  function test500ResponseExceptionContent() {
    $this->routerReturnsResource();
    $this->resourceExpects()
      ->will($this->throwException(new Exception('Foo message')));
    $this->setStatusMessagesMock()->will($this->returnCallBack(array($this, 'cbSM')));
    $response = $this->app->handleRequest($this->request);
    $this->assertRegExp(
      '/\nLine: [0-9]+/', $response->getContent()
    );
    $this->assertContains(
      "Foo message\nFile: " . __FILE__, $response->getContent()
    );
  }
  
  function cbSM($response, $request) {
    return $response->getContent();
  }
  
  function setStatusMessagesMock() {
    return $this->sm->expects($this->once())->method('getMessage');
  }
  
  function testAppPassesResponseAndRequestToStatusMessageCreator() {
    $this->request->setPath('/foo/bar');
    $this->response->setStatus(405);
    $this->routerReturnsResource();
    $this->resourceExpects()->will($this->returnValue($this->response));
    // This is a hack
    $response_test = clone $this->response;
    $this->setStatusMessagesMock()->with($response_test, $this->request);
    $this->app->handleRequest($this->request);
  }
  
  function testAppUsesStatusMessageCreatorReturnAsReponseContent() {
    $this->routerReturnsResource();
    $this->resourceExpects()->will($this->returnValue($this->response));
    $this->setStatusMessagesMock()->will($this->returnValue('foo bar baz'));
    $this->assertContains(
      'foo bar baz', $this->app->handleRequest($this->request)->getContent()
    );
  }
  
  function testAppReturnsPlainResponseWhenStatusMessageReturnsFalse() {
    $this->response->setContent('foo');
    $this->routerReturnsResource();
    $this->resourceExpects()->will($this->returnValue($this->response));
    $this->setStatusMessagesMock()->will($this->returnValue(false));
    $this->assertContains(
      'foo', $this->app->handleRequest($this->request)->getContent()
    );
  }
  
  function testAppUsesPassedMapping() {
    $map = $this->app->getMap();
    $this->assertArrayHasKey('/foo_ra', $map);
    $this->assertEquals('BarRa', $map['/foo_ra']);
  }
  
  function testAppSetMapping() {
    $app_name = get_class($this) . '_MappingTest';
    $cname = $app_name . '_Application';
    $resource1 = $app_name . '_Foo_Resource';
    $resource2 = $app_name . '_BarResource';
    eval("
      class $cname extends Asar_Application {
        function setUp() {
          \$this->setIndex('$resource1');
          \$this->setMap('/bar', '$resource2');
        }
      }
    ");
    $app = new $cname($app_name, $this->router, $this->sm);
    $map = $app->getMap();
    $this->assertType('array', $map);
    $this->assertEquals($map['/'], $resource1);
    $this->assertEquals($map['/bar'], $resource2);
  }
  
  function testAppOverridesMapping() {
    $app_name = get_class($this) . '_MappingTest2';
    $cname = $app_name . '_Application';
    $resource = $app_name . '_Foo_Resource';
    eval("
      class $cname extends Asar_Application {
        function setUp() {
          \$this->setMap('/foo_ra', '$resource');
        }
      }
    ");
    $app = new $cname($app_name, $this->router, $this->sm);
    $map = $app->getMap();
    $this->assertEquals($map['/foo_ra'], $resource);
  }
  
  function testAppSetsDefaultHeaders() {
    $this->routerReturnsResource();
    $this->resourceExpects()->will($this->returnValue($this->response));
    $app_response = $this->app->handleRequest($this->request);
    $this->assertEquals('text/html', $app_response->getHeader('Content-Type'));
    $this->assertEquals('en', $app_response->getHeader('Content-Language'));
  }
  
  function testAppSkipsSettingDefaultHeadersWhenTheyAreDefined() {
    $this->response->setHeader('Content-Type', 'text/plain');
    $this->response->setHeader('Content-Language', 'tl');
    $this->routerReturnsResource();
    $this->resourceExpects()->will($this->returnValue($this->response));
    $app_response = $this->app->handleRequest($this->request);
    $this->assertEquals('text/plain', $app_response->getHeader('Content-Type'));
    $this->assertEquals('tl', $app_response->getHeader('Content-Language'));
  }
  
  function testAppForwardsRequestToAnotherResource() {
    $this->markTestIncomplete();
  }

}
