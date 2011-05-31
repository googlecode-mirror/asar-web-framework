<?php
namespace Asar\Tests\Unit\Asset;

require_once realpath(dirname(__FILE__). '/../../../../config.php');

use \Asar\Asset\Js;

class JsTest extends \Asar\Tests\TestCase {

  function setUp() {
    $this->js = new Js('foo/bar.js');
  }
  
  function testInstantiatingAndGettingPath() {
    $this->assertEquals('foo/bar.js', $this->js->getPath());
  }
  
  function testRenderGetsJsMarkup() {
    $this->assertEquals(
      '<script type="text/javascript" src="/foo/bar.js"></script>',
      $this->js->render()
    );
  }
  
  function testGettingDefaultDependencyIsEmptyArray() {
    $this->assertSame(array(), $this->js->getDependencies());
    $this->assertSame(0, count($this->js->getDependencies()));
  }
  
  function testGettingDependencyForAssetWithDependencySet() {
    $this->js = new Js('foo/bar.js', array('requires' => array('baz/goo.js')));
    $this->assertEquals(array('baz/goo.js'), $this->js->getDependencies());
  }
  
}
