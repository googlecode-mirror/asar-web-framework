<?php
require_once realpath(dirname(__FILE__). '/../../config.php');
require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Asar_ResponseTest extends PHPUnit_Framework_TestCase
{
  function setUp() {
    $this->R = new Asar_Response;
  }
  
  function testInstanceOfAsarResponseInterface() {
    $this->assertTrue(
      $this->R instanceof Asar_Response_Interface,
      'Asar_Response does not implement Asar_Response_Interface'
    );
  }
  
  function testAbleToSetContent() {
    $this->R->setContent('hello there!');
    $this->assertEquals(
      'hello there!',
      $this->R->getContent(),
      'The contents did not match "hello there!"'
    );
  }
  
  function testAbleToSetStatus() {
    $this->R->setStatus(404);
    $this->assertEquals(
      404, $this->R->getStatus(),
      'Unable to set Status'
    );
  }
  
  function testAbleToSetHeader() {
    $this->R->setHeader('Content-Type', 'text/plain');
    $this->assertEquals(
      'text/plain', $this->R->getHeader('Content-Type'),
      'Unable to set Header'
    );
  }
  
  function testGettingHeaders() {
    $headers = array(
      'Content-Type' => 'text/plain',
      'Content-Encoding' => 'gzip',
      'Vary'       => 'Accept-Encoding'
    );
    foreach ($headers as $name => $value) {
      $this->R->setHeader($name, $value);
    };
    $headers_output = $this->R->getHeaders();
    foreach ($headers as $name => $value) {
      $this->assertEquals(
        $value, $headers_output[$name],
        'Value in response did not match what was set.'
      );
    }
  }
  
  function testSettingMultipleHeadersAtOnce() {
    $headers = array(
      'Content-Type' => 'text/plain',
      'Content-Encoding' => 'gzip',
      'Vary'       => 'Accept-Encoding'
    );
    $this->R->setHeaders($headers);
    $headers_output = $this->R->getHeaders();
    foreach ($headers as $name => $value) {
      $this->assertEquals(
        $value, $headers_output[$name],
        'Value in response did not match what was set.'
      );
    }
  }
  
  function testGettingStatusCodeMessages() {
    $messages = array(
      100 => 'Continue',
      101 => 'Switching Protocols',
      200 => 'OK',
      201 => 'Created',
      202 => 'Accepted',
      203 => 'Non-Authoritative Information',
      204 => 'No Content',
      205 => 'Reset Content',
      206 => 'Partial Content',
      300 => 'Multiple Choices',
      301 => 'Moved Permanently',
      302 => 'Found',
      303 => 'See Other',
      304 => 'Not Modified',
      305 => 'Use Proxy',
      307 => 'Temporary Redirect',
      400 => 'Bad Request',
      401 => 'Unauthorized',
      402 => 'Payment Required',
      403 => 'Forbidden',
      404 => 'Not Found',
      405 => 'Method Not Allowed',
      406 => 'Not Acceptable',
      407 => 'Proxy Authentication Required',
      408 => 'Request Timeout',
      409 => 'Conflict',
      410 => 'Gone',
      411 => 'Length Required',
      412 => 'Precondition Failed',
      413 => 'Request Entity Too Large',
      414 => 'Request-URI Too Long',
      415 => 'Unsupported Media Type',
      416 => 'Requested Range Not Satisfiable',
      417 => 'Expectation Failed',
      500 => 'Internal Server Error',
      501 => 'Not Implemented',
      502 => 'Bad Gateway',
      503 => 'Service Unavailable'
    );
    foreach ($messages as $status => $reason_phrase) {
      $this->assertEquals(
        $reason_phrase, Asar_Response::getStatusMessage($status)
      );
    }
  }
  
  function testGettingStatusCodeMessagesForBadStatus() {
    $bad_status_codes = array(
      'foo', null, 1, 600, 10
    );
    foreach ($bad_status_codes as $status) {
      $this->assertEquals(
        null, Asar_Response::getStatusMessage($status)
      );
    }
  }
}
