<?php

namespace Asar\Tests\Functional\ResourceTraversing;

require_once realpath(__DIR__ . '/../../../../') . '/config.php';

use \Asar\Client;
use \Asar\ApplicationInjector;
use \Asar\ApplicationScope;
use \Asar\Config\DefaultConfig;
use \Asar\Request;

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__);

class Test extends \Asar\Tests\TestCase {
  
  function setUp() {
    $this->client = new Client;
    $this->app = ApplicationInjector::injectApplication(
      new ApplicationScope(
        'Asar\Tests\Functional\ResourceTraversing\ResourceTraversing',
        new DefaultConfig
      )
    );
  }
  
  /**
   * @dataProvider dataTraversing
   */
  function testTraversing($path, $expected_content, $xpctdstatus = 200) {
    $response = $this->app->handleRequest(new Request(
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
      array('/a-parent', '/a-parent GET.'),
      array('/a-parent/child', '/a-parent/child GET.'),
      array('/a-parent/child/grand-child', '/a-parent/child/grand-child GET.'),
      array('/forward-to-child', '/a-parent/child GET.'),
      array('/blog/2010', '/blog/2010 GET.'),
      array('/blog/Churvaluvalu', null, 404),
      array('/blog/2010/09', '/blog/2010/09 GET.'),
      array('/blog/2010/09/an-amazing-title', 'an-amazing-title'),
    );
  }
  
  function testRedirection() {
    $response = $this->app->handleRequest(new Request(
      array('path' => '/redirect-one')
    ));
    $this->assertEquals(302, $response->getStatus(), $response->getContent());
    $this->assertEquals(
      'http://asar-test.local/a-parent/child/grand-child',
      $response->getHeader('Location')
    );
  }

}
