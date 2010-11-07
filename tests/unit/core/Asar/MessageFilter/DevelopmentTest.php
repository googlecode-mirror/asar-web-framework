<?php
require_once realpath(dirname(__FILE__) . '/../../../../config.php');

class Asar_MessageFilter_DevelopmentTest extends PHPUnit_Framework_TestCase {
  
  private $html = "<html>\n<head>\n</head>\n<body>\n</body>\n</html>";
  
  function setUp() {
    $this->config = new Asar_Config(array());
    $this->filter = new Asar_MessageFilter_Development($this->config);
  }
  
  function testAddDebugInformation() {
    $response = new Asar_Response();
    $response->setContent($this->html);
    $this->markTestIncomplete();
    
    $response->setHeader('Asar-Internal', array('debug' => $debug));
  }
  
}