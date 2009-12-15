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
  
  function mock($methods = array()) {
    return $this->getMock('Asar_Utility_CLI', $methods);
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
  
  function testCreatingProjectDirectoriesThroughExecute() {
    $this->cli = $this->mock(array('taskCreateProjectDirectories'));
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
      $cli = $this->mock(array('__call'));
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
  
  function testCreateFile() {
    $path = self::getTempDir() . 'afile.txt';
    $contents = "The contents of the file. Hehehe.";
    ob_start();
    $this->cli->taskCreateFile( $path, $contents );
    $feedback = ob_get_clean();
    $this->assertSame("\nCreated: afile.txt", $feedback);
  }
  
  function testCreateFileWhenFileExists() {
    $subpath = 'adifferentfile.txt';
    $path = self::getTempDir() . $subpath;
    $contents = "Foo Hehehe.";
    Asar_File::create($path)->write($contents)->save();
    ob_start();
    $this->cli->taskCreateFile( $subpath, $contents );
    $feedback = ob_get_clean();
    $this->assertFileExists($path);
    $this->assertSame(
      "\nSkipped - File exists: $subpath", $feedback
    );
  }
  
  private function _createDirectory($subpath) {
    $path = self::getTempDir() . $subpath;
    ob_start();
    $this->cli->taskCreateDirectory($path);
    return array('feedback' => ob_get_clean(), 'full_path' => $path);
  }
  
  function testCreateDirectory() {
    $subpath = 'adirectory';
    $data = $this->_createDirectory($subpath);
    $this->assertFileExists($data['full_path']);
    $this->assertSame("\nCreated: " . $subpath, $data['feedback']);
  }
  
  function testCreateDirectoryWhenDirectoryExists() {
    $subpath = 'some-directory';
    mkdir(self::getTempDir() . $subpath);
    $data = $this->_createDirectory($subpath);
    $this->assertSame(
      "\nSkipped - Directory exists: $subpath", $data['feedback']
    );
  }
  
  function testCreateFileAndDirectory() {
    $cli = $this->mock(array('taskCreateDirectory', 'taskCreateFile'));
    $path = Asar::constructPath(
      self::getTempDir(), 'folder', 'thefile.txt'
    );
    $contents = 'Foo Bar.';
    $cli->expects($this->at(0))
      ->method('taskCreateDirectory')
      ->with($this->equalTo(dirname($path)));
    $cli->expects($this->at(1))
      ->method('taskCreateFile')
      ->with($this->equalTo($path), $this->equalTo($contents));
    $cli->taskCreateFileAndDirectory($path, $contents);
  }
  
  function testCreatingProjectDirectories() {
    $project_path = self::getTempDir() . 'xdir';
    $directories = array(
      '', // $project_path
      'apps', 'lib', 'lib/vendor', 'web', 'tests', 'logs'
    );
    ob_start();
    $this->cli->taskCreateProjectDirectories('xdir');
    ob_end_clean();
    foreach ($directories as $directory) {
      $this->assertFileExists(Asar::constructPath($project_path, $directory));
    }
  }
  
  function testCreatingProjectDirectoriesUsesCreateDirectory() {
    $cli = $this->mock(array('taskCreateDirectory'));
    $project_path = self::getTempDir() . 'adir';
    $directories = array(
      '', // $project_path
      'apps', 'lib', 'lib/vendor', 'web', 'tests', 'tests/data', 
      'tests/functional', 'tests/unit', 'logs'
    );
    $i = 0;
    foreach ($directories as $directory) {
      $cli->expects($this->at($i))
        ->method('taskCreateDirectory')
        ->with(
          $this->equalTo(Asar::constructPath($project_path, $directory))
        );
      $i++;
    }
    $cli->taskCreateProjectDirectories('adir');
  }
  
  function testCreatingProjectDirectoriesCreatesAppDirectoryWhenSpecified() {
    $app_path = Asar::constructPath(
      self::getTempDir() . 'adir', 'apps', 'TheApp'
    );
    $directories = array(
      '', 'Resource', 'Representation'
    );
    ob_start();
    $this->cli->taskCreateProjectDirectories('adir', 'TheApp');
    ob_end_clean();
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
  
  function testCreatingHtaccessUsesCreateFileTask() {
    $cli = $this->mock(array('taskCreateFile'));
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
    $cli = $this->mock(array(
      'taskCreateProjectDirectories', 'taskCreateApplication',
      'taskCreateResource', 'taskCreateFrontController',
      'taskCreateHtaccessFile', 'taskCreateTasksFile'
    ));
    $cli->expects($this->at(0))
      ->method('taskCreateProjectDirectories')
      ->with($this->equalTo('mydir'), $this->equalTo('AnApp'));
    $cli->expects($this->once())
      ->method('taskCreateApplication')
      ->with( $this->equalTo('AnApp'), $this->equalTo('mydir') );
    $cli->expects($this->once())
      ->method('taskCreateFrontController')
      ->with( $this->equalTo('mydir'), $this->equalTo('AnApp') );
    $cli->expects($this->once())
      ->method('taskCreateHtaccessFile')
      ->with( $this->equalTo('mydir') );
    $cli->expects($this->once())
      ->method('taskCreateTasksFile')
      ->with( $this->equalTo('mydir'), $this->equalTo('AnApp') );
    $cli->expects($this->once())
      ->method('taskCreateResource')
      ->with(
        $this->equalTo('/'), $this->equalTo('AnApp'), $this->equalTo('mydir')
      );
    $cli->taskCreateProject('mydir', 'AnApp');
  }
  
  function _testCreatingAFile($file, $contents ) {
    return $this->_testCreatingMultipleFiles(array($file => $contents));
  }
  
  function _testCreatingMultipleFiles(array $files, $use_alternative = false ) {
    $create_method = $use_alternative ? 
      'taskCreateFileAndDirectory' : 'taskCreateFile';
    $cli = $this->mock(array($create_method));
    $i = 0;
    foreach ($files as $file => $contents) {
      $cli->expects($this->at($i))
        ->method($create_method)
        ->with( $this->equalTo($file), $this->equalTo($contents) );
      $i++;
    }
    return $cli;
  }
  
  function testCreateApplicationFile() {
    $cli = $this->_testCreatingAFile(
      Asar::constructPath(
        self::getTempDir(), 'thedir', 'apps', 'TheApp', 'Application.php'
      ),
      "<?php\n" .
      "class TheApp_Application extends Asar_Application {\n" .
      "  \n".
      "}\n"
    );
    $cli->taskCreateApplication('TheApp', 'thedir');
  }
  
  function testCreateResource(array $data = array()) {
    if (empty($data)) {
      $data = array(
        'project' => 'project',
        'app' => 'MyApp',
        'url' => '/foo',
        'expected_resource_name' => 'Foo',
        'expected_resource_path' => 'Foo'
      );
    }
    $names  = explode('_', str_replace('__', '_|', $data['expected_resource_name']));
    $levels = explode('/', $data['expected_resource_path']);
    $files = array();
    $current_path = Asar::constructPath(
      self::getTempDir(), $data['project'], 'apps', $data['app'], 'Resource'
    );
    $current_name = $data['app'] . "_Resource";
    for ($i = 0; $i < count($names); $i++) {
      $current_path .= DIRECTORY_SEPARATOR . $levels[$i];
      $current_name .= '_' . str_replace('|', '_', $names[$i]);
      $files[$current_path . '.php'] = 
        "<?php\n" .
        "class " . $current_name . " extends Asar_Resource {\n" .
        "  \n" .
        "  function GET() {\n".
        "    \n" .
        "  }\n" .
        "  \n" .
        "}\n";
    }
    $cli = $this->_testCreatingMultipleFiles($files, true);
    if (array_key_exists('project_context', $data) && $data['project_context']) {
      $cli->taskCreateResource($data['url']);
    } else {
      $cli->taskCreateResource($data['url'], $data['app'], $data['project']);
    }
  }
  
  function testCreateResourceIndex() {
    $this->testCreateResource(array(
      'project' => 'aproject',
      'app' => 'AnApp',
      'url' => '/',
      'expected_resource_name' => 'Index',
      'expected_resource_path' => 'Index'
    ));
  }
  
  function testCreateResourceMultiLevelPath() {
    $this->testCreateResource(array(
      'project' => 'foo',
      'app' => 'BarApp',
      'url' => '/foo/bar/baz',
      'expected_resource_name' => 'Foo_Bar_Baz',
      'expected_resource_path' => 'Foo/Bar/Baz'
    ));
  }
  
  function testCreateResourceMultiLevelPathWithWildCard() {
    $this->testCreateResource(array(
      'project' => 'foo',
      'app' => 'BarApp',
      'url' => '/foo/*/baz',
      'expected_resource_name' => 'Foo__Item_Baz',
      'expected_resource_path' => 'Foo/_Item/Baz'
    ));
  }
  
  public function testCreatingResourceInProjectContext() {
    Asar_File::create('tasks.php')
      ->write('<?php $main_app = "TestApp";')
      ->save();
    $this->testCreateResource(array(
      'project' => '',
      'app' => 'TestApp',
      'url' => '/foo/bar',
      'expected_resource_name' => 'Foo_Bar',
      'expected_resource_path' => 'Foo/Bar',
      'project_context' => true
    ));
  }
  
  function testCreatingProjectCreatesFrontController() {
    $cli = $this->_testCreatingAFile(
      Asar::constructPath(
        self::getTempDir(), 'mydir', 'web', 'index.php'
      ),
      "<?php\n" .
      "set_include_path(\n" .
      "  realpath(dirname(__FILE__) . '/../apps') . PATH_SEPARATOR .\n" .
      "  realpath(dirname(__FILE__) . '/../vendor') . PATH_SEPARATOR .\n" .
      "  get_include_path()\n" .
      ");\n" .
      "require_once 'Asar.php';\n" .
      "Asar::start('MyApp');\n"
    );
    $cli->taskCreateFrontController('mydir', 'MyApp');
  }
  
  function testCreatingProjectCreatesTaskFile() {
    $cli = $this->_testCreatingAFile(
      Asar::constructPath(
        self::getTempDir(), 'project', 'tasks.php'
      ),
      "<?php\n" .
      '$main_app = \'FooApp\';' . "\n"
    );
    $cli->taskCreateTasksFile('project', 'FooApp');
  }
  
}
