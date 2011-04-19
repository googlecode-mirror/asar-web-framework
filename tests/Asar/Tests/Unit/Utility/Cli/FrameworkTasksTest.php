<?php

namespace Asar\Tests\Unit\Utility\Cli;

require_once realpath(dirname(__FILE__) . '/../../../../../config.php');

use \Asar\Utility\Cli\FrameworkTasks;
use \Asar\Utility\Cli\CliInterface;
use \Asar\File;
use \Asar\FileHelper\Exception\FileAlreadyExists;
use \Asar\FileHelper\Exception\DirectoryAlreadyExists;
use \Asar;

class FrameworkTasksTest extends \Asar\Tests\TestCase {

  function setUp() {
    $this->dir = '/foo';
    $this->file_helper = $this->getMock('Asar\FileHelper');
    $this->tasks = new FrameworkTasks($this->file_helper);
    $this->controller = $this->getMock(
      'Asar\Utility\Cli', array(), array(), '', false
    );
    $this->controller->expects($this->any())
      ->method('getWorkingDirectory')
      ->will($this->returnValue($this->dir));
    $this->tasks->setController($this->controller);
    $this->htaccess_contents = 
      "<IfModule mod_rewrite.c>\n" .
      "RewriteEngine On\n".
      "RewriteBase /\n".
      "RewriteCond %{REQUEST_FILENAME} !-f\n".
      "RewriteCond %{REQUEST_FILENAME} !-d\n".
      "RewriteRule . /index.php [L]\n".
      "</IfModule>\n";
  }
  
  private function mockTask() {
    $methods = func_get_args();
    if (count($methods) === 0) {
      $methods = array('taskCreateDirectory');
    }
    return $this->getMock(
      'Asar\Utility\Cli\FrameworkTasks', $methods, array(),
      '', false
    );
  }
  
  private function controllerOutsAt($i, $output) {
    return $this->controller->expects($this->at($i))
      ->method('out')
      ->with($output);
  }
  
  function testBaseTasksImplementsCLIInterface() {
    $this->assertTrue(
      $this->tasks instanceof CliInterface,
      'BaseTasks does not implement Asar\Utility\Cli\CliInterface.'
    );
  }
  
  private function fileHelperCreate() {
    return $this->file_helper->expects($this->once())
      ->method('create');
  }
  
  private function controllerOut($string) {
    return $this->controller->expects($this->once())
      ->method('out')
      ->with($string);
  }
  
  private function getFullPath() {
    $subpaths = func_get_args();
    return call_user_func_array(
      array($this, 'constructPath'),
      array_merge(array($this->dir), $subpaths)
    );
  }
  
  private function constructPath() {
    $subpaths = func_get_args();
    return rtrim(implode(DIRECTORY_SEPARATOR, $subpaths), DIRECTORY_SEPARATOR);
  }
  
  function testCreateFile() {
    $path = 'afile.txt';
    $contents = "The contents of the file. Hehehe.";
    $this->fileHelperCreate()
      ->with($this->getFullPath($path), $contents)
      ->will($this->returnValue(new File));
    $this->controllerOut('Created: afile.txt');
    $this->tasks->taskCreateFile( $path, $contents );
  }
  
  function testCreateFileWhenFileExists() {
    $filename = 'adifferentfile.txt';
    $this->fileHelperCreate()
      ->will($this->throwException(new FileAlreadyExists));
    $this->controllerOut("Skipped - File exists: $filename");
    $this->tasks->taskCreateFile( $filename, 'foo');
  }
  
  function testCreateDirectory() {
    $dir = 'adirectory';
    $this->file_helper->expects($this->once())
      ->method('createDir')
      ->with($this->getFullPath($dir))
      ->will($this->returnValue(TRUE));
    $this->controllerOut("Created: " . $dir);
    try {
      $this->tasks->taskCreateDirectory( $dir);
    } catch (Exception $e) {
      $this->fail(
        'Task was unable to catch expected exception ' . get_class($e)
      );
    }
  }
  
  function testCreateDirectoryWhenDirectoryExists() {
    $subpath = 'some-directory';
    $this->file_helper->expects($this->once())
      ->method('createDir')
      ->with($this->getFullPath($subpath))
      ->will($this->throwException(new DirectoryAlreadyExists));
    $this->controllerOut("Skipped - Directory exists: $subpath");
    try {
      $this->tasks->taskCreateDirectory($subpath);
    } catch (Exception $e) {
      $this->fail(
        'Task was unable to catch expected exception ' . get_class($e)
      );
    }
  }
  
