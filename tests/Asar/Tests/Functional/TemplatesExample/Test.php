<?php

namespace Asar\Tests\Functional\TemplatesExample;

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
      new ApplicationScope('TemplatesExample', new DefaultConfig)
    );
  }
  
  public function testIndexAndItsDefaultTemplate() {
    $content = $this->client->GET($this->app)->getContent();
    $this->assertContains(
      '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"',
      $content, "Layout was not rendered properly in: \n $content"
    );
    
    $this->assertTag(
      array(
        'tag' => 'h1',
        'content' => 'This is the main heading found in Index/GET.html.php.',
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
  
  public function testIndexAndPostRequest() {
    $content = $this->client->POST($this->app)->getContent();
    $this->assertContains(
      '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"',
      $content, 'Layout was not rendered properly'
    );
    
    $this->assertTag(
      array(
        'tag' => 'h1',
        'content' => 'This is the main heading found in Index/POST.html.php.',
        'parent' => array('tag' => 'body', 'parent' => array('tag' => 'html'))
      ),
      $content, "Did not find heading in response content:\n $content"
    );
    
    $this->assertTag(
      array(
        'tag' => 'h2',
        'content' => 'This is the subheading for the POST template',
        'parent' => array('tag' => 'body')
      ),
      $content, "Did not find heading in response content:\n $content"
    );
    
    $this->assertTag(
      array(
        'tag' => 'p',
        'content' => 'And this is the paragraph',
        'parent' => array('tag' => 'div')
      ),
      $content, "Did not find paragraph in response content:\n $content"
    );
    
  }
  
  public function testIndexWithGetTxtRequest() {
    $content = $this->client
      ->GET($this->app, array('headers' => array('Accept' => 'text/plain')))
      ->getContent();
    $this->assertNotContains(
      '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"',
      $content,
      'Layout must not be found.'
    );
    $this->assertContains(
      "=====================================================\n" .
      "This is the main heading found in Index/GET.txt.php.\n" .
      "=====================================================\n",
      $content,
      "Did not find heading in response content:\n $content"
    );
    $this->assertContains(
      'This is the paragraph. Easy, no?',
      $content,
      "Did not find paragraph in response content:\n $content"
    );
  }
  
  public function testIndexWithGetXmlRequest() {
    $response= $this->client->GET(
      $this->app, array(
        'path'=>'/xml', 'headers' => array('Accept' => 'application/xml')
      )
    );
    $content = $response->getContent();
    $this->assertContains(
      '<foo>This is from Xml.php</foo>',
      $content, "Did not find expected string in:\n$content"
    );
    $this->assertEquals(
      'application/xml', $response->getHeader('Content-Type')
    );
  }
  
  public function testSettingOnTemplateNoLayout() {
    $content = $this->client->GET(
      $this->app, array('path' => '/nolayout')
    )->getContent();
    $this->assertNotContains(
      '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"',
      $content,
      "Layout must not render when template is set to noLayout.\n$content"
    );
    $this->assertContains(
      '<h1>This is the main heading.',
      $content,
      "Did not find heading in response content.\n$content"
    );
    $this->assertContains(
      '<p>This is the paragraph. Easy, no?',
      $content,
      "Did not find paragraph in response content.\n$content"
    );
  }
  
  public function testSettingLayoutVariable() {
    $content = $this->client->GET(
      $this->app, array('path' => '/set-layout')
    )->getContent();
    $this->assertTag(
      array(
        'tag' => 'h1',
        'content' => 'This is the main heading found in SetLayout/GET.html.php.',
        'parent' => array('tag' => 'body')
      ),
      $content, 'Did not find heading in response content.'
    );
    
    $this->assertTag(
      array(
        'tag' => 'title',
        'content' => 'SetLayout Title',
        'parent' => array('tag' => 'head')
      ),
      $content, 'Unable to set layout variable: ' . $content
    );
    
  }
  
  public function testAlternativeTemplateLookup() {
    $content = $this->client->GET($this->app, array('path'=>'/alternative'))->getContent();
    $this->assertContains(
      '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"', $content
    );
    $this->assertTag(
      array(
        'tag' => 'h1',
        'content' => 'This is the main heading found in Alternative.GET.html.php.',
        'parent' => array('tag' => 'body')
      ),
      $content, 'Did not find heading in response content.'
    );
    $this->assertTag(
      array(
        'tag' => 'p',
        'content' => 'This is an alternative template setup.',
        'parent' => array('tag' => 'body')
      ),
      $content
    );
  }
  
  /**
   * @dataProvider dataContentNegotiationWithComplexAcceptHeader
   */
  function testContentNegotiationWithComplexAcceptHeader($accept, $ctype) {
    $response = $this->client->GET(
      $this->app,
      array(
        'path'=>'/content_negotiation',
        'headers' => array('Accept' => $accept)
      )
    );
    $this->assertEquals($ctype, $response->getHeader('Content-Type'), $response->getContent());
  }
  
  function dataContentNegotiationWithComplexAcceptHeader() {
    return array(
      array(
        'text/html', 'text/html'
      ),
      array(
        'text/plain', 'text/plain'
      ),
      array(
        'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'text/html'
      ),
      array(
        'text/plain,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        'text/plain'
      ),
      array(
        'text/html,text/plain', 'text/html'
      ),
    );
  }
  
  function testAlternativeTemplateEngine() {
    $response = $this->client->GET(
      $this->app,
      array(
        'path'=>'/alt-template'
      )
    );
    $this->assertTag(
      array(
        'tag' => 'p',
        'content' => 'This is an alternative template setup.',
        'parent' => array(
          'tag' => 'body'
        )
      ),
      $response->getContent(),
      $response->getContent()
    );
  }
  
}
