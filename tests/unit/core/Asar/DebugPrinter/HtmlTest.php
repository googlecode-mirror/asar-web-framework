<?php

require_once realpath(dirname(__FILE__). '/../../../../config.php');

class Asar_DebugPrinter_HtmlTest extends PHPUnit_Framework_TestCase {
  
  private $html = "<html>\n<head>\n</head>\n<body>\n</body>\n</html>";
  
  function setUp() {
    $this->debug = new Asar_Debug;
    $this->printer = new Asar_DebugPrinter_Html;
  }
  
  function testDebugContent() {
    $this->assertTag(
      array(
        'tag' => 'div', 'id' => 'asarwf_debug_info'
      ),
      $this->printer->printDebug($this->debug, $this->html)
    );
  }
  
  function testDebugWithEmptyContent() {
    $this->assertTag(
      array(
        'tag' => 'div', 'id' => 'asarwf_debug_info'
      ),
      $this->printer->printDebug($this->debug, '')
    );
  }
  
  function testPrinterMakesTable() {
    $this->assertTag(
      array(
        'tag' => 'table', 'parent' => array(
          'tag' => 'div', 'id' => 'asarwf_debug_info'
        )
      ),
      $this->printer->printDebug($this->debug, $this->html)
    );
  }
  
  function testPrinterAddsDebugDataToTable() {
    $this->debug->set('alpha', 1);
    $this->debug->set('beta', 'two');
    $this->debug->set('gamma', array('one', 'two', 'three'));
    $this->assertTag(
      array(
        'tag'     => 'th',
        'content' => 'alpha',
        'parent'  => array(
          'tag'    => 'tr', 
          'child'  => array( 'tag' => 'td', 'content' => '1'),
          'parent' => array(
            'tag' => 'tbody', 'parent' => array(
              'tag' => 'table', 'parent' => array(
                'tag' => 'div', 'id' => 'asarwf_debug_info'
              )
            )
          )
        )
      ),
      $this->printer->printDebug($this->debug, $this->html)
    );
  }
  
  function testPrinterAddsDebugDataToTable2() {
    $this->debug->set('alpha', 1);
    $this->debug->set('beta', 'two');
    $this->assertTag(
      array(
        'tag' => 'th',
        'content' => 'beta',
        'parent' => array(
          'tag' => 'tr', 
          'child' => array(
            'tag' => 'td',
            'content' => 'two'
          ),
          'parent' => array(
            'tag' => 'tbody', 'parent' => array(
              'tag' => 'table', 'parent' => array(
                'tag' => 'div', 'id' => 'asarwf_debug_info'
              )
            )
          )
        )
      ),
      $this->printer->printDebug($this->debug, $this->html)
    );
  }
  
  function testPrinterAddsArrayDataAsUnorderedList() {
    $this->debug->set('alpha', 1);
    $this->debug->set('beta', 'two');
    $this->debug->set('gamma', array('one', 'two', 'three'));
    $this->assertTag(
      array(
        'tag'     => 'th',
        'content' => 'gamma',
        'parent'  => array(
          'tag'    => 'tr', 
          'child'  => array( 
            'tag'   => 'td', 
            'child' => array(
              'tag' => 'ul',
              'children' => array(
                'count' => 3
              ),
              'child' => array(
                'tag' => 'li', 'content' => 'two'
              )
            )
          )
        )
      ),
      $this->printer->printDebug($this->debug, $this->html)
    );
  }
  
  function testPrinterAddsAssociativeArrayDataAsDefinitionList() {
    $this->debug->set(
      'delta', 
      array(
        'one'   => '1',
        'two'   => 'duo',
        'three' => 'trez'
      )
    );
    $this->assertTag(
      array(
        'tag'     => 'th',
        'content' => 'delta',
        'parent'  => array(
          'tag'    => 'tr', 
          'child'  => array( 
            'tag'   => 'td', 
            'child' => array(
              'tag'   => 'table',
              'child' => array(
                'tag'      => 'tbody',
                'children' => array('count' => 3),
                'child'    => array(
                  'tag'   => 'tr',
                  'child' => array(
                    'tag'        => 'th',
                    'content'    => 'three',
                    'attributes' => array('scope' => 'row'),
                  )
                )
                
              )
            )
          )
        )
      ),
      $this->printer->printDebug($this->debug, $this->html)
    );
  }
  
}