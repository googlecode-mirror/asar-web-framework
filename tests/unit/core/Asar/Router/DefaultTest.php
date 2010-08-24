<?php

require_once realpath(dirname(__FILE__). '/../../../../config.php');

/**
 * TODO: Make the tests more readable.
 */
class Asar_Router_DefaultTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->resource_factory = $this->getMock(
      'Asar_ResourceFactory', array('getResource'), array(), '', FALSE
    );
    $this->router = new Asar_Router_Default($this->resource_factory);
  }
  
  static function generateRandomClassName($prefix = 'Amock', $suffix = '') {
    if ($suffix)
      $suffix = '_' . $suffix;
    do {
      $randomClassName = $prefix . '_' . 
      substr(md5(microtime()), 0, 8) . $suffix;
    } while ( class_exists($randomClassName, FALSE) );
    return $randomClassName;
  }
  
  function testRouterInstanceOfRouterInterface() {
    $this->assertType('Asar_Router_Interface', $this->router);
  }
  
  /**
   * @dataProvider dataReturnsRoutedResource
   */
  function testReturnsRoutedResource($path, $resource_name) {
    $app_name = self::generateRandomClassName(get_class($this));
    
    $resource_levels = explode('_', $resource_name);
    $test_resource = $app_name . '_Resource';
    foreach ($resource_levels as $level) {
      $test_resource .= '_' . $level;
      if (!class_exists($test_resource)) {
        eval ("class $test_resource {}");
      }
    }
    $expected_resource = $app_name . '_Resource_' . $resource_name;
    
    $this->resource_factory->expects($this->once())
      ->method('getResource')
      ->with($expected_resource);
    
    $this->router->route($app_name, $path, array());
  }
  
  function dataReturnsRoutedResource() {
    return array(
      //array('/', 'Index'),
      array('/basic', 'Basic'),
      array('/page', 'Page'),
      array('/some-where', 'SomeWhere'),
      array('/when/the-going/gets_tough', 'When_TheGoing_GetsTough'),
      array('/pages/a-name-of-a-page', 'Pages_Item'),
      array('/pages/another-page', 'Pages_Item'),
    );
  }
  
  function testRouterReturnsObjFromResourceFactory() {
    $app_name = self::generateRandomClassName(get_class($this));
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
    $app_name = self::generateRandomClassName(get_class($this));
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
    $this->setExpectedException('Asar_Router_Exception_ResourceNotFound');
    $this->router->route('A_Name', '/nowhere', array());
  }
  
  function testRouterGettingUrl() {
    $this->markTestIncomplete();
  }
  
}
