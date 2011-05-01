<?php

namespace Asar\Tests\Unit;

require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\Resource;
use \Asar\Request;

class ResourceTest extends \Asar\Tests\TestCase {
  
  private static $inc = 0;
  
  function setUp() {
    $this->old_post_data = $_POST;
    $this->R = new Resource;
  }
  
  function tearDown() {
    $_POST = $this->old_post_data;
  }
  
  protected function createResourceClassDefinition(
    $rname, $get_body = '', $setup_body = ''
  ) {
    $cname = $this->generateAppNameNew('\\' . $rname);
    $body = "
        function setUp() {
          $setup_body
        }
        
        function GET() {
          $get_body
        }
    ";
    $this->createClassDefinition($cname, '\Asar\Resource', $body);
    return $cname;
  }
  
  function testResourceImplementsResourceInterface() {
    $this->assertInstanceOf('Asar\Resource\ResourceInterface', $this->R);
  }
  
  function testResourceImplementsConfigInterface() {
    $this->assertInstanceOf('Asar\Config\ConfigInterface', $this->R);
    $this->config = $this->getMock('Asar\Config\ConfigInterface');
    $this->config->expects($this->once())
      ->method('getConfig')
      ->will($this->returnValue(array('foo' => 'bar')));
    $this->R->importConfig($this->config);
    $this->assertEquals('bar', $this->R->getConfig('foo'));
  }
  
  function testResourceReturnsAResponse() {
    $this->assertInstanceOf(
      'Asar\Response', $this->R->handleRequest(new Request)
    );
  }
  
  function testShouldBeAbleToSetRequestAttribute() {
    $this->request = new Request;
    $this->R = $this->getMock('Asar\Resource', array('GET'));
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
    $request = new Request;
    $request->setMethod($method);
    $this->R = $this->getMock('Asar\Resource', array($method));
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
    $request = new Request($req_opts);
    $request->setMethod($method);
    $R = $this->getMock('Asar\Resource', array($method));
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
    $this->assertInternalType('array', $_POST);
  }
  
  function testResourceWithoutDefinedHttpMethodShouldReturn405HttpStatus() {
    $R = $this->getMock('Asar\Resource', array('some_method'));
    $request = new Request;
    foreach (array('GET', 'POST', 'PUT', 'DELETE') as $method) {
      $request->setMethod($method);
      $response = $R->handleRequest($request);
      $this->assertEquals(405, $response->getStatus());
    }
  }
  
  function testResourceShouldSendResponse500StatusWhenMethodRaisesException() {
    $R = $this->getMock('Asar\Resource', array('GET'));
    $R->expects($this->once())
      ->method('GET')
      ->will($this->returnCallBack(array($this, 'checkRaisingException')));
    $response = $R->handleRequest(new Request);
    $this->assertEquals(
      500, $response->getStatus(),
      'The HTTP Response Status is not 500 for method throwing an Exception'
    );
    $this->assertEquals(
      'My Exception Message.', $response->getContent()
    );
  }
  
  function checkRaisingException() {
    throw new \Exception('My Exception Message.');
  }
  
  function testResourceRunsSetupMethod() {
    $cname = $this->createResourceClassDefinition(
      'ResourceRunSetup', '', '$_POST["foo"] = "bar";'
    );
    $R = new $cname;
    $this->assertTrue(array_key_exists('foo', $_POST));
    $this->assertEquals('bar', $_POST['foo']);
  }
  
  function testModifyConfigUseTemplates() {
    $cname = $this->createResourceClassDefinition(
      'DefaultUseTemplatesConfiguration', '',
      '$this->config["use_templates"] = false;'
    );
    $R = new $cname;
    $this->assertFalse($R->getConfig('use_templates'));
  }
  
  function testModifyConfigValue() {
    $cname = $this->createResourceClassDefinition(
      'ChangeConfigValues', '$this->setConfig("foo", "bar");'
    );
    $R = new $cname;
    $R->GET();
    $this->assertEquals( "bar", $R->getConfig('foo'));
  }
  
