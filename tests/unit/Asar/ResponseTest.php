<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Asar_ResponseTest extends PHPUnit_Framework_TestCase
{
    function setUp() {
        $this->R = new Asar_Response;
    }
    
    public function testInstanceOfAsarResponseInterface()
    {
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
    
    public function testGettingHeaders()
    {
        $headers = array(
            'Content-Type' => 'text/plain',
            'Content-Encoding' => 'gzip',
            'Vary'             => 'Accept-Encoding'
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
    
    public function testSettingMultipleHeadersAtOnce()
    {
        $headers = array(
            'Content-Type' => 'text/plain',
            'Content-Encoding' => 'gzip',
            'Vary'             => 'Accept-Encoding'
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
}
