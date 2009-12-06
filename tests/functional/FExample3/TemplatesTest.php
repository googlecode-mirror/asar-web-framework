<?php
require_once realpath(dirname(__FILE__) . '/../../config.php');
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(realpath(__FILE__)));

class FExample3_TemplatesTest extends PHPUnit_Framework_TestCase {

  public function setUp() {
    $this->client = new Asar_Client;
    $this->app = new Example3_Application;
    $this->client->setServer($this->app);
  }
  
  public function testIndexAndItsDefaultTemplate() {
    $response = $this->client->GET('/');
    $this->assertContains(
      '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"',
      $response->getContent(),
      'Layout was not rendered properly'
    );
    $html = new Asar_Utility_XML(
      $response->getContent()
    );
    
    $this->assertEquals(
      'This is the main heading found in Index/GET.html.php.',
      $html->body->h1->stringValue(),
      'Did not find heading in response content.'
    );
    $this->assertEquals(
      'This is the paragraph. Easy, no?',
      $html->body->p->stringValue(),
      'Did not find paragraph in response content.'
    );
  }
  
  public function testIndexAndPostRequest() {
    $response = $this->client->POST('/');
    $this->assertContains(
      '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"',
      $response->getContent(),
      'Layout was not rendered properly'
    );
    $html = new Asar_Utility_XML(
      $response->getContent()
    );
    
    $this->assertEquals(
      'This is the main heading found in Index/POST.html.php.',
      $html->body->h1->stringValue(),
      'Did not find heading in response content.'
    );
    $this->assertEquals(
      'This is the subheading for the POST template',
      $html->body->h2->stringValue(),
      'Did not find heading in response content.'
    );
    $this->assertEquals(
      'And this is the paragraph',
      $html->body->div->p->stringValue(),
      'Did not find paragraph in response content.'
    );
  }
  
  public function testIndexWithGetTxtRequest() {
    $content = $this->client
      ->GET('/', array(), array('Accept' => 'text/plain'))
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
      'Did not find heading in response content.'
    );
    $this->assertContains(
      'This is the paragraph. Easy, no?',
      $content,
      'Did not find paragraph in response content.'
    );
  }
  
  public function testIndexWithGetXmlRequest() {
    $response = $this->client
      ->GET('/xml', array(), array('Accept' => 'application/xml'));
    $this->assertContains(
      '<foo>This is from Xml.php</foo>',
      $response->getContent()
    );
    $this->assertEquals(
      'application/xml', $response->getHeader('Content-Type')
    );
  }
  
  public function testSettingOnTemplateNoLayout() {
    $response = $this->client->GET('/nolayout');
    $this->assertNotContains(
      '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"',
      $response->getContent(),
      'Layout must not render when template is set to noLayout.'
    );
    $this->assertContains(
      '<h1>This is the main heading.',
      $response->getContent(),
      'Did not find heading in response content.'
    );
    $this->assertContains(
      '<p>This is the paragraph. Easy, no?',
      $response->getContent(),
      'Did not find paragraph in response content.'
    );
  }
  
  public function testSettingLayoutVariable() {
    $response = $this->client->GET('/set-layout');
    $html = new Asar_Utility_XML($response->getContent());
    $this->assertEquals(
      'This is the main heading found in SetLayout/GET.html.php.',
      $html->body->h1->stringValue()
    );
    $this->assertEquals(
      'SetLayout Title',
      $html->head->title->stringValue(),
      'Unable to set layout variable.'
    );
  }
  
  public function testAlternativeTemplateLookup() {
    $content = $this->client->GET('/alternative')->getContent();
    $this->assertContains(
      '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"', $content
    );
    $html = new Asar_Utility_XML($content);
    $this->assertEquals(
      'This is the main heading found in Alternative.GET.html.php.',
      $html->body->h1->stringValue()
    );
    $this->assertEquals(
      'This is an alternative template setup.', $html->body->p->stringValue()
    );
  }
}
