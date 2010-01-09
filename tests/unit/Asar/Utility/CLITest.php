<?php
require_once realpath(dirname(__FILE__) . '/../../../config.php');

class Asar_Utility_CLITest extends Asar_Test_Helper {

  function setUp() {
    // This is so we put all created files in the temporary directory
    $this->cli = Asar_Utility_CLI::instance();
  }
  
  function mock($methods = array()) {
    return $this->getMock('Asar_Utility_CLI', $methods, array(), '', false);
  }
  
  function mockTaskList($methods = array()) {
    return $this->getMock(
      'Asar_Utility_CLI_Interface',
      array_merge($methods, array('setController'))
    );
  }
  
  function testInterpretingCommands() {
    $arguments = array(
      '/the/cli/caller', '--flag1', '--flag2', 'the-command', 'arg1', 'arg2'
    );
    $this->assertEquals(
      array(
        'caller'    => '/the/cli/caller',
        'flags'     => array('flag1', 'flag2'),
        'command'   => 'the-command',
        'arguments' => array('arg1', 'arg2')
      ),
      $this->cli->interpret($arguments)
    );
  }
  
  function testInterpretingCommands2() {
    $arguments = array(
      '/the/cli/callerx', '--flag1', 'a-command'
    );
    $this->assertEquals(
      array(
        'caller'    => '/the/cli/callerx',
        'flags'     => array('flag1'),
        'command'   => 'a-command',
        'arguments' => array()
      ),
      $this->cli->interpret($arguments)
    );
  }
  
  function testInterpretingCommands3() {
    $arguments = array(
      '/another/caller', 'mycommand', 'arg1', 'arg2'
    );
    $this->assertEquals(
      array(
        'caller'    => '/another/caller',
        'flags'     => array(),
        'command'   => 'mycommand',
        'arguments' => array('arg1', 'arg2')
      ),
      $this->cli->interpret($arguments)
    );
  }
  
  function testInterpretingCommands4() {
    $arguments = array(
      '/another/caller', 'mycommand', '--command-flag', 'arg2'
    );
    $this->assertEquals(
      array(
        'caller'    => '/another/caller',
        'flags'     => array(),
        'command'   => 'mycommand',
        'arguments' => array('--command-flag', 'arg2')
      ),
      $this->cli->interpret($arguments)
    );
  }
  
  function testInterpretingCommands5() {
    $arguments = array(
      '/caller', 'nspace:mycommand', 'arg1', 'arg2'
    );
    $this->assertEquals(
      array(
        'caller'    => '/caller',
        'flags'     => array(),
        'namespace' => 'nspace',
        'command'   => 'mycommand',
        'arguments' => array('arg1', 'arg2')
      ),
      $this->cli->interpret($arguments)
    );
  }

  function testExecutePassesArgumentsToInterpret() {
    $this->cli = $this->mock(array('interpret'));
    $arguments = array(
      '/cli/ui/front', '--aflag'
    );
    $this->cli->expects($this->once())
      ->method('interpret')
      ->with($this->equalto($arguments));
    $this->cli->execute($arguments);
  }
  
  function testGettingVersion() {
    ob_start();
    $this->cli->execute(array('/a/cli/front-controller', '--version'));
    $output = ob_get_clean();
    $this->assertEquals(
      'Asar Web Framework ' . Asar::getVersion(), $output
    );
  }
  
  function testVersionIsNotReturnedWhenVersionFlagIsNotPresent() {
    $this->assertNotEquals(
      'Asar Web Framework ' . Asar::getVersion(),
      $this->cli->execute(array('/a/cli/front-controller'))
    );
  }
  
  // TODO: Think of a better alternative to 'Controller'
  function testRegisterPassesItselfToTaskListAsController() {
    $tasks = $this->mockTaskList();
    $tasks->expects($this->once())
      ->method('setController')
      ->with($this->equalTo($this->cli));
    $this->cli->register($tasks);
  }
  
  function testRegisterWithSettingNamespace() {
    $tasks1 = $this->mockTaskList(array('taskToDo'));
    $tasks1->expects($this->once())
      ->method('taskToDo')
      ->with($this->equalTo('arg'));
    $tasks2 = $this->mockTaskList(array('taskToDo'));
    $tasks2->expects($this->never())
      ->method('taskToDo');
    $this->cli->register($tasks1, 'anamespace');
    $this->cli->register($tasks2);
    $this->cli->execute(array(
      '/p', 'anamespace:to-do', 'arg'
    ));
  }
  
  function testInvokingTaskMethodThroughExecute() {
    $tasks = $this->mockTaskList(array('taskCreateProjectDirectories'));
    $tasks->expects($this->once())
      ->method('taskCreateProjectDirectories')
      ->with($this->equalTo('adirectory'));
    $this->cli->register($tasks);
    $this->cli->execute(array(
      '/yo', 'create-project-directories', 'adirectory'
    ));
  }
  
  function testInvokingDuplicateTaskMethods() {
    $tasks1 = $this->mockTaskList(array('taskDummyTask'));
    $tasks2 = $this->mockTaskList(array('taskDummyTask'));
    $tasks1->expects($this->never())
      ->method('taskDummyTask');
    $tasks2->expects($this->once())
      ->method('taskDummyTask')
      ->with($this->equalTo('adirectory'));
    $this->cli->register($tasks1);
    $this->cli->register($tasks2);
    $this->cli->execute(array(
      '/yo', 'dummy-task', 'adirectory'
    ));
  }
  
  function testThrowAsarUtilityCLIExceptionWhenTaskMethodIsNotDefined() {
    try {
      $this->cli->execute(array('/a', 'something-to-do-but-cannot-do', 'arg1'));
    } catch(Asar_Utility_CLI_Exception_UndefinedTask $e) {
      $this->assertEquals(
        "The task method 'taskSomethingToDoButCannotDo' is not defined.",
        $e->getMessage()
      );
      return;
    }
    $this->fail(
      'Did not raise expected exception ' .
      "'Asar_Utility_CLI_Exception_UndefinedTask'."
    );
  }
}
