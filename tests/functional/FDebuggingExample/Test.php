<?php
require_once realpath(dirname(__FILE__) . '/../../config.php');
set_include_path(get_include_path() . PATH_SEPARATOR . dirname(realpath(__FILE__)));

class FDebuggingExample_Test extends Asar_Test_Helper {
  
  function setUp() {
    Asar::setMode(Asar::MODE_DEBUG);
    $this->client = new Asar_Client;
    $this->app = new DebuggingExample_Application;
    $this->client->setServer($this->app);
  }
  
  function tearDown() {
    Asar::setMode(Asar::MODE_DEVELOPMENT);
  }
  
  function testDebugInformationForHtmlRequest() {
    $content = $this->client->GET('/')->getContent();
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
    
  }
  
}
