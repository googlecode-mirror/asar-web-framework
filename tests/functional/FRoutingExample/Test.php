<?php
require_once realpath(dirname(__FILE__) . '/../../config.php');
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(realpath(__FILE__)));

class FRoutingExample_Test extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->client = new Asar_Client;
    $this->app = new RoutingExample_Application;
    $this->client->setServer($this->app);
  }
  
  function testGettingIndexResource() {
    $this->testGetAResource(
      '/', 'RoutingExample_Resource_Index'
    );
  }
  
  function testGetAResource(
    $path = '/page', 
    $expected_resource = 'RoutingExample_Resource_Page'
  ) {
    $response = $this->client->GET($path);
    $this->assertNotEquals(
      404, $response->getStatus(),
      "Did not find resource for path '$path'."
    );
    $this->assertEquals($expected_resource, $response->getContent());
  }
  
  function testGetDashedCamelCaseResource() {
    $this->testGetAResource(
      '/some-where', 'RoutingExample_Resource_SomeWhere'
    );
  }
  
  function testGetMultiLevelPath0() {
    $this->testGetAResource(
      '/root',
      'RoutingExample_Resource_Root'
    );
  }
  
  function testGetMultiLevelPath1() {
    $this->testGetAResource(
      '/root/a_branch',
      'RoutingExample_Resource_Root_ABranch'
    );
  }
  
  function testGetMultiLevelPath1b() {
    /** Should we redirect? **/
    $this->testGetAResource(
      '/root/a_branch/',
      'RoutingExample_Resource_Root_ABranch'
    );
  }
  
  function testGetMultiLevelPath3() {
    $this->testGetAResource(
      '/root/a_branch/another-branch/leaf',
      'RoutingExample_Resource_Root_ABranch_AnotherBranch_Leaf'
    );
  }
  
  function testGetAnIndexResource() {
    $this->testGetAResource(
      '/articles', 'RoutingExample_Resource_Articles'
    );
  }
  
  function testGetWildCardResource() {
    $this->testGetAResource(
      '/articles/an-article-name', 'RoutingExample_Resource_Articles__Item'
    );
  }
  
  function testGetWildCardResourceWithSubspace() {
    $this->testGetAResource(
      '/articles/another-article-name/edit',
      'RoutingExample_Resource_Articles__Item_Edit'
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

