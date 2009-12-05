<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Asar_ClientHttpTest extends Asar_Test_Helper
{
    
    private $can_connect_to_test_server = null;
    
    public function setUp()
    {
        $this->client  = new Asar_Client;
        $this->server  = 'http://asar-test.local';
        $this->client->setServer($this->server);
        if (!$this->_isCanConnectToTestServer())
            $this->markTestSkipped('Unable to connect to test server.');
        Asar_Test_Server::setUp(array('fixture' => 'normal'));
    }
    
    private function _isCanConnectToTestServer()
    {
        if (is_null($this->can_connect_to_test_server)) {
            $this->can_connect_to_test_server = false;
            Asar_Test_Server::setUp(array('fixture' => 'normal'));
            $fp = fsockopen('asar-test.local', 80, $errno, $errstr, 30);
            if (!$fp) {
                /*echo
                    'Could not connect to asar-test.local. Check server setup. ' .
                    "$errstr ($errno)\n\n";*/
            } else {
                $out = "GET / HTTP/1.1\r\n";
                $out .= "Host: asar-test.local\r\n";
                $out .= "Connection: Close\r\n\r\n";
                fwrite($fp, $out);
                $test = stream_get_contents($fp);
                if (strpos($test,'<h1>This is the Great HTML</h1>') > 0) {
                    $this->can_connect_to_test_server = true;
                } else {
                    /*echo 'Was able to connect to asar-test.local but did not ' .
                        "find test fixtures. Please check server setup.\n\n";*/
                }
            }
            fclose($fp);
        }
        return $this->can_connect_to_test_server;
    }
    
    public function testClientShouldSetServer()
    {
        $this->assertEquals(
            $this->server, $this->client->getServer(),
            'The client was not able to set the current server.'
        );
    }
    
    public function testSetServerRemovesTrailingSlashes()
    {
        $server = $this->client->setServer('http://asar-test.local/');
        $this->assertEquals(
            $this->server, $this->client->getServer(),
            'The client was not able to set the current server.'
        );
    }
    
    public function testGetShouldReturnTheCorrectResponse()
    {
        $this->client->setServer($this->server);
        $response = $this->client->GET('/');
        $this->assertEquals(
            200, $response->getStatus(),
            'Requesting did not return the expected response status.'
        );
        $this->assertEquals(
            'text/html', $response->getHeader('Content-Type'),
            'Requesting did not return the expected content type.'
        );
        $this->assertContains(
            'This is the Great HTML', $response->getContent(),
            'Requesting did not return the expected content.'
        );
    }
    
    public function testMakeRawHttpRequestString()
    {
        $R = new Asar_Request;
        $headers = array('Accept' => 'text/html', 'Connection' => 'Close' );
        $R->setPath('/a/path/to/a/resource.html');
        foreach ($headers as $key => $value) {
            $R->setHeader($key, $value);
        }
        $str = $this->client->createRawHttpRequestString($R);
        $this->assertTrue(
            self::isStartsWith($str, "GET /a/path/to/a/resource.html HTTP/1.1\r\n"),
            'Did not find request line in generated Raw HTTP Request string.'
        );
        $this->_testHeaders($headers, $str);
        // Last characters should be \r\n\r\n
        $this->assertTrue(
            self::isEndsWith($str, "\r\n\r\n"),
            'Raw HTTP Request string should end in "\r\n\r\n".'
        );
    }
    
    protected function _testHeaders($headers, $str)
    {
        foreach ($headers as $key => $value) {
            $this->assertContains(
                "\r\n" . Asar_Utility_String::dashCamelCase($key) . 
                    ": $value\r\n", $str,
                "Did not find the $key header that was set."
            );
        }
    }
    
    public function testMakeRawHttpRequestStringRandomValues()
    {
        $R = new Asar_Request;
        $R->setMethod('POST');
        $headers = array();
        $rand = Asar_Utility_RandomStringGenerator::instance();
        
        // Generate random headers
        for ($i = 0; $i < 10; $i++) {
            $headers[ucwords($rand->getPhpLabel( mt_rand(4, 20) ))] =
                $rand->getAlphaNumeric( mt_rand(8, 40) );
        }
        $R->setPath('/path/to/a/post/processor');
        foreach ($headers as $key => $value) {
            $R->setHeader($key, $value);
        }
        $str = $this->client->createRawHttpRequestString($R);
        
        $this->assertEquals(
            self::isStartsWith($str, "POST /path/to/a/post/processor HTTP/1.1\r\n"),
            'Incorrect request line in generated Raw HTTP Request string.'
        );
        $this->_testHeaders($headers, $str);
    }
    
    public function testMakeRawHttpRequestStringWithPostValues()
    {
        $R = new Asar_Request;
        $R->setMethod('POST');
        $R->setPath('/post/processor');
        $R->setContent(array(
            'foo' => 'bar', 'goo[]' => 'jazz', 'good' => 'bad='
        ));
        $expected = 'foo=bar&' . urlencode('goo[]') . '=jazz&good=bad' .
            urlencode('=');
        $headers = array(
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Content-Length' => strlen($expected)
        );
        $str = $this->client->createRawHttpRequestString($R);
        $this->assertTrue(
            self::isStartsWith($str, "POST /post/processor HTTP/1.1\r\n"),
            'Incorrect request line in generated Raw HTTP Request string.'
        );
        $this->_testHeaders($headers, $str);
        $this->assertTrue(
            self::isEndsWith($str, "\r\n\r\n$expected"),
            'Raw HTTP Request string should end in "\r\n\r\n' . $expected . '".'
        );
    }
    
    public function testMakeRawHttpRequestStringWithPostValuesBetter()
    {
        $R = new Asar_Request;
        $R->setMethod('POST');
        $post = $this->createRandomKeyValuePairs();
        $R->setContent($post);
        $R->setPath('/a/post/processor');
        $expected = $this->createUrlEncodedParams($post);
        $headers = array(
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Content-Length' => strlen($expected)
        );
        
        $str = $this->client->createRawHttpRequestString($R);
        $this->assertTrue(
            self::isStartsWith($str, "POST /a/post/processor HTTP/1.1\r\n"),
            'Incorrect request line in generated Raw HTTP Request string.'
        );
        $this->_testHeaders($headers, $str);
        $this->assertContains(
            $expected, $str,
            'Expected POST content is not inside the RAW HTTP Request string.'
        );
        $this->assertTrue(
            self::isEndsWith($str, $expected),
            'Raw HTTP Request string should end in "\r\n\r\n' . $expected . '".'
        );
    }
    
    private function createRandomKeyValuePairs($length = 10) {
        $post = array();
        $rand = Asar_Utility_RandomStringGenerator::instance();
        // Generate random post key => value pairs
        // TODO:Include punctuations and special characters
        for ($i = 0; $i < $length; $i++) {
            $post[$rand->getLowercaseAlpha( mt_rand(4, 20) )] =
                $rand->getAlphaNumeric( mt_rand(8, 40) );
        }
        return $post;
    }
    
    private function createUrlEncodedParams($array)
    {
        $post_pairs = array();
        foreach($array as $key => $value) {
            $post_pairs[] = rawurlencode($key) . '=' . rawurlencode($value);
        }
        return implode('&', $post_pairs);
    }
    
    public function testMakeRawHttpRequestShouldHaveNoContent()
    {
        $R = new Asar_Request;
        $R->setPath('/a/get/path');
        $post = $this->createRandomKeyValuePairs();
        $R->setContent($post);
        $not_expected = $this->createUrlEncodedParams($post);
        
        $str = $this->client->createRawHttpRequestString($R);
        $this->assertTrue(
            self::isStartsWith($str, "GET /a/get/path HTTP/1.1\r\n"),
            'Incorrect request line in generated Raw HTTP Request string.'
        );
        $this->assertNotContains(
            "\r\nContent-Type: application/x-www-form-urlencoded\r\n", $str
        );
        $this->assertNotContains(
            "\r\nContent-Length: " . strlen($not_expected) . "\r\n", $str
        );
        $this->assertTrue(
            self::isEndsWith($str, "\r\n\r\n"),
            'Raw HTTP Request string should end in "\r\n\r\n".'
        );
    }
    
    public function testRequestHttpShouldSetHostHeader()
    {
        $R = new Asar_Request;
        $rand = Asar_Utility_RandomStringGenerator::instance();
        $host = $rand->getAlphaNumeric( mt_rand(3, 10) ) . '.' .
            $rand->getAlphaNumeric( mt_rand(3, 5) );
        $headers = array('Host' => $host);
        $this->client->setServer("http://$host");
        $str = $this->client->createRawHttpRequestString($R);
        $this->_testHeaders($headers, $str);
    }
    
    public function testRequestHttpShouldSetConnectionHeaderToClose()
    {
        $R = new Asar_Request;
        $headers = array('Connection' => 'Close');
        $str = $this->client->createRawHttpRequestString($R);
        $this->_testHeaders($headers, $str);
    }
    
    public function testRequestingPassesWhateverValueFromCrhrsToSrhr()
    {
        $client = $this->getMock(
            'Asar_Client', 
            array('createRawHttpRequestString', 'sendRawHttpRequest')
        );
        $R = array('yoyo');
        $client->expects($this->once())
            ->method('createRawHttpRequestString')
            ->will($this->returnValue($R));
        $client->expects($this->once())
            ->method('sendRawHttpRequest')
            ->with($this->identicalTo($R));
        $client->setServer('http://asar-test.local');
        $client->GET('/a/path/to/somewhere');
        // TODO: AssertSame no longer works properly on objects. Fix when fixed.
    }
    
    public function testSrhrDoesNotRunWhenCrhrsReturnsNull()
    {
        $client = $this->getMock(
            'Asar_Client', 
            array('createRawHttpRequestString', 'sendRawHttpRequest')
        );
        $client->expects($this->once())
            ->method('createRawHttpRequestString')
            ->will($this->returnValue(null));
        $client->expects($this->never())
            ->method('sendRawHttpRequest');
        $client->setServer('http://asar-test.local');
        $client->GET('/a');
    }
    
    public function getFirstArgument()
    {
        $args = func_get_args();
        $this->saveObject('arg', $args[0]);
    }
    
    public function testSendRawHttpRequest()
    {
        $rstr = "GET / HTTP/1.1\r\n" .
            "Host: asar-test.local\r\n" .
            "Connection: Close\r\n\r\n";
        $raw_response = $this->client->sendRawHttpRequest($rstr);
        $this->assertTrue(
            self::isStartsWith($raw_response, "HTTP/1.1 200 OK\r\n"),
            'Did not find Response Line.'
        );
        $headers = array('Content-Type' => 'text/html');
        $this->_testHeaders($headers, $raw_response);
        $this->assertContains(
            '<title>The Great HTML</title>', $raw_response);
    }
    
    public function testSendRawHttpRequestBetter()
    {   
        $tests = $this->_generateArrayTests(
            'index.html', 'form.html', 'schedule.xml', 'yellow-submarine.txt'
        );
        
        foreach ($tests as $path => $test_params) {
            $rstr = "GET $path HTTP/1.1\r\n" .
                "Host: asar-test.local\r\n" .
                "Connection: Close\r\n\r\n";
            $raw_response = $this->client->sendRawHttpRequest($rstr);
            $headers = $test_params['headers'];
            $this->_testHeaders($headers, $raw_response);
            $this->assertTrue(
                self::isStartsWith($raw_response, "HTTP/1.1 200 OK\r\n"),
                'Did not find Response Line.'
            );
            $this->assertTrue(
                self::isEndsWith(
                    $raw_response, "\r\n\r\n" . $test_params['content']
                ),
                'Did not find content at the end of the response.'
            );
        }
    }
    
    private function _generateArrayTests()
    {
        $args = func_get_args();
        $out = array();
        foreach ($args as $file) {
            $ctype = 'text/html';
            if (self::isEndsWith($file, '.txt'))
                $ctype = 'text/plain';
            elseif (self::isEndsWith($file, '.xml'))
                $ctype = 'application/xml';
            
            $out['/'.$file] = array(
                'headers' => array(
                    'Content-Type' => $ctype,
                    'Content-Length' => strlen(file_get_contents(
                        $this->_pathFor($file)
                    ))
                ),
                'content' => file_get_contents($this->_pathFor($file))
            );
        }
        return $out;
    }
    
    private function _pathFor($file)
    {
        return Asar::constructRealPath(
            Asar::getFrameworkPath(), 'tests', 'data',
            'test-server-fixtures', 'normal', $file
        );
    }
    
    public function testSendRawHttpRequestException($site = 'an.unknown.site')
    {
        try {
            $this->client->setServer("http://$site");
            $this->client->GET('/');
            $this->fail('Must raise exception when trying to connect to ' .
                " unknown site '$site'." );
        } catch (Asar_Client_Exception $e) {
            $this->assertEquals(
                "Unable to connect to $site:80.", $e->getMessage(),
                'Must raise the correct exception message.'
            );
        }
        
    }
    
    public function testRawHttpRequestsWithKnownSite($site = 'asar-test.local')
    {
        $this->client->setServer("http://$site");
        $this->client->GET('/');
    }
    
    public function testSendRawHttpRequestUsesClientSetServer()
    {
        $sites = array(
            'some.notknown.site', 'alksjdfpoewiu.somewhere', 'badabadadfraera',
            'google.com', 'localhost', 'asar-test.local'
        );
        foreach ($sites as $site) {
            $fp = @fsockopen($site, 80, $errno, $errstr, 30);
            if ($fp) {
                $this->testRawHttpRequestsWithKnownSite($site);
                fclose($fp);
            } else {
                $this->testSendRawHttpRequestException($site);
            }
        }
    }
    
    public function testExportRawHttpResponse($params = array())
    {
        $defaults = array(
             'content' => 'Hello World',
             'status'  => 200
        );
        extract(array_merge($defaults, $params));
        $clength = strlen($content);
        $raw = "HTTP/1.1 $status OK\r\n".
            "Date: Sat, 14 Nov 2009 18:31:11 GMT\r\n" .
            "Server: Apache/2.2.11\r\n" .
            "Last-Modified: Sat, 14 Nov 2009 06:32:48 GMT\r\n" .
            "ETag: \"181fc-198-4784ef1e5e400\"\r\n" .
            "Accept-Ranges: bytes\r\n" .
            "Content-Length: $clength\r\n" .
            "Vary: Accept-Encoding\r\n" .
            "Connection: close\r\n" .
            "Content-Type: text/plain\r\n\r\n" .
            $content;
        $R = $this->client->exportRawHttpResponse($raw);
        $this->assertTrue(
            $R instanceof Asar_Response,
            'ExportRawHttpResponse did not return an Asar_Response object.'
        );
        $this->assertEquals($status, $R->getStatus());
        $headers = $R->getHeaders();
        $this->assertEquals('text/plain', $headers['Content-Type']);
        $this->assertEquals($clength, $headers['Content-Length']);
        $this->assertEquals('Accept-Encoding', $headers['Vary']);
        $this->assertEquals('Apache/2.2.11', $headers['Server']);
        $this->assertEquals($content, $R->getContent());
    }
    
    public function testExportRawHttpResponseWithContentContainingCrlfSequence()
    {
        $params = array(
            'content' => "A Very Dangerous\r\nContent.\r\n\r\nReally."
        );
        $this->testExportRawHttpResponse($params);
    }
    
    public function testErhrWithADifferentStatusCode()
    {
        $this->testExportRawHttpResponse(array('status' => 201));
    }
    
    public function testRequestingPassesWhateverValueFromSrhrErhr()
    {
        $client = $this->getMock(
            'Asar_Client', array('sendRawHttpRequest', 'exportRawHttpResponse')
        );
        $R = array(1,2,3);
        $client->expects($this->once())
            ->method('sendRawHttpRequest')
            ->will($this->returnValue($R));
        $client->expects($this->once())
            ->method('exportRawHttpResponse')
            ->with($this->identicalTo($R));
        $client->setServer('http://asar-test.local');
        $client->GET('/a/path/to/somewhere');
        // TODO: AssertSame no longer works properly on objects. Fix when fixed.
    }
    
    public function testRequestingReturnsWhateverResultsFromErhr()
    {
        $client = $this->getMock('Asar_Client', array('exportRawHttpResponse'));
        $R = array('a' => 1, '2' => 'b');
        $client->expects($this->once())
            ->method('exportRawHttpResponse')
            ->will($this->returnValue($R));
        $client->setServer('http://asar-test.local');
        $this->assertSame(
            $R, $client->GET('/'),
            'Client did not use exportRawHttpResponse to output response.'
        );
    }
    
    /*
    
    
    public function testSendHttpRequestTxt()
    {
        $this->client->setServer($this->server);
        $response = $this->client->GET('/yellow-submarine.txt');
        $this->assertEquals(
            'text/plain', $response->getHeader('Content-Type'),
            'Requesting did not return the expected content type.'
        );
        $this->assertContains(
            'We all live in the yellow submarine', $response->getContent(),
            'Requesting did not return the expected content.'
        );
    }
    */    
}

