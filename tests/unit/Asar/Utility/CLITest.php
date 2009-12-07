<?php
require_once realpath(dirname(__FILE__) . '/../../../config.php');

class Asar_Utility_CLITest extends Asar_Test_Helper {

  function setUp() {
    chdir(self::getTempDir());
    $this->cli = new Asar_Utility_CLI;
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

  function testExecutePassesArgumentsToInterpret() {
    $this->cli = $this->getMock( 
      'Asar_Utility_CLI', array('interpret')
    );
    $arguments = array(
      '/cli/ui/front', '--version'
    );
    $this->cli->expects($this->once())
      ->method('interpret')
      ->with($this->equalto($arguments));
    $this->cli->execute($arguments);
  }
  
  function testGettingVersion() {
    $this->assertEquals(
      'Asar Web Framework ' . Asar::getVersion(),
      $this->cli->execute(array(
        '/a/cli/front-controller', '--version'
      ))
    );
  }
  
  function testCreatingProjectDirectoriesThroughExecute() {
    $this->cli = $this->getMock(
      'Asar_Utility_CLI', array('taskCreateProjectDirectories')
    );
    $this->cli->expects($this->once())
      ->method('taskCreateProjectDirectories')
      ->with($this->equalTo('adirectory'));
    $this->cli->execute(array(
      '/yo', 'create-project-directories', 'adirectory'
    ));
  }
  
  function testExecuteCallsCallMagicMethodForCommands() {
    $commands = array(
      'taskSomethingToDo' => array(
        'something-to-do', 'directory'
      ),
      'taskSomething' => array(
        'something', 'arg1', 'arg2'
      ),
      'taskAnotherThing' => array(
        'another-thing', 'arg1', 'arg2', '--flag1'
      )
    );
    $this->cli = $this->getMock('Asar_Utility_CLI', array('__call'));
    foreach ($commands as $method => $args) {
      $command = array_shift($args);
      $this->cli->expects($this->any())
        ->method('__call')
        ->with(
          $this->equalTo($method),
          $this->equalTo($args)
        );
      $this->cli->execute(array_merge(
        array('/su', $command), $args)
      );
    }
  }
  
  function testCreatingProjectDirectories() {
    $this->cli->taskCreateProjectDirectories('adir');
    $project_path = self::getTempDir() . 'adir';
    $directories = array(
      $project_path,
      Asar::constructPath($project_path, 'apps'),
      Asar::constructPath($project_path, 'vendor'),
      Asar::constructPath($project_path, 'web'),
      Asar::constructPath($project_path, 'tests'),
      Asar::constructPath($project_path, 'logs')
    );
    foreach ($directories as $directory) {
      $this->assertFileExists($directory);
    }
  }
  
}
