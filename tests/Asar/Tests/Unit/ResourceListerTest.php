<?php

namespace Asar\Tests\Unit;

require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\ResourceLister;
use \Asar\FileSearcher;
use \Asar\IncludePathManager;

class ResourceListerTest extends \Asar\Tests\TestCase {

  function setUp() {
    $this->resource_lister = new ResourceLister(new FileSearcher);
    $this->tempdir = $this->getTempDir();
    $this->TFM = $this->getTFM();
    $this->IPM = new IncludePathManager;
    $this->IPM->add($this->tempdir);
	  $this->clearTestTempDirectory();
  }
  
  function tearDown() {
	  $this->clearTestTempDirectory();
	}
  
  private function generateRandomClassName($prefix = 'Amock', $suffix = '') {
    if ($suffix)
      $suffix = '_' . $suffix;
    do {
      $randomClassName = $prefix . '_' . 
      substr(md5(microtime()), 0, 8) . $suffix;
    } while ( class_exists($randomClassName, FALSE) );
    return $randomClassName;
  }
  
  private function createResourceFiles($app_name, $resources) {
    $classes_used = array();
    foreach ($resources as $resource) {
      $full_resource_name = $app_name . '_Resource_' . $resource;
      $classes_used[] = $full_resource_name;
      $this->TFM->newFile(
        str_replace('_', '/', $full_resource_name) . '.php', 'foo'
      );
    }
    return $classes_used;
  }
  
  function testFirst() {
    $app_name = $this->generateRandomClassName(get_class($this));
    // Create App Directory Structure and Files
    $this->TFM->newDir($app_name);
    // With Resources
    $resources = array(
      'Index', 'Foo', 'Foo_Bar', 'Foo_Baz', 'Parent', 'Parent_Child',
      'Parent_Child_GrandChild'
    );
    $expected_resources = $this->createResourceFiles($app_name, $resources);
    // Make sure we can see it
    $result = $this->resource_lister->getResourceListFor($app_name);
    
    // Check if ResourceLister finds everything
    //$this->assertEquals(count($expected_resources), count($result));
    foreach ($expected_resources as $resource) {
      $this->assertContains($resource, $result);
    }
  }

}