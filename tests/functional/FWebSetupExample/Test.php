<?php
require_once realpath(dirname(__FILE__) . '/../../config.php');

class FWebSetupExample_Test extends PHPUnit_Framework_TestCase
{
     private $can_connect_to_test_server = null;
    
    public function setUp()
    {
        if (!$this->_isCanConnectToTestServer())
            $this->markTestSkipped('Unable to connect to test server. Check server setup.');
        $this->client = new Asar_Client;
        Asar_Test_Server::setUp(array(
            'path' =>  Asar::constructRealPath(dirname(__FILE__), 'web')
        ));
        $this->client->setServer('http://asar-test.local/');
    }
    
    // TODO: move this somewhere else to be more reusable
    // Copied from Asar_Unit_ClientHttpTest
    private function _isCanConnectToTestServer()
    {
        if (is_null($this->can_connect_to_test_server)) {
            $this->can_connect_to_test_server = false;
            Asar_Test_Server::setUp(array('fixture' => 'normal'));
            $fp = @fsockopen('asar-test.local', 80, $errno, $errstr, 30);
            if (!$fp) {
                return false;
            } else {
                $out = "GET / HTTP/1.1\r\n";
                $out .= "Host: asar-test.local\r\n";
                $out .= "Connection: Close\r\n\r\n";
                fwrite($fp, $out);
                $test = stream_get_contents($fp);
                if (strpos($test,'<h1>This is the Great HTML</h1>') > 0) {
                    $this->can_connect_to_test_server = true;
                }
            }
            fclose($fp);
        }
        return $this->can_connect_to_test_server;
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
