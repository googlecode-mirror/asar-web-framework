<?php

require_once realpath(dirname(__FILE__). '/../../config.php');

class Asar_RequestTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->R = new Asar_Request;
    }
    
    public function testRequestShouldImplementAsarRequestInterface()
    {
        $this->assertTrue(
            $this->R instanceof Asar_Request_Interface,
            'Asar_Request does not implement Asar_Request_Interface'
        );
    }
    
    public function testRequestShouldBeAbleToSetPath()
    {
        $this->R->setPath('/path/to/page');
        $this->assertEquals(
            '/path/to/page', $this->R->getPath(),
            'Unable to set path on Request object'
        );
    }
    
    public function testRequestDefaultsToIndexPath()
    {
        $this->assertEquals(
            '/', $this->R->getPath(),
            'Path does not default to index ("/").'
        );
    }
    
    public function testRequestShouldBeAbleToSetMethod()
    {
        $this->R->setMethod('POST');
        $this->assertEquals(
            'POST', $this->R->getMethod(),
            'Unable to set method on Request object'
        );
    }
    
    public function testRequestShouldDefaultToGetMethodOnInitialization()
    {
        $this->assertEquals(
            'GET', $this->R->getMethod(),
            'Method does not default to GET on Initialization'
        );
    }
    
    public function testShouldBeAbleToSetContent()
    {
        $this->R->setContent(array('bar'=>'foo'));
        $this->assertEquals(
            array('bar'=>'foo'), $this->R->getContent(),
            'Unable to set content for Request object'
        );
    }
    
    public function testSettingRequestParameters()
    {
        $this->R->setParams(array('foo' => 'bar', 'fruit' => 'apple'));
        $params = $this->R->getParams();
        $this->assertEquals(
            'bar', $params['foo'],
            'Foo param in request params not found'
        );
        $this->assertEquals(
            'apple', $params['fruit'],
            'Fruit param in request params not found'
        );
    }
    
    public function testHeaderFieldNamesShouldBeCaseInsensitive()
    {
        $this->R->setHeader('SoMe-fiEld-Name', 'Foo Bar');
        $this->assertEquals(
            'Foo Bar', $this->R->getHeader('somE-Field-nAmE'),
            'Field names for headers should be case-insensitive.'
        );
    }
    
    public function testMultipleSettingOfHeaderFields()
    {
        $this->R->setHeaders(array(
            'A-Field' => 'goo',
            'Another-Field' => 'bar',
            'Some-Other-Field' => 'car'
        ));
        $headers = $this->R->getHeaders();
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
    
    public function testHeaderFieldNamesShouldBeConvertedToDashedCamelCase()
    {
        $this->R->setHeader('afield', 'foo');
        $this->R->setHeader('yet-aNotHEr-Field', 'bar');
        $this->R->setHeader('And-yet-anOther', 'jar');
        $headers = $this->R->getHeaders();
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
    
    public function testRequestShouldBeAbleToSetAcceptHeader()
    {
        $this->R->setHeader('Accept', 'text/plain');
        $this->assertEquals(
        	'text/plain', $this->R->getHeader('Accept'),
        	'Unable to set content-type.'
    	);
    }
    
    public function testRequestShouldDefaultToHtmlContentType()
    {
        $this->assertEquals(
            'text/html', $this->R->getHeader('Accept'),
            'Content-type does not default to "text/html" on initialization.'
        );
    }
    
    public function testSettingRequestHeaders()
    {
        // Some common HTTP Request headers...
        $headers = array(
            'User-Agent'      => 'Mozilla/5.0',
            'Accept'          => 'text/html',
            'Accept-Charset'  => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'Accept-Language' => 'tl,en-us;q=0.7,en;q=0.3',
            'Accept-Encoding' => 'gzip,deflate',
            'Host'            => 'somehost.com'
        );
        $this->R->setHeaders($headers);
        foreach ($headers as $key => $value)
        {
            $this->assertEquals(
                $value, $this->R->getHeader($key),
                "Unable to obtain $key request header."
            );
        }
    }
}

