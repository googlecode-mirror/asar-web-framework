<?php
namespace Asar\Tests\Unit\Asset;

require_once realpath(dirname(__FILE__). '/../../../../config.php');

use \Asar\Asset\Css;

class CssTest extends \Asar\Tests\TestCase {

  function setUp() {
    $this->css = new Css('foo/bar.css');
  }
  
  function testInstantiatingAndGettingPath() {
    $this->assertEquals('foo/bar.css', $this->css->getPath());
  }
  
  function testRenderGetsCssMarkup() {
    $this->assertEquals(
      '<link rel="stylesheet" type="text/css" media="screen" ' .
      'href="/foo/bar.css" />',
      $this->css->render()
    );
  }
  
  function testGettingDefaultDependencyIsEmptyArray() {
    $this->assertSame(array(), $this->css->getDependencies());
    $this->assertSame(0, count($this->css->getDependencies()));
  }
  
  function testGettingDependencyForAssetWithDependencySet() {
    $this->css = new Css('foo/bar.css', array('requires' => array('baz/goo.css')));
    $this->assertEquals(array('baz/goo.css'), $this->css->getDependencies());
  }
  
  function testSettingMediaType() {
    $this->css = new Css('baz/foo.css', array('media' => 'aural'));
    $this->assertEquals(
      '<link rel="stylesheet" type="text/css" media="aural" ' .
      'href="/baz/foo.css" />',
      $this->css->render()
    );
  }
  
}
