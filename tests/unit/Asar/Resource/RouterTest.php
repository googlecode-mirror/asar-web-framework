<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Asar_Resource_RouterTest extends Asar_Test_Helper {

  function setUp() {
    $this->F   = new Asar_Resource_Router;
    $this->app = $this->getMock('Asar_Application', array(), array(),
      self::generateRandomClassName(get_class($this), 'Application')
    );
  }
  
  function testReturnsBasicMapping($path = '/', $resource_name = 'Index') {
    $app_name = str_replace('_Application', '', get_class($this->app));
    
    $resource_levels = explode('_', $resource_name);
    $test_resource = $app_name . '_Resource';
    foreach ($resource_levels as $level) {
      $test_resource .= '_' . $level;
      if (!class_exists($test_resource)) {
        eval ("class $test_resource {}");
      }
    }
    $expected_resource = $app_name . '_Resource_' . $resource_name;
    
    $this->assertEquals(
      $expected_resource,
      $this->F->getRoute($this->app, $path),
      "Wrong resource name returned for path '$path'."
    );
  }
  
  function testReturnAnotherBasicMapping() {
    $this->testReturnsBasicMapping('/basic', 'Basic');
  }
  
  function testReturnAnotherBasicMapping2() {
    $this->testReturnsBasicMapping('/page', 'Page');
  }
  
  function testReturnAnotherBasicMappingBetter() {
    $rand = Asar_Utility_RandomStringGenerator::instance();
    $path = '/' . $rand->getLowercaseAlpha(mt_rand(5,20));
    $expected = strtoupper($path[1]) . substr($path, 2);
    $this->testReturnsBasicMapping($path, $expected);
  }
  
  function testReturnMappingForMultiWordResources() {
    $this->testReturnsBasicMapping('/some-where', 'SomeWhere');
  }
  
  function testReturnMappingForMultiLevelPaths() {
    $this->testReturnsBasicMapping(
      '/when/the-going/gets_tough', 'When_TheGoing_GetsTough'
    );
  }
  
  function testMappingForWildCardResource() {
    $this->testReturnsBasicMapping('/pages/a-name-of-a-page', 'Pages__Item');
  }
  
  function testMappingForWildCardResourceAndAnotherLevel() {
    $this->testReturnsBasicMapping(
      '/pages/a-name-of-a-page/edit', 'Pages__Item_Edit'
    );
  }
  
}

/*
- Alternative Rout‌ing Strategy
  - Based on class names and class naming convention
  - /foo/bar/baz would map to App_Resource_Foo_Bar_Baz
  - /foo/bar-baz would map to App_Resource_Foo_BarBaz
  - Special Names would have double underscores like 
    App_Resource_Foo_Bar__<Special Name>
  - 404s would look for App_Resource_Foo_Bar__404
  - Wildcards would look for Foo_Bar__Baz
  - Non-valid names would map to Foo_Bar__Map??
*/

