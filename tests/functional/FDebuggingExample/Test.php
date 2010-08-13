<?php
require_once realpath(dirname(__FILE__) . '/../../config.php');
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(realpath(__FILE__)));

class FDebuggingExample_Test extends PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->markTestIncomplete();
    if (!Asar_Test_Server::isCanConnectToTestServer()) {
      $this->markTestSkipped(
        'Unable to connect to test server. Check server setup.'
      );
    }
    $this->client = new Asar_Client;
    Asar_Test_Server::setUp(array(
      'path' =>  Asar::constructRealPath(dirname(__FILE__), 'web')
    ));
    $this->client->setServer('http://asar-test.local/');
  }
  
  function tearDown() {
    #Asar::setMode(Asar::MODE_DEVELOPMENT);
  }
  
  function testDebugInformationForHtmlRequest() {
    $content = $this->client->GET('/')->getContent();
    //var_dump($content);
    // Test if the setup is okay...
    $this->assertContains('Debugging Tests', $content);
    $this->assertContains('<title', $content);
    
    $html = new Asar_Utility_XML($content);
    $debug = $html->getElementById('asarwf_debug_info');
    $this->assertNotNull(
      $debug, 'Debug info table element in html output not found.'
    );
    // Execution Time
    $this->assertEquals(
      'Execution Time', $debug->tbody->tr[0]->th->stringValue(),
      'Unable to find execution time label in debug info table.'
    );
    $this->assertRegExp(
      '/[0-9]+.[0-9]{2}ms/', $debug->tbody->tr[0]->td->stringValue(),
      'Unable to find execution time value in debug info table.'
    );
    
    // Memory Used
    /*
    function echo_memory_usage() {
        $mem_usage = memory_get_usage(true);
       
        if ($mem_usage < 1024)
            echo $mem_usage." bytes";
        elseif ($mem_usage < 1048576)
            echo round($mem_usage/1024,2)." kilobytes";
        else
            echo round($mem_usage/1048576,2)." megabytes";
           
        echo "<br/>";
    } 
    */
    $this->assertEquals(
      'Memory Used', $debug->tbody->tr[1]->th->stringValue(),
      'Unable to find memory usage label in debug info table.'
    );
    $this->assertRegExp(
      '/[0-9]+.[0-9]{2}(M|K)B/', $debug->tbody->tr[1]->td->stringValue(),
      'Unable to find memory usage value in debug info table.'
    );
    
    // Application
    $this->assertEquals(
      'Application', $debug->tbody->tr[2]->th->stringValue(),
      'Unable to find application name label in debug info table.'
    );
    $this->assertEquals(
      'DebuggingExample', $debug->tbody->tr[2]->td->stringValue(),
      'Unable to find application name value in debug info table.'
    );
    
    // Resource
    $this->assertEquals(
      'Resource', $debug->tbody->tr[3]->th->stringValue(),
      'Unable to find resource name label in debug info table.'
    );
    $this->assertEquals(
      'DebuggingExample_Resource_Index', $debug->tbody->tr[3]->td->stringValue(),
      'Unable to find resource name value in debug info table.'
    );
    
    // Templates
    $this->assertEquals(
      'Templates Used', $debug->tbody->tr[4]->th->stringValue(),
      'Unable to find templates section label in debug info table.'
    );
    $this->assertEquals(
      new Asar_Utility_XML(
        '<ul><li>'. 
        realpath(dirname(__FILE__) . '/DebuggingExample/Representation/Index.GET.html.php') .
        '</li><li>'.
        realpath(dirname(__FILE__) . '/DebuggingExample/Representation/Layout.html.php') .
        '</li></ul>'
      ),
      $debug->tbody->tr[4]->td->ul,
      'Unable to find list of templates used in debug info table.'
    );
  }
  
}
