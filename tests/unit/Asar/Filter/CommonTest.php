<?php

require_once 'PHPUnit/Framework.php';
require_once 'Asar.php';

class Asar_Filter_CommonTest extends PHPUnit_Framework_TestCase {
    
    function setUp()
    {
        Asar::setMode(Asar::MODE_DEVELOPMENT);
        $this->response = new Asar_Response;
        $this->response->setContent('<html><head><title>The title</title></head><body><h1>The Body</h1></body></html>');
        $this->generator = Asar_Utility_RandomStringGenerator::instance();
    }
    
    function tearDown()
    {
        Asar::setMode(Asar::MODE_PRODUCTION);
        Asar::clearDebugMessages();
    }
    
    function testAddingDebugInfoForHtmlResponse()
    {
        Asar::debug('testdebugkey', 'testmessage');
        $result = Asar_Filter_Common::filterResponse($this->response);
        $this->assertContains('testdebugkey', $result->getContent(), 'Did not find debug key in response content');
        $this->assertContains('testmessage', $result->getContent(), 'Did not find debug message in response content');
    }
    
    function testAddingAnotherDebugInfoForHtmlResponse() {
        Asar::debug('yodel', 'deloy');
        $result = Asar_Filter_Common::filterResponse($this->response);
        $this->assertContains('yodel', $result->getContent(), 'Did not find debug key in response content');
        $this->assertContains('deloy', $result->getContent(), 'Did not find debug message in response content');
    }
    
    function testAddingRandomDebugInfoForHtmlResponse()
    {
        $key = $this->generator->getAlphaNumeric(10);
        $message = $this->generator->getAlphaNumeric(45);
        Asar::debug($key, $message);
        $result = Asar_Filter_Common::filterResponse($this->response);
        $this->assertContains($key, $result->getContent(), 'Did not find debug key in response content');
        $this->assertContains($message, $result->getContent(), 'Did not find debug message in response content');
    }
    
    function testDebugInfoForHtmlResponseShouldBeInTableForm()
    {
        $key = $this->generator->getAlphaNumeric(10);
        $message = $this->generator->getAlphaNumeric(45);
        Asar::debug($key, $message);
        $result = Asar_Filter_Common::filterResponse($this->response);
        $expected = <<<DEBUG
<div id="asar_debug">
    <h1>Asar Debugging Information</h1>
    <table id="asar_debug_table">
        <tr>
            <th scope="row">$key</th>
            <td>$message</td>
        </tr>
    </table>
</div>
</body>
DEBUG;
        $this->assertContains($expected, $result->getContent(), 'Did not find debug key and message in response content that is properly formatted');
    }
    
    function testDebugInfoForHtmlResponsMustProperlyEncodeValues()
    {
        $key = 'The <<&>>';
        $message = '< > Yada & Yoda';
        Asar::debug($key, $message);
        $result = Asar_Filter_Common::filterResponse($this->response)->getContent();
        $this->assertContains(htmlentities($key), $result, 'Did not properly encode debug key');
        $this->assertContains(htmlentities($message), $result, 'Did not properly encode debug message');
    }
    
    function testFormatAsUnorderedListDebugMessageWhenItIsArrayForHtmlResponse()
    {
        $key = $this->generator->getAlphaNumeric(10);
        $test_value = $this->generator->getAlphaNumeric(40);
        $message = array('ABCDEFG', 'Two', 'C', $test_value);
        Asar::debug($key, $message);
        $content = Asar_Filter_Common::filterResponse($this->response)->getContent();
        $this->assertContains('<ul>', $content, 'Did not find opening ul tag on message');
        $this->assertContains('</ul>', $content, 'Did not find ending ul tag on message');
        $start = strpos($content, '<ul>');
        $end   = strpos($content, '</ul>');
        $list = simplexml_load_string(substr($content, $start, ($end + 5 - $start)));
        $this->assertEquals(4, count($list->children()), 'list must have 4 elements');
        foreach ($list->li as $item) {
            $this->assertEquals('li', $item->getName(), 'Did not find list elements (li) in Message');
            $this->assertEquals(current($message), $item . '', 'Message list values are not formatted properly');
            next($message);
        }
    }
    
    function testHtmlVersionOfDebugInfoShouldOnlyAppearForHtmlResponses()
    {
        Asar::debug('testdebugkey', 'testmessage');
        $types = Asar_Message::getSupportedTypes();
        foreach ($types as $type => $mimetype) {
            $this->setUp();
            $this->response->setType($type);
            $content = Asar_Filter_Common::filterResponse($this->response)->getContent();
            if ($mimetype != 'text/html') {
                $this->assertNotContains('<div id="asar_debug">', $content, "HTML version of debug info was found for '$mimetype' response type");
            } else {
                $this->assertContains('<div id="asar_debug">', $content, 'HTML version of debug info was not found for \'text/html\' response type');
            }
        }
    }
}