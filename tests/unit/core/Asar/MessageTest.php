<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

class Asar_MessageTest extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->M = new Asar_Message;
  }
  
  function testMessageShouldImplementAsarMessageInterface() {
    $this->assertType('Asar_Message_Interface', $this->M);
  }
  
  function testShouldBeAbleToSetContent() {
    $this->M->setContent(array('bar'=>'foo'));
    $this->assertEquals(
      array('bar'=>'foo'), $this->M->getContent(),
      'Unable to set content for Message object'
    );
  }
  
  function testMessagReturnsEmptyStringAsContentByDefault() {
    $this->assertSame('', $this->M->getContent());
  }
  
  function testHeaderFieldNamesShouldBeCaseInsensitive() {
    $this->M->setHeader('SoMe-fiEld-Name', 'Foo Bar');
    $this->assertEquals(
      'Foo Bar', $this->M->getHeader('somE-Field-nAmE'),
      'Field names for headers should be case-insensitive.'
    );
  }
  
  function testMultipleSettingOfHeaderFields() {
    $this->M->setHeaders(array(
      'A-Field' => 'goo',
      'Another-Field' => 'bar',
      'Some-Other-Field' => 'car'
    ));
    $headers = $this->M->getHeaders();
    $this->assertEquals(
      'goo', $headers['A-Field'],
      'Header field was not found.'
    );
    $this->assertEquals(
      'bar', $headers['Another-Field'],
      'Header field was not found.'
    );
    $this->assertEquals(
      'car', $headers['Some-Other-Field'],
      'Header field was not found.'
    );
  }
  
  function testHeaderFieldNamesShouldBeConvertedToDashedCamelCase() {
    $this->M->setHeader('afield', 'foo');
    $this->M->setHeader('yet-aNotHEr-Field', 'bar');
    $this->M->setHeader('And-yet-anOther', 'jar');
    $headers = $this->M->getHeaders();
    $this->assertTrue(
      array_key_exists('Afield', $headers),
      'Header field name was not converted to Dashed-Camel-Case.'
    );
    $this->assertTrue(
      array_key_exists('Yet-Another-Field', $headers),
      'Header field name was not converted to Dashed-Camel-Case.'
    );
    $this->assertTrue(
      array_key_exists('And-Yet-Another', $headers),
      'Header field name was not converted to Dashed-Camel-Case.'
    );
  }
  
  function testMessageShouldBeAbleToSetAcceptHeader() {
    $this->M->setHeader('Accept', 'text/plain');
    $this->assertEquals(
      'text/plain', $this->M->getHeader('Accept'),
      'Unable to set content-type.'
    );
  }
  
  function testSettingMessageHeaders() {
    // Some common HTTP Message headers...
    $headers = array(
      'User-Agent'    => 'Mozilla/5.0',
      'Accept'      => 'text/html',
      'Accept-Charset'  => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
      'Accept-Language' => 'tl,en-us;q=0.7,en;q=0.3',
      'Accept-Encoding' => 'gzip,deflate',
      'Host'      => 'somehost.com'
    );
    $this->M->setHeaders($headers);
    foreach ($headers as $key => $value) {
      $this->assertEquals(
        $value, $this->M->getHeader($key),
        "Unable to obtain $key request header."
      );
    }
  }
  
  function testRequestSettingContentOnCreation() {
    $M = new Asar_Message(array('content' => 'foo bar'));
    $this->assertEquals('foo bar', $M->getContent());
  }
  
  function testRequestSettingHeadersOnCreation() {
    $M = new Asar_Message(
      array('headers' => array('foo' => 'Foo', 'bar' => 'Baz'))
    );
    $this->assertEquals('Foo', $M->getHeader('foo'));
    $this->assertEquals('Baz', $M->getHeader('bar'));
  }
  
}
