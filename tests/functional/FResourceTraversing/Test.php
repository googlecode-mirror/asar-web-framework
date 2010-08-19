<?php
require_once realpath(dirname(__FILE__) . '/../../config.php');
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(realpath(__FILE__)));

class FResourceTraversing_Test extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->client = new Asar_Client;
    $f = new Asar_ApplicationFactory(new Asar_Config_Default);
    $this->app = $f->getApplication('ResourceTraversing');
  }
  
  /**
   * @dataProvider dataTraversingSuccess
   */
  function testTraversingSuccess($path, $expected_content) {
    $response = $this->app->handleRequest(new Asar_Request(
      array('path' => $path)
    ));
    $this->assertEquals(200, $response->getStatus());
    $this->assertEquals($expected_content, $response->getContent());
  }
  
  function dataTraversingSuccess() {
    return array(
      array('/', '/ GET.'),
      array('/blog', '/blog GET.'),
      array('/parent', '/parent GET.'),
      array('/parent/child', '/parent/child GET.'),
      array('/parent/child/grand-child', '/parent/child/grand-child GET.')
    );
  }

}