  function testCreateFileAndDirectory() {
    $path = $this->constructPath('folder', 'thefile.txt');
    $contents = 'Foo Bar.';
    $this->file_helper->expects($this->at(0))
      ->method('createDir')
      ->with($this->getFullPath(dirname($path)));
    $this->file_helper->expects($this->at(1))
      ->method('create')
      ->with($this->getFullPath($path), $contents);
    $this->tasks->taskCreateFileAndDirectory($path, $contents);
  }
  
  function testCreatingProjectDirectories() {
    $project_path = 'xdir';
    $directories = array(
      '', // $project_path
      'apps', 'lib', 'lib/vendor', 'web', 'tests', 'logs'
    );
    $i = 0;
    foreach ($directories as $dir) {
      $path = $this->getFullPath($project_path, $dir);
      $this->file_helper->expects($this->at($i))
        ->method('createDir')
        ->with($path);
      $i++;
    }
    $this->tasks->taskCreateProjectDirectories('xdir');
  }
  
  function testCreatingProjectDirectoriesCallsCreateDirectory() {
    $tasks = $this->mockTask();
    $project_path = 'xdir';
    $directories = array(
      '', // $project_path
      'apps', 'lib', 'lib/vendor', 'web', 'tests', 'logs'
    );
    $i = 0;
    foreach ($directories as $dir) {
      $path = $this->constructPath($project_path, $dir);
      $tasks->expects($this->at($i))
        ->method('taskCreateDirectory')
        ->with($path);
      $i++;
    }
    $tasks->taskCreateProjectDirectories('xdir');
  }
  
  function testCreatingProjectDirectoriesCreatesAppDirectoryWhenSpecified() {
    $tasks = $this->mockTask();
    $project_path = 'adir';
    $app_path = 'TheApp';
    $directories = array(
      '', // $app_path
      'Resource', 'Representation'
    );
    $i = 7;
    foreach ($directories as $dir) {
      $path = $this->constructPath($project_path, 'apps', $app_path, $dir);
      $tasks->expects($this->at($i))
        ->method('taskCreateDirectory')
        ->with($path);
      $i++;
    }
    $tasks->taskCreateProjectDirectories('adir', 'TheApp');
  }
  
  function testCreatingHtaccessFileForProject() {
    $project_path = 'directory';
    $htaccess_path = $this->constructPath($project_path,'web', '.htaccess');
    $tasks = $this->_testCreatingAFile(
      $htaccess_path, $this->htaccess_contents
    );
    $tasks->taskCreateHtaccessFile($project_path);
  }
  
  function testCreatingTestConfigFile() {
    $tasks = $this->_testCreatingAFile(
      $this->constructPath(
        'thedirectory', 'tests', 'config.php'
      ),
      "<?php\n" .
      "ini_set('error_reporting', E_ALL | E_STRICT);\n" .
      "require_once realpath(dirname(__FILE__) . '/../lib/core/Asar.php');\n" .
      "\$__asar = Asar::getInstance();\n" .
      "\$__asar->getToolSet()->getIncludePathManager()->add(\n" .
      "  \$__asar->getFrameworkCorePath(),\n" .
      "  \$__asar->getFrameworkDevTestingPath(),\n" .
      "  \$__asar->getFrameworkExtensionsPath()\n" .
      ");\n" .
      "require_once 'Asar/EnvironmentScope.php';\n" .
      "require_once 'Asar/Injector.php';\n" .
      "if (!isset(\$_SESSION)) {\n" .
      "  \$_SESSION = array();\n" .
      "}\n" .
      "\$scope = new Asar_EnvironmentScope(\n" .
      "  \$_SERVER, \$_GET, \$_POST, \$_FILES, \$_SESSION, \$_COOKIE, " .
        "\$_ENV, getcwd()\n" .
      ");\n" .
      "Asar_Injector::injectEnvironmentHelperBootstrap(\$scope)->run();\n" .
      "Asar_Injector::injectEnvironmentHelper(\$scope)" .
        "->runTestEnvironment();\n" .
      "\n"
    );
    $tasks->taskCreateTestConfigFile('thedirectory');
  }
  
