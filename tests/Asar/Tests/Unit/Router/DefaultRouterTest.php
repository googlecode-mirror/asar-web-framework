<?php

namespace Asar\Tests\Unit\Router;

require_once realpath(dirname(__FILE__). '/../../../../config.php');

use Asar\Router\DefaultRouter;

/**
 * @todo Make the tests more readable.
 * @todo Make router follow new naming convention
 */
class DefaultRouterTest extends \Asar\Tests\TestCase {
  
  function setUp() {
    $this->resource_lister  = $this->getMock(
      'Asar\ResourceLister\ResourceListerInterface',
      array('getResourceListFor')
    );
    $this->resource_factory = $this->quickMock(
      'Asar\ResourceFactory', array('getResource')
    );
    $this->router = new DefaultRouter(
      $this->resource_factory, $this->resource_lister
    );
  }
  
  function generateRandomClassName($prefix = 'Amock', $suffix = '') {
    if ($suffix)
      $suffix = '\\' . $suffix;
    do {
      $randomClassName = $prefix . '\\A' . substr(md5(microtime()), 0, 8) . 
        $suffix;
    } while ( class_exists($randomClassName, FALSE) );
    return $randomClassName;
  }
  
  function testRouterInstanceOfRouterInterface() {
    $this->assertInstanceOf('Asar\Router\RouterInterface', $this->router);
  }
  
  /**
   * @dataProvider dataReturnsRoutedResource
   */
  function testReturnsRoutedResource($path, $resource_name) {
    $app_name = $this->generateRandomClassName(get_class($this));
    $classes = $this->createClassesBasedOnResourceName(
      $app_name, $resource_name
    );
    $this->resource_lister->expects($this->any())
      ->method('getResourceListFor')
      ->will($this->returnValue($classes));
    $expected_resource = $app_name . '\Resource\\' . $resource_name;
    $this->resource_factory->expects($this->once())
      ->method('getResource')
      ->with($expected_resource);
    try {
      $this->router->route($app_name, $path, array());
    } catch (\Asar\Router\Exception $e) {
      $this->fail(
        "Router was unable to match '$path' with '$expected_resource'."
      );
    }
  }
  
  function dataReturnsRoutedResource() {
    return array(
      array('/basic', 'Basic'),
      array('/page', 'Page'),
      array('/some-where', 'SomeWhere'),
      array('/when/the-going/gets_tough', 'When\TheGoing\GetsTough'),
      array('/blog/2010/8/25', 'Blog\RtYear\RtMonth\RtDay'),
      array(
        '/news/2010/8/25',
        'News\RtYear\RtMonth\RtDay'
      ),
      array('/articles/This-is-an-article-title', 'Articles\RtTitle'),
      array('Articles\RtTitle', 'Articles\RtTitle'),
    );
  }
  
  private function createClassesBasedOnResourceName($app_name, $resource_name) {
    $resource_levels = explode('\\', $resource_name);
    $test_resource = $app_name . '\Resource';
    $classes_used = array();
    foreach ($resource_levels as $level) {
      $test_resource .= '\\' . $level;
      $classes_used[] = $test_resource;
      if (!class_exists($test_resource)) {
        $this->createClassDefinition($test_resource);
      }
    }
    return $classes_used;
  }
  
  /**
   * @dataProvider dataWildCardRoutingAndAListOfAvailableResources
   */
  function testWildCardRoutingAndAListOfAvailableResources(
    $path, $resource_names, $expected, $reverse = false
  ) {
    $app_name = $this->generateRandomClassName(get_class($this));
    $resource_name1 = 'Blogs\RtTitle';
    $resource_name2 = 'Blogs\Foo';
    $class_collection = array();
    foreach ($resource_names as $resource_name) {
      $class_collection[] = $this->createClassesBasedOnResourceName(
        $app_name, $resource_name
      );
    }
    $classes = array_unique(
      call_user_func_array('array_merge', $class_collection)
    );
    if ($reverse) {
      $classes = array_reverse($classes);
    }
    $this->resource_lister->expects($this->any())
      ->method('getResourceListFor')
      ->will($this->returnValue($classes));
    $expected_resource = $app_name . '\Resource\\' . $expected;
    $this->resource_factory->expects($this->once())
      ->method('getResource')
      ->with($expected_resource);
    try {
      $this->router->route($app_name, $path, array());
    } catch (\Asar\Router\Exception $e) {
      $this->fail(
        "Router was unable to match '$path' with '$expected_resource' with ".
        "list of expected resources $class_collection."
      );
    }
  }
  
  function dataWildCardRoutingAndAListOfAvailableResources() {
    return array(
      array(
        '/blogs/This-is-a-blog-title',
        array('Blogs\RtTitle', 'Blogs\Foo'),
        'Blogs\RtTitle'
      ),
      array(
        '/blogs/This-is-a-blog-title/edit',
        array('Blogs\RtTitle', 'Blogs\Foo', 'Blogs\RtTitle\Edit'),
        'Blogs\RtTitle\Edit'
      ),
      array(
        '/blogs/This-is-a-blog-title/edit',
        array('Blogs\RtTitle', 'Blogs\Foo', 'Blogs\RtTitle\Edit'),
        'Blogs\RtTitle\Edit', true
      ),
      array(
        '/vlogs/Foo',
        array('Vlogs\RtTitle', 'Vlogs\Foo'),
        'Vlogs\Foo'
      ),
    );
  }
  
  function testRouterReturnsObjFromResourceFactory() {
    $app_name = $this->generateRandomClassName(get_class($this));
    $this->createClassDefinition($app_name . '\Resource\Foo');
    $obj = $this->getMock('Asar\Resource');
    $this->resource_factory->expects($this->once())
      ->method('getResource')
      ->will($this->returnValue($obj));
    try {
      $this->assertSame($obj, $this->router->route($app_name, '/foo', array()));
    } catch (Exception $e) {
      $this->fail($e->getMessage());
    }
  }
  
  /**
   * @dataProvider dataRouterUsesMap
   */
  function testRouterUsesMap($path, $resource_name) {
    $map = array($path => $resource_name);
    $app_name = $this->generateRandomClassName(get_class($this));
    $this->createClassDefinition($app_name . '\Resource\\' . $resource_name);
    //eval (sprintf("class %s {}", ));
    $this->resource_factory->expects($this->once())
      ->method('getResource')
      ->with($app_name . '\\Resource\\' . $resource_name);
    $this->router->route($app_name, $path, $map);
  }
  
  function dataRouterUsesMap() {
    return array(
      array('/', 'MyIndex'),
      array('/foo', 'FooResource'),
      array('/foo/bar', 'The\\Foo\\Bar\\Resource'),
    );
  }
  
  function testRouterThrowsResourceNotFoundException() {
    $this->resource_lister->expects($this->any())
      ->method('getResourceListFor')
      ->will($this->returnValue(array()));
    $this->setExpectedException(
      'Asar\Router\Exception\ResourceNotFound',
      "The resource class definition for the path '/nowhere' was not found."
    );
    $this->router->route('A_Name', '/nowhere', array());
  }
  
  function testRouterThrowsResourceNotFoundException2() {
    $this->resource_lister->expects($this->any())
      ->method('getResourceListFor')
      ->will($this->returnValue(array()));
    $this->setExpectedException(
      'Asar\Router\Exception\ResourceNotFound',
      "The resource class definition for the path 'Nowhere' was not found."
    );
    $this->router->route('A_Name', 'Nowhere', array());
  }
  
}
