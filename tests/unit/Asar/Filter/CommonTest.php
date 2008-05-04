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

	function testHtmlDebugMessageMustBeProperlyContainedInHtmlBody()
	{
		Asar::debug('testdebugkey', 'testmessage');
		$result = Asar_Filter_Common::filterResponse($this->response)->getContent();
		$ending_body_tag_pos = strpos($result, '</body>');
		$debug_div_tag_pos = strpos($result, '<div id="asar_debug">');
		$this->assertFalse(strpos($result, '</body>', $ending_body_tag_pos + 2) > 0, 'Duplicate ending body tag!');
		$this->assertTrue(strpos($result, '</html>') > $debug_div_tag_pos, 'Ending html tag should be after debug div!');
		$this->assertTrue($ending_body_tag_pos > $debug_div_tag_pos, 'Debug div should occur before body tag');
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
    
    function testNoDebugInfoShouldAppearWhenOnProductionMode()
    {
        Asar::debug('testdebugkeya', 'testmessagea');
        Asar::setMode(Asar::MODE_PRODUCTION);
        $content = Asar_Filter_Common::filterResponse($this->response)->getContent();
        $this->assertNotContains('testdebugkeya', $content, 'Debug key should not be found when on production mode');
        $this->assertNotContains('testmessagea', $content, 'Debug message should not be found when on production mode');
    }
    
    function testRequestFilterShouldSetTxtMimeTypeWhenResourceEndsWithATxtFileExtensionType()
    {
        /**
         * @todo There could be aproblem with resource names ending in file-extensions but are not intended to invoke something like that
         */
        $request = new Asar_Request;
        $request->setPath('/index.txt');
        Asar_Filter_Common::filterRequestTypeNegotiation($request);
        $this->assertEquals('txt', $request->getType(), 'The type should be "txt"');
        $this->assertEquals('/index', $request->getPath(), 'The path should be "/index"');
    }
    
    function testRequestFilterShouldNotSetEmptyStringWhenOriginalResourcePathIsJustASlash()
    {
        $request = new Asar_Request;
        $request->setPath('/');
        Asar_Filter_Common::filterRequestTypeNegotiation($request);
        $this->assertEquals('/', $request->getPath(), 'The path should be "/index"');
    }
    
    function testRequestFilterShouldSetRootPathWhenResourcePathIsNotSet()
    {
        $request = new Asar_Request;
        Asar_Filter_Common::filterRequestTypeNegotiation($request);
        $this->assertEquals('/', $request->getPath(), 'The path should be "/index"');
    }
    
}