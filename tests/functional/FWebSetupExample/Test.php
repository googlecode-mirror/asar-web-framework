<?php
require_once realpath(dirname(__FILE__) . '/../../config.php');

class FWebSetupExample_Test extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->client = new Asar_Client;
        Asar_Test_Server::setUp(array(
            'path' =>  Asar::constructRealPath(dirname(__FILE__), 'web')
        ));
        $this->client->setServer('http://asar-test.local/');
    }
    
    public function testRegularRequest()
    {
        $response = $this->client->GET('/');
        $contents = $response->getContent();
        $this->assertContains(
            '<html>', $contents,
            'Contents were not properly sent.'
        );
        $this->assertContains(
            'Hello World!', $contents,
            'Expected string was not found on output.'
        );
        $this->assertEquals(
            'text/html', $response->getHeader('Content-Type'),
            'Application did not set the content-type header properly.'
        );
    }
    
}
