<?php
require_once realpath(dirname(__FILE__) . '/../../../../../config.php');

class Asar_Utility_Cli_InterpreterTest extends PHPUnit_Framework_TestCase {

  function setUp() {
    $this->interpreter = new \Asar\Utility\Cli\Interpreter;
  }
  
  /**
   * @dataProvider dataInterpretingCommands
   */
  function testInterpretingCommands($arguments, $expected) {
    $this->assertEquals(
      $expected, $this->interpreter->interpret($arguments)
    );
  }
  
  function dataInterpretingCommands() {
    return array(
      array(
        array(
          '/the/cli/caller', '--flag1', '--flag2', 'the-command', 'arg1', 'arg2'
        ),
        new \Asar\Utility\Cli\Command(array(
          'caller'    => '/the/cli/caller',
          'flags'     => array('flag1', 'flag2'),
          'command'   => 'the-command',
          'arguments' => array('arg1', 'arg2')
        ))
      ),
      
      array(
        array(
          '/the/cli/callerx', '--flag1', 'a-command'
        ),
        new \Asar\Utility\Cli\Command(array(
          'caller'    => '/the/cli/callerx',
          'flags'     => array('flag1'),
          'command'   => 'a-command',
          'arguments' => array()
        ))
      ),
      
      array(
        array(
          '/another/caller', 'mycommand', 'arg1', 'arg2'
        ),
        new \Asar\Utility\Cli\Command(array(
          'caller'    => '/another/caller',
          'flags'     => array(),
          'command'   => 'mycommand',
          'arguments' => array('arg1', 'arg2')
        ))
      ),
      
      array(
        array(
          '/another/caller', 'mycommand', '--command-flag', 'arg2'
        ),
        new \Asar\Utility\Cli\Command(array(
          'caller'    => '/another/caller',
          'flags'     => array(),
          'command'   => 'mycommand',
          'arguments' => array('--command-flag', 'arg2')
        ))
      ),
      
      array(
        array(
          '/caller', 'nspace:mycommand', 'arg1', 'arg2'
        ),
        new \Asar\Utility\Cli\Command(array(
          'caller'    => '/caller',
          'flags'     => array(),
          'namespace' => 'nspace',
          'command'   => 'mycommand',
          'arguments' => array('arg1', 'arg2')
        ))
      )
      
    );
  }
  
  
}
