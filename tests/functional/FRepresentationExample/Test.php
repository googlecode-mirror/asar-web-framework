<?php
require_once realpath(dirname(__FILE__) . '/../../config.php');
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(realpath(__FILE__)));

class FRepresentationExample_Test extends PHPUnit_Framework_TestCase {

  public function setUp() {
    $this->client = new Asar_Client;
    $this->app = Asar_ApplicationInjector::injectApplication(
      new Asar_ApplicationScope('RepresentationExample', new Asar_Config_Default)
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
