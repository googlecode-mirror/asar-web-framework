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
      'Asar\ResourceLister\ResourceListerInterface', array('getResourceListFor')
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
      $suffix = '_' . $suffix;
    do {
      $randomClassName = $this->generateUnderscoredName(
        $prefix . '_' . substr(md5(microtime()), 0, 8) . $suffix
      );
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
    $expected_resource = $app_name . '_Resource_' . $resource_name;
    $this->resource_factory->expects($this->once())
      ->method('getResource')
      ->with($expected_resource);
    try {
      $this->router->route($app_name, $path, array());
    } catch (Asar_Router_Exception $e) {
      $this->fail();
    }
  }
  
  function dataReturnsRoutedResource() {
    return array(
      array('/basic', 'Basic'),
      array('/page', 'Page'),
      array('/some-where', 'SomeWhere'),
      array('/when/the-going/gets_tough', 'When_TheGoing_GetsTough'),
      array('/blog/2010/8/25', 'Blog_RtYear_RtMonth_RtDay'),
      array(
        '/news/2010/8/25',
        'News_RtYear_RtMonth_RtDay'
      ),
      array('/articles/This-is-an-article-title', 'Articles_RtTitle'),
      array('Articles_RtTitle', 'Articles_RtTitle'),
    );
  }
  
  private function createClassesBasedOnResourceName($app_name, $resource_name) {
    $resource_levels = explode('_', $resource_name);
    $test_resource = $app_name . '_Resource';
    $classes_used = array();
    foreach ($resource_levels as $level) {
      $test_resource .= '_' . $level;
      $classes_used[] = $test_resource;
      if (!class_exists($test_resource)) {
        eval ("class $test_resource {}");
      }
    }
    return $classes_used;
  }
  
  /**
   * @dataProvider dataReturnsRoutedResource2
   */
  function testReturnsRoutedResource2(
    $path, $resource_names, $expected, $reverse = false
  ) {
    $app_name = $this->generateRandomClassName(get_class($this));
    $resource_name1 = 'Blogs_RtTitle';
    $resource_name2 = 'Blogs_Foo';
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
    $expected_resource = $app_name . '_Resource_' . $expected;
    $this->resource_factory->expects($this->once())
      ->method('getResource')
      ->with($expected_resource);
    try {
      $this->router->route($app_name, $path, array());
    } catch (Asar_Router_Exception $e) {
      $this->fail();
    }
  }
  
  function dataReturnsRoutedResource2() {
    return array(
      array(
        '/blogs/This-is-a-blog-title',
        array('Blogs_RtTitle', 'Blogs_Foo'),
        'Blogs_RtTitle'
      ),
      array(
        '/blogs/This-is-a-blog-title/edit',
        array('Blogs_RtTitle', 'Blogs_Foo', 'Blogs_RtTitle_Edit'),
        'Blogs_RtTitle_Edit'
      ),
      array(
        '/blogs/This-is-a-blog-title/edit',
        array('Blogs_RtTitle', 'Blogs_Foo', 'Blogs_RtTitle_Edit'),
        'Blogs_RtTitle_Edit', true
      ),
      array(
        '/vlogs/Foo',
        array('Vlogs_RtTitle', 'Vlogs_Foo'),
        'Vlogs_Foo'
      ),
    );
  }
  
  function testRouterReturnsObjFromResourceFactory() {
    $app_name = $this->generateRandomClassName(get_class($this));
    eval (sprintf("class %s {}", $app_name . '_Resource_Foo'));
    $obj = $this->getMock('Asar_Resource');
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
    eval (sprintf("class %s {}", $app_name . '_Resource_' . $resource_name));
    $this->resource_factory->expects($this->once())
      ->method('getResource')
      ->with($app_name . '_Resource_' . $resource_name);
    $this->router->route($app_name, $path, $map);
  }
  
  function dataRouterUsesMap() {
    return array(
      array('/', 'MyIndex'),
      array('/foo', 'FooResource'),
      array('/foo/bar', 'The_Foo_Bar_Resource'),
    );
  }
  
  function testRouterThrowsResourceNotFoundException() {
    $this->resource_lister->expects($this->any())
      ->method('getResourceListFor')
      ->will($this->returnValue(array()));
    $this->setExpectedException('Asar\Router\Exception\ResourceNotFound');
    $this->router->route('A_Name', '/nowhere', array());
  }
  
  function testRouterThrowsResourceNotFoundException2() {
    $this->resource_lister->expects($this->any())
      ->method('getResourceListFor')
      ->will($this->returnValue(array()));
    $this->setExpectedException('Asar\Router\Exception\ResourceNotFound');
    $this->router->route('A_Name', 'Nowhere', array());
  }
  
}
