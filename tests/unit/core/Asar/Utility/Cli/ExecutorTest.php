<?php
require_once realpath(dirname(__FILE__) . '/../../../../../config.php');

use \Asar\Utility\Cli\Executor;
use \Asar\Utility\Cli\Command;

class Asar_Utility_Cli_ExecutorTest extends PHPUnit_Framework_TestCase {

  function setUp() {
    $this->cli = new Executor;
  }
  
  function mockTaskList($methods = array()) {
    return $this->getMock(
      'Asar\Utility\Cli\CliInterface',
      array_merge($methods, array('setController', 'getTaskNamespace'))
    );
  }
  
  function testInvokingTaskMethodThroughExecute() {
    $tasks = $this->mockTaskList(array('taskCreateProjectDirectories'));
    $tasks->expects($this->once())
      ->method('taskCreateProjectDirectories')
      ->with($this->equalTo('adirectory'));
    $this->cli->registerTasks($tasks);
    $this->cli->execute(new Command(array(
      'caller'    => '/yo',
      'command'   => 'create-project-directories',
      'arguments' => array('adirectory')
    )));
  }
  
  function testInvokingDuplicateTaskMethods() {
    $tasks1 = $this->mockTaskList(array('taskDummyTask'));
    $tasks2 = $this->mockTaskList(array('taskDummyTask'));
    $tasks1->expects($this->never())
      ->method('taskDummyTask');
    $tasks2->expects($this->once())
      ->method('taskDummyTask')
      ->with($this->equalTo('adirectory'));
    $this->cli->registerTasks($tasks1);
    $this->cli->registerTasks($tasks2);
    $this->cli->execute(new Command(array(
      'caller'    => '/yo',
      'command'   => 'dummy-task',
      'arguments' => array('adirectory')
    )));
  }
  
  function testInvokingTaskMethodWithNameSpace() {
    $tasks = $this->mockTaskList(array('taskCreateProjectDirectories'));
    $tasks->expects($this->once())
      ->method('taskCreateProjectDirectories')
      ->with($this->equalTo('adirectory'));
    $this->cli->registerTasks($tasks, 'foo');
    $this->cli->execute(new Command(array(
      'caller'    => '/yo',
      'namespace' => 'foo',
      'command'   => 'create-project-directories',
      'arguments' => array('adirectory')
    )));
  }
  
  function testInvokingTaskMethodWithoutNamespaceThrowsUndefined() {
    $this->setExpectedException(
		  'Asar\Utility\Cli\Exception\UndefinedTask'
	  );
    $tasks = $this->mockTaskList(array('taskCreateProjectDirectories'));
    $tasks->expects($this->never())
      ->method('taskCreateProjectDirectories');
    $this->cli->registerTasks($tasks, 'foo');
    $this->cli->execute(new Command(array(
      'caller'    => '/yo',
      'command'   => 'create-project-directories',
      'arguments' => array('adirectory')
    )));
  }
  
  function testThrowAsarUtilityCliExceptionWhenTaskMethodIsNotDefined() {
    $this->setExpectedException(
		  'Asar\Utility\Cli\Exception\UndefinedTask',
		  "The task method 'taskSomethingToDoButCannotDo' is not defined."
	  );
	  $this->cli->execute(new Command(array(
	    'caller'  => '/a',
	    'command' => 'something-to-do-but-cannot-do',
	    'arguments' => 'arg1'
	  )));
  }
  
  function testInvokingFlagMethodThroughExecute() {
    $tasks = $this->mockTaskList(array('flagDoSomething'));
    $tasks->expects($this->once())
      ->method('flagDoSomething');
    $this->cli->registerTasks($tasks);
    $this->cli->execute(new Command(array(
      'caller'    => '/yo',
      'flags'   => array('do-something'),
    )));
  }
  
  function testReturningRegisteredTasks() {
    $tasks1 = $this->mockTaskList(array('taskDummyTask1'));
    $tasks2 = $this->mockTaskList(array('taskDummyTask2'));
    $tasks3 = $this->mockTaskList(array('taskDummyTask3'));
    $tasks4 = $this->mockTaskList(array('taskDummyTask4', 'taskDummyTask5'));
    $this->cli->registerTasks($tasks1);
    $this->cli->registerTasks($tasks2, null);
    $this->cli->registerTasks($tasks3, 'foo');
    $this->cli->registerTasks($tasks4, 'bar');
    $this->assertEquals(
      array(
        'dummy-task1', 'dummy-task2', 'foo:dummy-task3',
        'bar:dummy-task4', 'bar:dummy-task5'
      ),
      $this->cli->getRegisteredTasks()
    );
  }
}
