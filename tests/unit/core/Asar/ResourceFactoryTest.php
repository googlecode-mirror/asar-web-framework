<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

class Asar_ResourceFactoryTest extends PHPUnit_Framework_TestCase {

  function setUp() {
    $this->F = new Asar_ResourceFactory(
      $this->getMock('Asar_TemplateLFactory', array(), array(), '', false),
      $this->getMock('Asar_TemplateSimpleRenderer', array(), array(), '', false)
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
    $this->assertType('Asar_Templater', $this->F->getResource($class));
  }
  
  /**
   * @dataProvider dataGetsResourceDecoratedByRepresentationObject
   */
  function testGetsResourceDecoratedByRepresentationObject(
    $resource_class, $representation_class
  ) {
    eval("class $resource_class extends Asar_Resource {}");
    eval(
      'class ' . $representation_class . ' extends Asar_Representation {
        function getResource() {
          return $this->resource;
        }
      }'
    );
    $resource = $this->F->getResource($resource_class);
    $this->assertType($representation_class, $resource);
    $this->assertType($resource_class, $resource->getResource());
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
  

}