  function testCreatingProject() {
    $tasks = $this->mockTask(
      'taskCreateProjectDirectories', 'taskCreateApplicationConfig',
      'taskCreateResource', 'taskCreateFrontController', 'taskCreateBootstrap',
      'taskCreateHtaccessFile', 'taskCreateTasksFile',
      'taskCreateTestConfigFile'
    );
    $tasks->expects($this->at(0))
      ->method('taskCreateProjectDirectories')
      ->with('mydir', 'AnApp');
    $tasks->expects($this->once())
      ->method('taskCreateApplicationConfig')
      ->with('mydir', 'AnApp');
    $tasks->expects($this->once())
      ->method('taskCreateFrontController')
      ->with('mydir', 'AnApp');
    $tasks->expects($this->once())
      ->method('taskCreateHtaccessFile')
      ->with('mydir');
    $tasks->expects($this->once())
      ->method('taskCreateBootstrap')
      ->with('mydir');
    $tasks->expects($this->once())
      ->method('taskCreateTestConfigFile')
      ->with('mydir');
    $tasks->expects($this->once())
      ->method('taskCreateResource')
      ->with('mydir', 'AnApp', '/');
    $tasks->expects($this->once())
      ->method('taskCreateTasksFile')
      ->with('mydir');
    
    $tasks->taskCreateProject('mydir', 'AnApp');
  }
  
  
  function _testCreatingAFile($file, $contents, $creator = 'taskCreateFile') {
    $tasks = $this->mockTask($creator, 'taskCreateDirectory');
    $tasks->expects($this->once())
      ->method($creator)
      ->with($file, $contents);
    return $tasks;
  }
  
  function testCreateApplicationConfigFile() {
    $tasks = $this->_testCreatingAFile(
      $this->constructPath('thedir', 'apps', 'FooApp', 'Config.php'),
      "<?php\n" .
      "class FooApp_Config extends Asar_Config {\n" .
      "\n" .
      "  // Add configuration directives here...\n" .
      "  protected \$config = array(\n" .
      "    // e.g.:\n" .
      "    // 'use_templates' => false,\n" .
      "  );\n".
      "}\n"
    );
    $tasks->taskCreateApplicationConfig('thedir', 'FooApp');
  }
  
  /**
   * @dataProvider dataCreateResource
   */
  function testCreateResource(
    $project, $app, $url, $resource_name, $filepath, $contents = false
  ) {
    $full_resource_name = $app . '_Resource_' . $resource_name;
    if (!$contents) {
      $contents = 
        "  function GET() {\n".
        '    return "Hello from \'' . $url . '\'.";' . "\n" .
        "  }\n";
    }
    
    if ($project == '.') {
      $full_filepath = $this->constructPath(
        'apps', $app, 'Resource', $filepath . '.php'
      );
    } else {
      $full_filepath = $this->constructPath(
        $project, 'apps', $app, 'Resource', $filepath . '.php'
      );
    }
    $tasks = $this->_testCreatingAFile(
      $full_filepath,
      "<?php\n" .
      "class $full_resource_name extends Asar_Resource {\n" .
      "  \n" .
      $contents .
      "  \n" .
      "}\n"
    );
    $folders = explode('/', dirname($filepath));
    if (count($folders) > 0 && $folders[0] != '.') {
      $subpath = $this->constructPath($project, 'apps', $app, 'Resource');
      $i = 0;
      foreach ($folders as $folder) {
        $subpath .= DIRECTORY_SEPARATOR . $folder;
        $tasks->expects($this->at($i))
          ->method('taskCreateDirectory')
          ->with($subpath);
        $i++;
      }
    }
    $tasks->taskCreateResource($project, $app, $url);
  }
  
  function dataCreateResource() {
    return array(
      array(
        'project'       => 'project',
        'app'           => 'MyApp',
        'url'           => '/foo',
        'resource_name' => 'Foo',
        'filepath'      => 'Foo'
      ),
      array(
        'project'       => 'aproject',
        'app'           => 'AnApp',
        'url'           => '/',
        'resource_name' => 'Index',
        'filepath'      => 'Index'
      ),
      array(
        'project'       => 'foo',
        'app'           => 'BarApp',
        'url'           => '/foo/bar',
        'resource_name' => 'Foo_Bar',
        'filepath'      => 'Foo/Bar'
      ),
      array(
        'project'       => 'foo',
        'app'           => 'BarApp',
        'url'           => '/foo/bar/baz',
        'resource_name' => 'Foo_Bar_Baz',
        'filepath'      => 'Foo/Bar/Baz'
      ),
      array(
        'project'       => 'foo',
        'app'           => 'BarApp',
        'url'           => '/foo/{title}/baz',
        'resource_name' => 'Foo_RtTitle_Baz',
        'filepath'      => 'Foo/RtTitle/Baz',
        'contents'      => 
          "  function GET() {\n" .
          "    \$path = \$this->getPathComponents();\n" .
          "    return \"Hello from '/foo/{\$path['title']}/baz'.\";\n" .
          "  }\n" .
          "  \n" .
          "  function qualify(\$path) {\n" .
          "    // run your path validation here...\n" .
          "    return \n" .
          "      preg_match('/.+/', \$path['title']);\n" .
          "  }\n"
      ),
      array(
        'project'       => 'foo',
        'app'           => 'BarApp',
        'url'           => '/{foo}/{title}/baz',
        'resource_name' => 'RtFoo_RtTitle_Baz',
        'filepath'      => 'RtFoo/RtTitle/Baz',
        'contents'      => 
          "  function GET() {\n" .
          "    \$path = \$this->getPathComponents();\n" .
          "    return \"Hello from '/{\$path['foo']}/{\$path['title']}/baz'.\";\n" .
          "  }\n" .
          "  \n" .
          "  function qualify(\$path) {\n" .
          "    // run your path validation here...\n" .
          "    return \n" .
          "      preg_match('/.+/', \$path['foo']) &&\n" .
          "      preg_match('/.+/', \$path['title']);\n" .
          "  }\n"
      )
    );
  }
  
