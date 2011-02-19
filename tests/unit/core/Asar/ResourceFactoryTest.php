<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

class Asar_ResourceFactoryTest extends PHPUnit_Framework_TestCase {

  function setUp() {
    $conf = new Asar_Config(array('foo' => 'bar'));
    $this->F = new Asar_ResourceFactory(
      $this->getMock('Asar_TemplateLFactory', array(), array(), '', false),
      $this->getMock('Asar_TemplateSimpleRenderer', array(), array(), '', false),
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
  
  function testGetResourceReturnsResourceObject() {
    $class = self::generateRandomClassName(get_class($this), 'Resource_Foo');
    eval("class $class extends Asar_Resource {}");
    $this->assertInstanceOf('Asar_Templater', $this->F->getResource($class));
  }
  
  private function buildResourceClass($resource_class) {
    eval("class $resource_class extends Asar_Resource {}");
  }
  
  /**
   * @dataProvider dataGetsResourceDecoratedByRepresentationObject
   */
  function testGetsResourceDecoratedByRepresentationObject(
    $resource_class, $representation_class
  ) {
    $this->buildResourceClass($resource_class);
    eval(
      'class ' . $representation_class . ' extends Asar_Representation {
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
        get_class($this) . '_DecorationTest_Resource_Foo',
        get_class($this) . '_DecorationTest_Representation_Foo'
      ),
      array(
        get_class($this) . 
          '_DecorationTest_Resource_Resource_Foo_Resource_Bar',
        get_class($this) . 
          '_DecorationTest_Representation_Resource_Foo_Resource_Bar'
      ),
    );
  }
  
  function testPassesConfigToResource() {
    $resource_class = get_class($this) . '_DecorationTest_Resource_Jar';
    $this->buildResourceClass($resource_class);
    $resource = $this->F->getResource($resource_class);
    $this->assertEquals('bar', $resource->getConfig('foo'));
  }
  

}
