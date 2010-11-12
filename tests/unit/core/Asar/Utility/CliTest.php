<?php

require_once realpath(dirname(__FILE__) . '/../../../../config.php');

class Asar_Utility_CliTest extends PHPUnit_Framework_TestCase {

  function setUp() {
    // This is called to help the reflector
    $this->getMock('Asar_Utility_Cli_Interface');
    $this->interpreter = $this->getMock('Asar_Utility_Cli_Interpreter');
    $this->executor = $this->getMock(
      'Asar_Utility_Cli_Executor_Interface'
    );
    $this->dir = realpath(dirname(__FILE__));
    $this->cli = new Asar_Utility_Cli($this->interpreter, $this->executor, $this->dir);
  }
  
  function mock($methods = array()) {
    return $this->getMock('Asar_Utility_Cli', $methods, array(), '', false);
  }
  
  function mockTaskList($methods = array()) {
    return $this->getMock(
      'Asar_Utility_Cli_Interface',
      array_merge($methods, array('setController', 'getTaskNamespace'))
    );
  }

  function testExecutePassesArgumentsToInterpreter() {
    $arguments = array(
      '/cli/ui/front', '--aflag'
    );
    $command = new Asar_Utility_Cli_Command(array());
    $this->interpreter->expects($this->once())
      ->method('interpret')
      ->with($this->equalto($arguments))
      ->will($this->returnValue($command));
    $this->cli->execute($arguments);
  }
  
  function testExecutePassesCommandFromInterpreterToExecutor() {
    $command = new Asar_Utility_Cli_Command(array());
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
    $tasks = $this->getMock('Asar_Utility_Cli_Interface');
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
