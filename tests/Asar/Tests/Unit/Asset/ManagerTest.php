<?php
namespace Asar\Tests\Unit\Asset;

require_once realpath(dirname(__FILE__). '/../../../../config.php');

use \Asar\Asset\Manager;
use \Asar\Asset\Css;

class ManagerTest extends \Asar\Tests\TestCase {

  function setUp() {
    $this->TFM = $this->getTFM();
    $this->clearTestTempDirectory();
    $this->assets = new Manager;
  }
  
  function tearDown() {
    $this->clearTestTempDirectory();
  }
  
  function testIncludingCssConvertsToCssAsset() {
    $this->assets->includeCss('dir/file.css');
    $css_assets = $this->assets->getCssAssets();
    $this->assertInstanceOf('Asar\Asset\Css', $css_assets[0]);
    $this->assertEquals('dir/file.css', $css_assets[0]->getPath());
  }
  
  private function _assertSequence() {
    $args = func_get_args();
    $assets = array_shift($args);
    for($i = 0; $i < count($args); $i++) {
      $this->assertEquals($args[$i], $assets[$i]->getPath());
    }
  }
  
  function testIncluding3CssFilesConvertsToCssAsset() {
    $this->assets->includeCss('dir/file.css');
    $this->assets->includeCss('foo/bar.css');
    $this->assets->includeCss('f/zar.css');
    $this->_assertSequence(
      $this->assets->getCssAssets(), 'dir/file.css', 'foo/bar.css', 'f/zar.css'
    );
  }
  
  function testIncluding3CssFilesWithDependency() {
    $this->assets->includeCss(
      'dir/file.css', array('requires' => array('foo/bar.css'))
    );
    $this->assets->includeCss('foo/bar.css');
    $this->assets->includeCss('f/zar.css');
    $this->_assertSequence(
      $this->assets->getCssAssets(), 'foo/bar.css', 'dir/file.css', 'f/zar.css'
    );
  }
  
  function testIncluding3CssFilesWith1UnknownDependency() {
    $this->assets->includeCss(
      'dir/file.css', array('requires' => array('unknown.css'))
    );
    $this->assets->includeCss('foo/bar.css');
    $this->assets->includeCss('f/zar.css');
    $this->_assertSequence(
      $this->assets->getCssAssets(), 'dir/file.css', 'foo/bar.css', 'f/zar.css'
    );
  }
  
  function testIncludingJsConvertsToJsAsset() {
    $this->assets->includeJs('dir/file.js');
    $assets = $this->assets->getJsAssets();
    $this->assertInstanceOf('Asar\Asset\Js', $assets[0]);
    $this->assertEquals('dir/file.js', $assets[0]->getPath());
  }
  
  function testIncluding3JsFilesConvertToJsAssetsSorted() {
    $this->assets->includeJs('dir/file.js');
    $this->assets->includeJs('foo/bar.js');
    $this->assets->includeJs('foo/baz.js');
    $this->_assertSequence(
      $this->assets->getJsAssets(), 'dir/file.js', 'foo/bar.js', 'foo/baz.js'
    );
  }
  
  /*
  private function setupMinimumTemplate() {
    $this->TFM->newFile(
      'foo.php',
      '<html><head><title>Title</title></head><body></body></html>'
    );
    $this->T->setTemplateFile($this->TFM->getPath('foo.php'));
  }
  
  private function _testAddingCss($head) {
    $this->assertEquals(
      "<html><head><title>Title</title>\n$head</head><body></body></html>",
      $this->T->render()
    );
  }
  
  function testAddingCss() {
    $this->setupMinimumTemplate();
    $this->T->includeCss('dir/file.css');
    $this->_testAddingCss(
      '<link rel="stylesheet" type="text/css" media="screen" ' .
      "src=\"/dir/file.css\" />\n"
    );
  }
  
  function testAddingMoreCss() {
    $this->setupMinimumTemplate();
    $this->T->includeCss('dir/file.css');
    $this->T->includeCss('dir/anotherfile.css');
    $this->_testAddingCss(
      '<link rel="stylesheet" type="text/css" media="screen" ' .
      "src=\"/dir/file.css\" />\n" . 
      '<link rel="stylesheet" type="text/css" media="screen" ' .
      "src=\"/dir/anotherfile.css\" />\n"
    );
  }
  
  function testAddingCssWithDependencies() {
    $this->setupMinimumTemplate();
    $this->T->includeCss('dir/foo.css', 'dir/bar.css');
    $this->T->includeCss('dir/bar.css');
    $this->T->includeCss('dir/baz.css');
    $this->_testAddingCss(
      '<link rel="stylesheet" type="text/css" media="screen" ' .
      "src=\"/dir/bar.css\" />\n" . 
      '<link rel="stylesheet" type="text/css" media="screen" ' .
      "src=\"/dir/foo.css\" />\n" . 
      '<link rel="stylesheet" type="text/css" media="screen" ' .
      "src=\"/dir/baz.css\" />\n"
    );
  }*/

}