  function testGetConfigOnUnknownConfigReturnsNull() {
    $this->assertSame(NULL, $this->R->getConfig('foo'));
  }
  
  function testDefineOwnDefaultContentType() {
    $cname = $this->createResourceClassDefinition(
      'DefaultCTypeConfiguration', '',
      '$this->config["default_content_type"] = "text/plain";'
    );
    $R = new $cname;
    $this->assertEquals('text/plain', $R->getConfig('default_content_type'));
  }
  
  function testReturnResponsetContentTypeFromConfig() {
    $cname = $this->createResourceClassDefinition(
      'DefaultCTypeConfiguration2', '',
      '$this->config["default_content_type"] = "application/xml";'
    );
    $R = new $cname;
    $this->assertEquals(
      'application/xml', 
      $R->handleRequest(new Request(array(
        'headers' => array('Accept' => 'application/xml')
      )))->getHeader('Content-Type')
    );
  }
  
  function testDefineOwnDefaultLanguage() {
    $cname = $cname = $this->createResourceClassDefinition(
      'DefaultLangConfiguration', '',
      '$this->config["default_language"] = "tl";'
    );
    $R = new $cname;
    $this->assertEquals('tl', $R->getConfig('default_language'));
  }
  
  function testReturnResponsetContentLanguageFromConfig() {
    $cname = $this->createResourceClassDefinition(
      'DefaultLangConfiguration2', '', 
      '$this->config["default_language"] = "de";'
    );
    $R = new $cname;
    $this->assertEquals(
      'de', 
      $R->handleRequest(new Request(array(
        'headers' => array('Accept' => 'de')
      )))->getHeader('Content-Language')
    );
  }
  
  function testGetPath() {
    $cname = $this->createResourceClassDefinition(
      'GetPath', 'return $this->getPath();'
    );
    $R = new $cname;
    $this->assertEquals(
      '/foo/bar/baz',
      $R->handleRequest(new Request(array(
        'path' => '/foo/bar/baz'
      )))->getContent()
    );
  }
  
  function testGetPermaPath() {
    $cname = $this->createResourceClassDefinition(
      'GetPermaPath\Resource\Baz\Bar\Foo'
    );
    $R = new $cname;
    $this->assertEquals('/baz/bar/foo', $R->getPermaPath());
  }
  
  function testGetPermaPath2() {
    $cname = $this->createResourceClassDefinition(
      'GetPermaPath\Resource\RtBaz\RtBar\Foo'
    );
    $R = new $cname;
    $this->assertEquals(
      '/2009/yo/foo', $R->getPermaPath(array('baz' => 2009, 'bar' => 'yo'))
    );
  }
  
  function testForwardTo() {
    $cname = $this->createResourceClassDefinition(
      'Forwarding', 'return $this->forwardTo("Some\Resource");'
    );
    $R = new $cname;
    $request = new Request(array('path'=>'/foo/bar'));
    try {
      $R->handleRequest($request);
    } catch (\Asar\Resource\Exception\ForwardRequest $e) {
      $this->assertEquals('Some\Resource', $e->getMessage());
      $payload = $e->getPayload();
      $this->assertEquals($request, $payload['request']);
      return TRUE;
    }
    $this->fail('Did not throw Asar\Resource\Exception\ForwardRequest.');
  }
  
  /**
   * @dataProvider dataPathComponents
   */
  function testPathComponents($rname, $expected, $path) {
    $cname = $this->generateAppNameNew('\Resource\\' . $rname);
    $body = '
      function GET() {
        return $this->getPathComponents();
      }
    ';
    $this->createClassDefinition($cname, '\Asar\Resource', $body);
    $R = new $cname;
    $this->assertSame(
      $expected,
      $R->handleRequest(
        new Request(array('path' => $path))
      )->getContent()
    );
  }
  
