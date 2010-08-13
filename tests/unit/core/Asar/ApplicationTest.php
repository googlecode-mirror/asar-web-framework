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
  }
  
  function testApplicationImplementsResourceInterface() {
    $this->assertType('Asar_Resource_Interface', $this->app);
  }
  
  function testApplicationGetName() {
    $this->assertEquals('Some_Name', $this->app->getName());
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
    $request = new Asar_Request(array('path'=>$path));
    $this->routerExpects()->with('Some_Name', $path, $this->app->getMap());
    $this->app->handleRequest($request);
  }
  
  function testAppPassesRequestToResourcePassedFromRouter() {
    $request = new Asar_Request(array('path' => '/foo'));
    $this->routerReturnsResource();
    $this->resourceExpects()->with($request);
    $this->app->handleRequest($request);
  }
  
  function testHandleRequestUsesReturnValueFromResource() {
    $request = new Asar_Request;
    $response = new Asar_Response;
    $this->routerReturnsResource();
    $this->resourceExpects()->will($this->returnValue($response));
    $app_response = $this->app->handleRequest($request);
    $this->assertEquals($response, $app_response);
  }
  
  function testSends404ResponseWhenRouterThrowsNotFoundException() {
    $this->routerExpects()
      ->will($this->throwException(new Asar_Router_Exception_ResourceNotFound));
    $response = $this->app->handleRequest(new Asar_Request);
    $this->assertEquals(404, $response->getStatus());
  }
  
  function testSends500ResponseWhenResourceThrowsAGeneralException() {
    $this->routerReturnsResource();
    $this->resourceExpects()
      ->will($this->throwException(new Exception));
    $response = $this->app->handleRequest(new Asar_Request);
    $this->assertEquals(500, $response->getStatus());
  }
  
  function test500ResponseExceptionContent() {
    $this->routerReturnsResource();
    $this->resourceExpects()
      ->will($this->throwException(new Exception('Foo message')));
    $this->setStatusMessagesMock()->will($this->returnCallBack(array($this, 'cbSM')));
    $response = $this->app->handleRequest(new Asar_Request);
    $this->assertRegExp(
      '/\nLine: [0-9]+/', $response->getContent()
    );
    $this->assertContains(
      "Foo message\nFile: " . __FILE__, $response->getContent()
    );
  }
  
  public function cbSM($response, $request) {
    return $response->getContent();
  }
  
  function setStatusMessagesMock() {
    return $this->sm->expects($this->once())->method('getMessage');
  }
  
  function testAppPassesResponseAndRequestToStatusMessageCreator() {
    $request = new Asar_Request(array('path' => '/foo/bar'));
    $response = new Asar_Response(array('status' => 405));
    $this->routerReturnsResource();
    $this->resourceExpects()->will($this->returnValue($response));
    $this->setStatusMessagesMock()->with($response, $request);
    $this->app->handleRequest($request);
  }
  
  function testAppUsesStatusMessageCreatorReturnAsReponseContent() {
    $request = new Asar_Request;
    $response = new Asar_Response;
    $this->routerReturnsResource();
    $this->resourceExpects()->will($this->returnValue($response));
    $this->setStatusMessagesMock()->will($this->returnValue('foo bar baz'));
    $this->assertContains(
      'foo bar baz', $this->app->handleRequest($request)->getContent()
    );
  }
  
  function testAppReturnsPlainResponseWhenStatusMessageReturnsFalse() {
    $request = new Asar_Request;
    $response = new Asar_Response(array('content' => 'foo'));
    $this->routerReturnsResource();
    $this->resourceExpects()->will($this->returnValue($response));
    $this->setStatusMessagesMock()->will($this->returnValue(false));
    $this->assertContains(
      'foo', $this->app->handleRequest($request)->getContent()
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

}
