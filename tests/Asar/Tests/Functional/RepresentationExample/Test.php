<?php

namespace Asar\Tests\Functional\RepresentationExample;

require_once realpath(__DIR__ . '/../../../../') . '/config.php';

use \Asar\Client;
use \Asar\ApplicationInjector;
use \Asar\ApplicationScope;
use \Asar\Config\DefaultConfig;

set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__);

class Test extends \Asar\Tests\TestCase {

  public function setUp() {
    $this->client = new Client;
    $this->app = ApplicationInjector::injectApplication(
      new ApplicationScope(
        'RepresentationExample', new DefaultConfig
      )
    );
  }
  
  public function testIndexAndItsDefaultTemplate() {
    $content = $this->client->GET($this->app)->getContent();
    
    $this->assertTag(
      array(
        'tag' => 'h1',
        'content' => 'Hello World!',
        'parent' => array('tag' => 'body', 'parent' => array('tag' => 'html'))
      ),
      $content, "Did not find heading in response content:\n $content"
    );
    
    $this->assertTag(
      array(
        'tag' => 'p',
        'content' => 'This is the paragraph. Easy, no?',
        'parent' => array('tag' => 'body')
      ),
      $content, "Did not find paragraph in response content:\n $content"
    );
  }
  
  public function testIndexAndGettingTextRepresentation() {
    $content = $this->client->GET(
      $this->app, array('headers' => array('Accept' => 'text/plain'))
    )->getContent();
    $this->assertContains(
      "-------\nHello World!\n-------\n", $content
    );
    $this->assertContains(
      "-------\n\nThis is the paragraph. Easy, no?", $content
    );
  }
  
  public function testIndexAndGettingXmlRepresentation() {
    $content = $this->client->GET(
      $this->app, array('headers' => array('Accept' => 'application/xml'))
    )->getContent();
    $this->assertTag(
      array(
        'tag' => 'h1',
        'content' => 'Hello World!',
        'parent' => array('tag' => 'representation')
      ), $content, "Did not find h1 element in response content:\n $content"
      
    );
    $this->assertTag(
      array(
        'tag' => 'p',
        'content' => 'This is the paragraph. Easy, no?',
        'parent' => array('tag' => 'representation')
      ), $content, "Did not find p element in response content:\n $content"
    );
  }
}
