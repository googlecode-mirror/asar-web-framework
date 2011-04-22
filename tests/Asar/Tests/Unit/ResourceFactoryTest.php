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
      $suffix = '\\' . $suffix;
    do {
      $randomClassName = $prefix . '\A' . 
      substr(md5(microtime()), 0, 8) . $suffix;
    } while ( class_exists($randomClassName, FALSE) );
    return $randomClassName;
  }
  
  function testGetResourceReturnsATemplaterObjectObject() {
    $class = self::generateRandomClassName(
      $this->generateAppNameNew(''), 'Resource\Foo'
    );
    $this->createClassDefinition($class, '\Asar\Resource');
    $this->assertInstanceOf('\Asar\Templater', $this->F->getResource($class));
  }
  
  private function buildResourceClass($resource_class) {
    $this->createClassDefinition($resource_class, '\Asar\Resource');
  }
  
  /**
   * @dataProvider dataGetsResourceDecoratedByRepresentationObject
   */
  function testGetsResourceDecoratedByRepresentationObject(
    $resource_class, $representation_class
  ) {
    $this->buildResourceClass($resource_class);
    $this->createClassDefinition(
      $representation_class, '\Asar\Representation',
      'function getResource() {
        return $this->resource;
      }'
    );
    $resource = $this->F->getResource($resource_class);
    $this->assertInstanceOf($representation_class, $resource);
    $this->assertInstanceOf($resource_class, $resource->getResource());
  }
  
  function dataGetsResourceDecoratedByRepresentationObject() {
    return array(
      array(
        $this->generateAppName('\DecorationTest\Resource\Foo'),
        $this->generateAppName('\DecorationTest\Representation\Foo')
      ),
      array(
        $this->generateAppName( 
          '\DecorationTest\Resource\Resource\Foo\Resource\Bar'
        ),
        $this->generateAppName( 
          '\DecorationTest\Representation\Resource\Foo\Resource\Bar'
        )
      ),
    );
  }
  
  function testPassesConfigToResource() {
    $resource_class = $this->generateAppNameNew('\DecorationTest\Resource\Jar');
    $this->buildResourceClass($resource_class);
    $resource = $this->F->getResource($resource_class);
    $this->assertEquals('bar', $resource->getConfig('foo'));
  }
  
  function testGetResourceThrowsExceptionWhenResourceClassIsNotDefined() {
    $class = self::generateRandomClassName(
      $this->generateAppNameNew(''), 'Resource\Undefined\Class'
    );
    $this->setExpectedException(
      'Asar\ResourceFactory\Exception', 
      "The resource class '$class' is not defined or could not be found."
    );
    $this->F->getResource($class);
  }
  

}
