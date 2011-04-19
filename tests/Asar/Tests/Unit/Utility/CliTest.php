<?php

namespace Asar\Tests\Unit\Utility;

require_once realpath(dirname(__FILE__) . '/../../../../config.php');

use \Asar\Utility\Cli;
use \Asar\Utility\Cli\Command;

class CliTest extends \Asar\Tests\TestCase {

  function setUp() {
    // This is called to help the reflector
    $this->getMock('Asar\Utility\Cli\CliInterface');
    $this->interpreter = $this->getMock('Asar\Utility\Cli\Interpreter');
    $this->executor = $this->getMock(
      'Asar\Utility\Cli\Executor\ExecutorInterface'
    );
    $this->dir = realpath(dirname(__FILE__));
    $this->cli = new Cli($this->interpreter, $this->executor, $this->dir);
  }
  
  function mock($methods = array()) {
    return $this->getMock('Asar\Utility\Cli', $methods, array(), '', false);
  }
  
  function mockTaskList($methods = array()) {
    return $this->getMock(
      'Asar\Utility\Cli\CliInterface',
      array_merge($methods, array('setController', 'getTaskNamespace'))
    );
  }

  function testExecutePassesArgumentsToInterpreter() {
    $arguments = array(
      '/cli/ui/front', '--aflag'
    );
    $command = new Command(array());
    $this->interpreter->expects($this->once())
      ->method('interpret')
      ->with($this->equalto($arguments))
      ->will($this->returnValue($command));
    $this->cli->execute($arguments);
  }
  
  function testExecutePassesCommandFromInterpreterToExecutor() {
    $command = new Command(array());
    $this->interpreter->expects($this->once())
      ->method('interpret')
      ->will($this->returnValue($command));
    $this->executor->expects($this->once())
      ->method('execute')
      ->with($command);
    $this->cli->execute(array());
  }
  
  function testRegisterPassesItselfToTaskListAsController() {
    $tasks = $this->mockTaskList();
    $tasks->expects($this->once())
      ->method('setController')
      ->with($this->equalTo($this->cli));
    $this->executor->expects($this->once())
      ->method('registerTasks')
      ->with($tasks);
    $this->cli->register($tasks);
  }
  
  function testRegisterPassesNamespaceToExecutorRegisterTasks() {
    $tasks = $this->getMock('Asar\Utility\Cli\CliInterface');
    $tasks->expects($this->once())
      ->method('getTaskNamespace')
      ->will($this->returnValue('foo'));
    $this->executor->expects($this->once())
      ->method('registerTasks')
      ->with($tasks, 'foo');
    $this->cli->register($tasks);
  }
  
  function testOutput() {
    $test_string  = 'Foo bar.';
    $test_string2 = 'Bar foo.';
    ob_start();
    $this->cli->out($test_string);
    $this->cli->out($test_string2);
    $out = ob_get_clean();
    $this->assertEquals("$test_string\n$test_string2\n", $out);
  }
  
  function testGetRegisteredTasks() {
    $tasks = array('foo', 'bar');
    $this->executor->expects($this->once())
      ->method('getRegisteredTasks')
      ->will($this->returnValue($tasks));
    $this->assertEquals($tasks, $this->cli->getRegisteredTasks());
  }
  
  function testGettingWorkingDirectory() {
    $this->assertEquals(
      $this->dir, $this->cli->getWorkingDirectory()
    );
  }
}
