<?php

namespace Asar\Tests\Unit;

require_once realpath(dirname(__FILE__). '/../../../config.php');

use \Asar\ResourceLister;
use \Asar\Application\Finder as AppFinder;
use \Asar\IncludePathManager;

class ResourceListerTest extends \Asar\Tests\TestCase {

  function setUp() {
    $this->resource_lister = new ResourceLister(new AppFinder);
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
      $suffix = '\\' . $suffix;
    do {
      $randomClassName = $prefix . '\A' . 
      substr(md5(microtime()), 0, 8) . $suffix;
    } while ( class_exists($randomClassName, FALSE) );
    return $randomClassName;
  }
  
  private function createResourceFiles($app_name, $resources) {
    $classes_used = array();
    foreach ($resources as $resource) {
      $full_resource_name = $app_name . '\\Resource\\' . $resource;
      $classes_used[] = $full_resource_name;
      $full_file_path = str_replace('\\', '/', $full_resource_name) . '.php';
      $this->TFM->newFile(
        $full_file_path, 'foo'
      );
    }
    return $classes_used;
  }
  
  function testFirst() {
    $app_name = $this->generateRandomClassName(get_class($this));
    $app_dir = str_replace('\\', '/', $app_name);
    // Create App Directory Structure and Files
    $this->TFM->newDir($app_dir);
    // Create App File
    $this->TFM->newFile(
      "$app_dir/Application.php", 
      "<?php " . $this->createClassDefinitionStr(
        $app_name . '\Application', '\Asar\Application'
      )
    );
    include_once $this->TFM->getPath("$app_dir/Application.php");
    // With Resources
    $resources = array(
      'Index', 'Foo', 'Foo\Bar', 'Foo\Baz', 'Parent', 'Parent\Child',
      'Parent\Child\GrandChild'
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
