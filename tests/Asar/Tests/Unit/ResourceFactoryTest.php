<?php

namespace Asar\Tests\Unit;

require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\Config;
use \Asar\ResourceFactory;

/**
 * @todo Make classnames eventually use namespaces convention instead of
 * underscores.
 */
class ResourceFactoryTest extends \Asar\Tests\TestCase {

  function setUp() {
    $conf = new Config(array('foo' => 'bar'));
    $this->F = new ResourceFactory(
      $this->getMock('Asar\TemplatePackageProvider', array(), array(), '', false),
      $this->getMock('Asar\TemplateSimpleRenderer', array(), array(), '', false),
      $conf
    );
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
  
  function testGetResourceReturnsATemplaterObjectObject() {
    $class = self::generateRandomClassName(
      $this->generateAppName(''), 'Resource_Foo'
    );
    eval("class $class extends \Asar\Resource {}");
    $this->assertInstanceOf('\Asar\Templater', $this->F->getResource($class));
  }
  
  private function buildResourceClass($resource_class) {
    $resource_class = $this->generateUnderscoredName($resource_class);
    eval("class $resource_class extends \Asar\Resource {}");
  }
  
  /**
   * @dataProvider dataGetsResourceDecoratedByRepresentationObject
   */
  function testGetsResourceDecoratedByRepresentationObject(
    $resource_class, $representation_class
  ) {
    $this->buildResourceClass($resource_class);
    eval(
      'class ' . $representation_class . ' extends \Asar\Representation {
        function getResource() {
          return $this->resource;
        }
      }'
    );
    $resource = $this->F->getResource($resource_class);
    $this->assertInstanceOf($representation_class, $resource);
    $this->assertInstanceOf($resource_class, $resource->getResource());
  }
  
  function dataGetsResourceDecoratedByRepresentationObject() {
    return array(
      array(
        $this->generateAppName('_DecorationTest_Resource_Foo'),
        $this->generateAppName('_DecorationTest_Representation_Foo')
      ),
      array(
        $this->generateAppName( 
          '_DecorationTest_Resource_Resource_Foo_Resource_Bar'
        ),
        $this->generateAppName( 
          '_DecorationTest_Representation_Resource_Foo_Resource_Bar'
        )
      ),
    );
  }
  
  function testPassesConfigToResource() {
    $resource_class = $this->generateAppName('_DecorationTest_Resource_Jar');
    $this->buildResourceClass($resource_class);
    $resource = $this->F->getResource($resource_class);
    $this->assertEquals('bar', $resource->getConfig('foo'));
  }
  

}
