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
   * @dataProvider dataTraversing
   */
  function testTraversing($path, $expected_content, $xpctdstatus = 200) {
    $response = $this->app->handleRequest(new Asar_Request(
      array('path' => $path)
    ));
    $this->assertEquals(
      $xpctdstatus, $response->getStatus(), $response->getContent()
    );
    if ($response->getStatus() === 200 && $expected_content) {
      $this->assertEquals($expected_content, $response->getContent());
    }
  }
  
  function dataTraversing() {
    return array(
      array('/', '/ GET.'),
      array('/blog', '/blog GET.'),
      array('/parent', '/parent GET.'),
      array('/parent/child', '/parent/child GET.'),
      array('/parent/child/grand-child', '/parent/child/grand-child GET.'),
      array('/forward-to-child', '/parent/child GET.'),
      array('/blog/2010', '/blog/2010 GET.'),
      array('/blog/Churvaluvalu', null, 404),
      array('/blog/2010/09', '/blog/2010/09 GET.'),
    );
  }
  
  function testRedirection() {
    $this->markTestIncomplete();
  }

}