  function testCreatingResourceInProjectContext() {
    call_user_func_array(
      array($this, 'testCreateResource'),
      array(
        'project'       => '.',
        'app'           => 'TestApp',
        'url'           => '/foo/bar',
        'resource_name' => 'Foo_Bar',
        'filepath'      => 'Foo/Bar'
      )
    );
  }
  
  function testCreateProjectBootstrapFile() {
    $asar_arc_class_path = $this->constructPath(
      Asar::getInstance()->getFrameworkCorePath(), 'Asar.php'
    );
    $tasks = $this->_testCreatingAFile(
      $this->constructPath('thedir', 'bootstrap.php'),
      "<?php\n" .
      "// Change the path when appropriate\n" .
      "require_once realpath('$asar_arc_class_path');\n" .
      "\n" .
      "// This runs the whole bootsrap process inside a function\n" .
      "// so we don't pollute the global scope.\n" .
      "function _bootstrap() {\n" .
      "  // Prepares the include paths\n" .
      "  \$__asar = Asar::getInstance();\n" .
      "  \$__asar->getToolSet()->getIncludePathManager()->add(\n" .
      "    \$__asar->getFrameworkCorePath(),\n" .
      "    realpath(dirname(__FILE__) . '/apps')\n" .
      "  );\n" .
      "  require_once 'Asar/EnvironmentScope.php';\n" .
      "  require_once 'Asar/Injector.php';\n" .
      "  if (!isset(\$_SESSION)) {\n" .
      "    \$_SESSION = array();\n" .
      "  }\n" .
      "  if (!isset(\$argv)) {\n" .
      "    \$argv = array();\n" .
      "  }\n" .
      "  // Load the environment variables\n" .
      "  \$scope = new Asar_EnvironmentScope(\n" .
      "    \$_SERVER, \$_GET, \$_POST, \$_FILES, \$_SESSION, \$_COOKIE, \$_ENV, getcwd()\n" .
      "  );\n" .
      "  // Run initial bootstrap \n" .
      "  // We load the class loader here\n" .
      "  Asar_Injector::injectEnvironmentHelperBootstrap(\$scope)->run();\n" .
      "  return Asar_Injector::injectEnvironmentHelper(\$scope);\n" .
      "}\n" .
      "\n" .
      "return _bootstrap();\n"
    );
    $tasks->taskCreateBootstrap('thedir');
  }
  
  function testCreatingProjectCreatesFrontController() {
    $tasks = $this->_testCreatingAFile(
      $this->constructPath('thedir', 'web', 'index.php'),
      "<?php\n" .
      "\$env_helper = require realpath(dirname(__FILE__) . '/../bootstrap.php');\n" .
      "\$env_helper->runAppInProductionEnvironment('TheApp');\n"
    );
    $tasks->taskCreateFrontController('thedir', 'TheApp');
  }
  
  function testCreatingProjectCreatesTaskFile() {
    $cli = $this->_testCreatingAFile(
      $this->constructPath('project', 'tasks.php'),
      "<?php\n" .
      "\n" .
      "// This is a sample task\n" .
      "class MySampleTaskList implements Asar_Utility_Cli_Interface {\n" .
      "\n" .
      "  private \$controller;\n" .
      "\n" .
      "  function setController(Asar_Utility_Cli \$controller) {\n" .
      "    \$this->controller = \$controller;\n" .
      "  }\n" .
      "\n" .
      "  // You can call this task on the command line e.g.:\n" .
      "  // asarwf mysample:say-hello\n" .
      "  function taskSayHello() {\n" .
      "    //echo \"Hello World!\";\n" .
      "    \$this->controller->out(\"Hello World!\");\n" .
      "  }\n" .
      "\n" .
      "  function getTaskNamespace() {\n" .
      "    return 'mysample';\n" .
      "  }\n" .
      "\n" .
      "}\n" 
    );
    $cli->taskCreateTasksFile('project');
  }
  
}
