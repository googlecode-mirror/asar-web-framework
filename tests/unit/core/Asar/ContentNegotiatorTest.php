<?php

require_once realpath(dirname(__FILE__). '/../../../config.php');

class Asar_ContentNegotiatorTest extends PHPUnit_Framework_TestCase {
  
  /**
   * @dataProvider dataFormatNegotiation
   */
  function testFormatNegotiation(
    $accept_header, $available_formats, $expected_format
  ) {
    $this->CN = new Asar_ContentNegotiator;
    $this->assertSame(
      $expected_format,
      $this->CN->negotiateFormat($accept_header, $available_formats)
    );
  }
  
  function dataFormatNegotiation() {
    return array(
      array('text/html', array('text/html'), 'text/html'),
      array('text/plain', array('text/html', 'text/plain'), 'text/plain'),
      
      // Prioritization
      array('*/*', array('text/html', 'text/plain'), 'text/html'),
      array('*/*', array('text/plain', 'text/html'), 'text/plain'),
      
      array(
        'text/*', 
        array('foo/bar', 'application/xhtml+xml', 'text/plain', 'text/html'),
        'text/plain'
      ),
      array(
        'application/*', 
        array('foo/bar', 'application/xhtml+xml', 'text/plain', 'text/html'),
        'application/xhtml+xml'
      ),
      
      // For unknown or undefined types
      array('text/plain', array('text/html'), FALSE),
      
      // For complex Accept Headers
      array(
        'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        array('text/html', 'text/plain'), 
        'text/html'
      ),
      array(
        'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        array('text/plain', 'application/json'), 
        'text/plain'
      ),
      array(
        'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        array('application/xhtml+xml', 'text/html'), 
        'application/xhtml+xml'
      ),
      array(
        'application/xml;q=0.9,*/*;q=0.8,text/html,application/xhtml+xml',
        array('application/xhtml+xml', 'application/xml'), 
        'application/xhtml+xml'
      ),
      array(
        'text/plain,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
        array('text/html', 'text/plain'), 
        'text/plain'
      ),
      
      // Complex edge cases
      //array(
      //  'application/xml;q=0.9,*/*;q=0.8,text/html,application/xhtml+xml',
      //  array('application/xml', 'application/xhtml+xml'), 
      //  'application/xhtml+xml'
      //),
    );
  }
  
}
