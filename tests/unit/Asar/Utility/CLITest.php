<?php
require_once realpath(dirname(__FILE__) . '/../../../config.php');

class Asar_Utility_CLITest extends Asar_Test_Helper {

  private $htaccess_contents;

  function setUp() {
    // This is so we put all created files in the temporary directory
    chdir(self::getTempDir());
    $this->cli = new Asar_Utility_CLI;
    $this->htaccess_contents = 
      "<IfModule mod_rewrite.c>\n" .
      "RewriteEngine On\n".
      "RewriteBase /\n".
      "RewriteCond %{REQUEST_FILENAME} !-f\n".
      "RewriteCond %{REQUEST_FILENAME} !-d\n".
      "RewriteRule . /index.php [L]\n".
      "</IfModule>\n";
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
    foreach ($commands as $method => $args) {
      $cli = $this->getMock('Asar_Utility_CLI', array('__call'));
      $command = array_shift($args);
      $cli->expects($this->any())
        ->method('__call')
        ->with(
          $this->equalTo($method),
          $this->equalTo($args)
        );
      $cli->execute(array_merge(
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
  
  function testCreatingProjectDirectoriesCreatesAppDirectoryWhenSpecified() {
    $this->cli->taskCreateProjectDirectories('adir', 'TheApp');
    $app_path = Asar::constructPath(
      self::getTempDir() . 'adir', 'apps', 'TheApp'
    );
    $directories = array(
      '', 'Resource', 'Representation'
    );
    foreach ($directories as $directory) {
      $this->assertFileExists(Asar::constructPath($app_path, $directory));
    }
  }
  
  private function _testHtAccess($project_dir) {
    // Create Project Directory
    mkdir(Asar::constructPath(
      self::getTempDir(), $project_dir
    ));
    mkdir(Asar::constructPath(
      self::getTempDir(), $project_dir, 'web'
    ));
    ob_start(); // Suppress output
    // The task to test
    $this->cli->taskCreateHtaccessFile($project_dir);
    ob_end_clean();
    // The expected file
    return Asar::constructPath(
      self::getTempDir(), $project_dir, 'web', '.htaccess'
    );
  }
  
  function testCreatingHtaccessFileForProject() {
    $this->assertFileExists(
      $this->_testHtAccess('directory')
    );
  }
  
  function testCreatingHtaccessFileForProjectWithProperContents() {
    $this->assertEquals(
      $this->htaccess_contents,
      file_get_contents($this->_testHtAccess('another-directory'))
    );
  }
  
  function testCreateFile() {
    $path = self::getTempDir() . 'afile.txt';
    $contents = "The path to the file. Hehehe.";
    ob_start();
    $this->cli->taskCreateFile( $path, $contents );
    $feedback = ob_get_clean();
    $this->assertFileExists($path);
    $this->assertSame("\nCreated: /afile.txt", $feedback);
    $this->assertEquals($contents, file_get_contents($path));
  }
  
  function testCreatingHtaccessUsesCreateFileTask() {
    $cli = $this->getMock('Asar_Utility_CLI', array('taskCreateFile'));
    $cli->expects($this->once())
      ->method('taskCreateFile')
      ->with(
        Asar::constructPath(
          self::getTempDir(), 'thedirectory', 'web', '.htaccess'
        ), $this->htaccess_contents
      );
    $cli->taskCreateHtaccessFile('thedirectory');
  }
  
  function testThrowAsarUtilityCLIExceptionWhenTaskMethodIsNotDefined() {
    try {
      $this->cli->taskSomethingToDoButCannotDo();
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
  
  function testCreatingProject() {
    $cli = $this->getMock(
      'Asar_Utility_CLI', array(
        'taskCreateProjectDirectories', 'taskCreateApplication'
      )
    );
    $cli->expects($this->once())
      ->method('taskCreateProjectDirectories')
      ->with($this->equalTo('mydir'), $this->equalTo('AnApp'));
    $cli->expects($this->once())
      ->method('taskCreateApplication')
      ->with(
        $this->equalTo('mydir'), $this->equalTo('AnApp')
      );
    $cli->taskCreateProject('mydir', 'AnApp');
  }
  
  function testCreateApplicationFile() {
    $cli = $this->getMock('Asar_Utility_CLI', array('taskCreateFile'));
    $cli->expects($this->once())
      ->method('taskCreateFile')
      ->with(
        $this->equalTo(Asar::constructPath(
          self::getTempDir(), 'thedir', 'apps', 'TheApp', 'Application.php'
        )),
        $this->equalTo(
          "<?php\n" .
          "class TheApp_Application extends Asar_Application {\n" .
          "  \n".
          "}\n"
        )
      );
    $cli->taskCreateApplication('thedir', 'TheApp');
  }
  
  function testCreateController() {
    $cli = $this->getMock(
      'Asar_Utility_CLI', array('taskCreateFile')
    );
    $cli->expects($this->once())
      ->method('taskCreateFile')
      ->with(
        $this->equalTo(Asar::constructPath(
          self::getTempDir(), 'project', 'apps', 'MyApp', 'Resource',
          'Foo.php'
        )),
        $this->equalTo(
          "<?php\n" .
          "class MyApp_Resource_Foo extends Asar_Resource {\n" .
          "  \n" .
          "  function GET() {\n".
          "    \n" .
          "  }\n" .
          "}\n"
        )
      );
    $cli->taskCreateResource('project', 'MyApp', 'Foo');
  }
  
  function testCreatingProjectCreatesFrontController() {
    $this->markTestIncomplete();
    $cli = $this->getMock(
      'Asar_Utility_CLI', array('taskCreateProjectDirectories')
    );
    $cli->expects($this->once())
      ->method('taskCreateProjectDirectories')
      ->with($this->equalTo('adir'));
    $cli->taskCreateProject('adir', 'MyApp');
  }
  
}