  function dataPathComponents() {
    return array(
      array(
        'PathComponents\One\Two',
        array(
          'path-components' => 'path-components',
          'one' => 'one',
          'two' => 'two'
        ),
        '/path-components/one/two'
      ),
      array(
        'PathComponents\RtYear\RtMonth\Edit',
        array(
          'path-components' => 'path-components',
          'year' => '2010',
          'month' => '08',
          'edit'  => 'edit'
        ),
        '/path-components/2010/08/edit'
      ),
    );
  }
  
  function testQualifyReturnsTrueByDefault() {
    $this->assertTrue($this->R->qualify(array()));
  }
  
  private function mockResourceExpectsQualify($cname = '') {
    $this->R = $this->getMock(
      'Asar\Resource', array('qualify'), array(), $cname
    );
    return $this->R->expects($this->once())->method('qualify');
  }
  
  function testRunQualifyWhenHandlingRequest() {
    $this->mockResourceExpectsQualify()
      ->will($this->returnValue(TRUE));
    $this->R->handleRequest(new Request);
  }
  
  function testReturns404ResponseWhenQualifyReturnsFalse() {
    $this->mockResourceExpectsQualify()
      ->will($this->returnValue(FALSE));
    $response = $this->R->handleRequest(new Request);
    $this->assertEquals(404, $response->getStatus());
  }
  
  function testPassesQualifyWithValueFromPathComponents() {
    $rname = 'QualifyTest\RtTitle\Subpath';
    $rname = $this->generateAppNameNew('\Resource\\' . $rname);
    $path = '/qualify-test/foo-bar-yeah/subpath';
    $path_components = array(
      'qualify-test' => 'qualify-test',
      'title'        => 'foo-bar-yeah',
      'subpath'      => 'subpath'
    );
    $body = '
      private $qualify_passed_arg;
      function qualify($path) {
        $this->qualify_passed_arg = $path;
        return true;
      }
      
      function GET() {
        return $this->qualify_passed_arg;
      }
    ';
    $this->createClassDefinition($rname, '\Asar\Resource', $body);
    $R = new $rname;
    $this->assertEquals(
      $path_components,
      $R->handleRequest(new Request(array('path' => $path)))->getContent()
    );
  }
  
  /**
   * @dataProvider dataRedirection
   */
  function testRedirection(
    $location, $type, $expected_status_code, $expected_location
  ) {
    self::$inc++;
    $cname = $this->createResourceClassDefinition(
      'Redirecting' . self::$inc++,
      "return \$this->redirectTo('$location', '$type');"
    );
    $R = new $cname;
    $response = $R->handleRequest(new Request);
    $this->assertEquals($expected_status_code, $response->getStatus());
    $this->assertEquals(
      $expected_location, $response->getHeader('Location')
    );
  }
  
  function dataRedirection() {
    return array(
      array('http://www.foo.com/', 'basic', 302, 'http://www.foo.com/'),
      array('/a/relative/path', 'temporary', 307, '/a/relative/path'),
      array('/another/path', 'permanent', 301, '/another/path'),
      array('A\Resource\Name', '', 302, 'A\Resource\Name'),
      array('/another/path', 'see', 303, '/another/path'),
    );
  }
  
  function testRedirectMultipleChoices() {
    self::$inc++;
    $location = "array('/preferred/path', '/another/path', '/yet/again')";
    $cname = $this->createResourceClassDefinition(
      'Redirecting' . self::$inc++,
      "return \$this->redirectTo($location, 'multiple');"
    );
    $R = new $cname;
    $response = $R->handleRequest(new Request);
    $this->assertEquals(300, $response->getStatus());
    $headers = $response->getHeaders();
    $this->assertEquals(
      '/preferred/path', $headers['Location']
    );
    $this->assertArrayHasKey('Asar-Internal-Locationslist', $headers);
    $this->assertEquals(
      array('/preferred/path', '/another/path', '/yet/again'),
      $headers['Asar-Internal-Locationslist']
    );
  }
  
}
