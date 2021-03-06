<?php
namespace Asar\Tests\Unit;

require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\Application;
use \Asar\Request;
use \Asar\Response;
use \Asar\Router\Exception\ResourceNotFound;
use \Asar\Resource\Exception\ForwardRequest;
use \Asar\Debug;

class ApplicationTest extends \Asar\Tests\TestCase {
  
  function setUp() {
    $this->resource = $this->getMock(
      'Asar\Resource\ResourceInterface', array('handleRequest')
    );
    $this->router = $this->getMock(
      'Asar\Router\RouterInterface', array('route')
    );
    $this->sm = $this->getMock(
      'Asar\Response\StatusMessages\StatusMessagesInterface',
      array('getMessage')
    );
    $this->map = array('/foo_ra' => 'BarRa');
    $this->app = new Application(
      'Some_Name', $this->router, $this->sm, $this->map
    );
    $this->request = new Request;
    $this->response = new Response;
  }
  
  function testApplicationImplementsResourceInterface() {
    $this->assertInstanceOf('Asar\Resource\ResourceInterface', $this->app);
  }

  function testAppRunsSetupMethodOnConstruction() {
    $old_post = $_POST;
    /**
     * convert to underscores
     */
    $cname =$this->generateAppName('_SetUpTest_Application');
    eval('
      class '. $cname . ' extends \Asar\Application {
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
  
  private function routerExpects($at = 0) {
    return $this->router->expects($this->at($at))->method('route');
  }
  
  private function routerReturnsResource($at = 0, $resource = null) {
    if (!$resource) {
      $resource = $this->resource;
    }
    return $this->routerExpects($at)->will($this->returnValue($resource));
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
      ->will($this->throwException(new ResourceNotFound));
    $response = $this->app->handleRequest($this->request);
    $this->assertEquals(404, $response->getStatus());
  }
  
  function testSends500ResponseWhenResourceThrowsAGeneralException() {
    $this->routerReturnsResource();
    $this->resourceExpects()
      ->will($this->throwException(new \Exception));
    try {
      $response = $this->app->handleRequest($this->request);
    } catch (\Exception $e) {
      $this->fail('Application was unable to catch exception.');
    }
    $this->assertEquals(500, $response->getStatus());
  }
  
  function test500ResponseExceptionContent() {
    $this->routerReturnsResource();
    $this->resourceExpects()
      ->will($this->throwException(new \Exception('Foo message')));
    $this->setStatusMessagesMock()->will(
      $this->returnCallBack(array($this, 'cbSM'))
    );
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
    $app_name = $this->generateAppName('_MappingTest');
    $cname = $app_name . '_Application';
    $resource1 = $app_name . '_Foo_Resource';
    $resource2 = $app_name . '_BarResource';
    eval("
      class $cname extends \Asar\Application {
        function setUp() {
          \$this->setIndex('$resource1');
          \$this->setMap('/bar', '$resource2');
        }
      }
    ");
    $app = new $cname($app_name, $this->router, $this->sm);
    $map = $app->getMap();
    $this->assertInternalType('array', $map);
    $this->assertEquals($map['/'], $resource1);
    $this->assertEquals($map['/bar'], $resource2);
  }
  
  function testAppOverridesMapping() {
    $app_name = $this->generateAppName('_MappingTest2');
    $cname = $app_name . '_Application';
    $resource = $app_name . '_Foo_Resource';
    eval("
      class $cname extends \Asar\Application {
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
  
  function testForwardingRequestToAnotherResource() {
    $this->request->setPath('/bar');
    $e = new ForwardRequest('Foo');
    $e->setPayload(array('request' => $this->request));
    $this->resource->expects($this->once())
      ->method('handleRequest')
      ->will($this->throwException($e));
    $final_resource = $this->getMock('Asar\Resource\ResourceInterface');
    $final_resource->expects($this->once())
      ->method('handleRequest');
    $this->routerReturnsResource();
    $this->routerReturnsResource(1, $final_resource);
    $this->app->handleRequest($this->request);
  }
  
  function testForwardingUsesResponseFromFinalResource() {
    $this->response->setContent('Foo!');
    $e = new ForwardRequest('Foo');
    $e->setPayload(array('request' => $this->request));
    $this->resource->expects($this->once())
      ->method('handleRequest')
      ->will($this->throwException($e));
    $final_resource = $this->getMock('Asar\Resource\ResourceInterface');
    $final_resource->expects($this->once())
      ->method('handleRequest')
      ->will($this->returnValue($this->response));
    $this->routerReturnsResource();
    $this->routerReturnsResource(1, $final_resource);
    $this->assertEquals(
      'Foo!', $this->app->handleRequest($this->request)->getContent()
    );
  }
  
  function testForwardingSetsPathAsFileName() {
    $this->response->setContent('Foo!');
    $e = new ForwardRequest('Foo');
    $e->setPayload(array('request' => $this->request));
    $this->resource->expects($this->once())
      ->method('handleRequest')
      ->will($this->throwException($e));   
    $this->routerReturnsResource();
    $this->routerExpects(1)
      ->with('Some_Name', 'Foo', $this->map);
    $this->app->handleRequest($this->request);
  }
  
  function testForwardingChecksForRecursion() {
    $this->request->setPath('/foo/bar');
    $e = new ForwardRequest('Foo');
    $e->setPayload(array('request' => $this->request));
    $this->resource->expects($this->any())
      ->method('handleRequest')
      ->will($this->throwException($e));    
    $this->router->expects($this->any())
      ->method('route')
      ->will($this->returnValue($this->resource));
    $response = $this->app->handleRequest($this->request);
    $this->assertEquals(500, $response->getStatus());
    $this->assertContains(
      "Maximum forwards reached for path '/foo/bar'.", $response->getContent()
    );
  }
  
  function testForwardingPassesRequestFromExceptionPayload() {
    $request = new Request(array('content' => "bar"));
    $received_request = clone($request);
    $received_request->setHeader(
      'Asar-Internal-Isforwarded', true
    );
    $e = new ForwardRequest('Foo');
    $e->setPayload(array('request' => $request));
    $this->resource->expects($this->once())
      ->method('handleRequest')
      ->will($this->throwException($e));    
    $final_resource = $this->getMock('Asar\Resource\ResourceInterface');
    $final_resource->expects($this->once())
      ->method('handleRequest')
      ->with($received_request);
    $this->routerReturnsResource();
    $this->routerReturnsResource(1, $final_resource);
    $this->app->handleRequest($this->request);
  }
  
  function testRedirectionPassesPathToRouterWhenLocationIsNotPath() {
    $this->response->setStatus(302);
    $this->response->setHeader('Location', 'A_Resource_Name');
    $this->routerReturnsResource();
    $this->resourceExpects()->will($this->returnValue($this->response));
    $this->routerExpects(1)->with('Some_Name', 'A_Resource_Name', $this->map);
    $this->app->handleRequest($this->request);
  }
  
  function testRedirectionDoesNotPassPathToRouterWhenLocationIsAPath() {
    $this->response->setStatus(301);
    $this->response->setHeader('Location', '/a/proper/path');
    $this->resourceExpects()->will($this->returnValue($this->response));
    $this->router->expects($this->once())
      ->method('route')
      ->will($this->returnValue($this->resource));
    $app_response = $this->app->handleRequest($this->request);
    echo $app_response->getContent();
    $this->assertEquals(301, $app_response->getStatus());
    $this->assertEquals('/a/proper/path', $app_response->getHeader('Location'));
  }
  
  function testRedirectUsesPermaPathFromReturnedResponseAsNewLocation() {
    $this->response->setStatus(307);
    $this->response->setHeader('Location', 'A_Resource_Name');
    $this->routerReturnsResource();
    $this->resourceExpects()->will($this->returnValue($this->response));
    $destination_resource = $this->getMock('Asar\Resource');
    $destination_resource->expects($this->once())
      ->method('getPermaPath')
      ->will($this->returnValue('/foo/bar'));
    $this->routerExpects(1)->will($this->returnValue($destination_resource));
    $app_response = $this->app->handleRequest($this->request);
    $this->assertEquals(
      307, $app_response->getStatus(), $app_response->getContent()
    );
    $this->assertEquals('/foo/bar', $app_response->getHeader('Location'));
  }
  
  function testRequestFiltersFilteringRequestInSequence() {
    $request = new Request(array('content' => 0));
    for ($i = 0, $filters = array(); $i < 3; $i++) {
      $filter = $this->getMock('Asar\RequestFilter\RequestFilterInterface');
      $filter->expects($this->once())
        ->method('filterRequest')
        ->with(new Request(array('content' => $i)))
        ->will($this->returnValue(
          new Request(array('content' => $i + 1))
        ));
      $request_filters[] = $filter;
    }
    $app = new Application(
      'Some_Name', $this->router, $this->sm, $this->map, $request_filters
    );
    $app->handleRequest($request);
  }
  
  function testUseRequestFromRequestFilter() {
    $request = new Request(array('content' => 0));
    $request2 = new Request(array('content' => 2));
    $filter = $this->getMock('Asar\RequestFilter\RequestFilterInterface');
    $filter->expects($this->once())
      ->method('filterRequest')
      ->with($request)
      ->will($this->returnValue($request2));
    $this->routerReturnsResource();
    $this->resourceExpects()
      ->with($request2);
    $app = new Application(
      'Some_Name', $this->router, $this->sm, $this->map, array($filter)
    );
    $app->handleRequest($request);
  }
  
  function testResponseFiltersFilteringResponse() {
    $request = new Request;
    $this->routerReturnsResource();
    $this->resourceExpects()
      ->will($this->returnValue(new Response(array(
        'content' => 0
      ))));
    for ($i = 0, $filters = array(); $i < 3; $i++) {
      $filter = $this->getMock(
        'Asar\ResponseFilter\ResponseFilterInterface'
      );
      $filter->expects($this->once())
        ->method('filterResponse')
        ->with(new Response(array('content' => $i)))
        ->will($this->returnValue(
          new Response(array('content' => $i + 1))
        ));
      $response_filters[] = $filter;
    }
    $app = new Application(
      'Some_Name', $this->router, $this->sm, $this->map, array(), $response_filters
    );
    $app->handleRequest($request);
  }
  
  function testSettingApplicationNameInInternalHeader() {
    $request = new Request;
    $debug = new Debug;
    $filter = $this->getMock(
      'Asar\RequestFilter\RequestFilterInterface'
    );
    $request->setHeader('Asar-Internal-Debug', $debug);
    $filter->expects($this->once())
      ->method('filterRequest')
      ->with($request)
      ->will($this->returnValue($request));
    $this->routerReturnsResource();
    $app = new Application(
      'Some_Name', $this->router, $this->sm, $this->map, array($filter)
    );
    $app->handleRequest($request);
    $this->assertEquals('Some_Name', $debug->get('Application'));
  }

}
