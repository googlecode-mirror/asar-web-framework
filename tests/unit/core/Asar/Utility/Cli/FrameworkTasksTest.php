<?php
require_once realpath(dirname(__FILE__) . '/../../../../../config.php');

class Asar_Utility_Cli_FrameworkTasksTest extends PHPUnit_Framework_TestCase {

  function setUp() {
    $this->dir = '/foo';
    $this->file_helper = $this->getMock('Asar_FileHelper');
    $this->tasks = new Asar_Utility_Cli_FrameworkTasks($this->file_helper);
    $this->controller = $this->getMock(
      'Asar_Utility_Cli', array(), array(), '', false
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
      'Asar_Utility_Cli_FrameworkTasks', $methods, array(),
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
      $this->tasks instanceof Asar_Utility_CLI_Interface,
      'BaseTasks does not implement Asar_Utility_CLI_Interface.'
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
      ->will($this->returnValue(new Asar_File));
    $this->controllerOut('Created: afile.txt');
    $this->tasks->taskCreateFile( $path, $contents );
  }
  
  function testCreateFileWhenFileExists() {
    $filename = 'adifferentfile.txt';
    $this->fileHelperCreate()
      ->will($this->throwException(
        new Asar_FileHelper_Exception_FileAlreadyExists
      ));
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
    $this->tasks->taskCreateDirectory( $dir);
  }
  
  function testCreateDirectoryWhenDirectoryExists() {
    $subpath = 'some-directory';
    $this->file_helper->expects($this->once())
      ->method('createDir')
      ->with($this->getFullPath($subpath))
      ->will($this->throwException(
        new Asar_FileHelper_Exception_DirectoryAlreadyExists
      ));
    $this->controllerOut("Skipped - Directory exists: $subpath");
    $this->tasks->taskCreateDirectory($subpath);
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
      "  \$_SERVER, \$_GET, \$_POST, \$_FILES, \$_SESSION, \$_COOKIE, \$_ENV, getcwd()\n" .
      ");\n" .
      "Asar_Injector::injectEnvironmentHelperBootstrap(\$scope)->run();\n" .
      "Asar_Injector::injectEnvironmentHelper(\$scope)->runTestEnvironment();\n" .
      "\n"
    );
    $tasks->taskCreateTestConfigFile('thedirectory');
  }
  
  function testCreatingProject() {
    $tasks = $this->mockTask(
      'taskCreateProjectDirectories', 'taskCreateApplicationConfig',
      'taskCreateResource', 'taskCreateFrontController',
      'taskCreateHtaccessFile', 'taskCreateTasksFile',
      'taskCreateTestConfigFile'
    );
    $tasks->expects($this->at(0))
      ->method('taskCreateProjectDirectories')
      ->with('mydir', 'AnApp');
    $tasks->expects($this->once())
      ->method('taskCreateApplicationConfig')
      ->with('mydir', 'AnApp');
    /*$tasks->expects($this->once())
      ->method('taskCreateFrontController')
      ->with( $this->equalTo('mydir'), $this->equalTo('AnApp') );*/
    $tasks->expects($this->once())
      ->method('taskCreateHtaccessFile')
      ->with('mydir');
    $tasks->expects($this->once())
      ->method('taskCreateTestConfigFile')
      ->with('mydir');
    $tasks->expects($this->once())
      ->method('taskCreateResource')
      ->with('mydir', 'AnApp', '/');
    /*$tasks->expects($this->once())
      ->method('taskCreateTasksFile')
      ->with( $this->equalTo('mydir'), $this->equalTo('AnApp') );
    */
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
      $this->constructPath('thedir', 'apps', 'TheApp', 'Config.php'),
      "<?php\n" .
      "class TheApp_Config extends Asar_Config {\n" .
      "\n" .
      "  // Add configuration directives here...\n" .
      "  protected \$config = array(\n" .
      "    // e.g.:\n" .
      "    // 'use_templates' => false,\n" .
      "  );\n".
      "}\n"
    );
    $tasks->taskCreateApplicationConfig('thedir', 'TheApp');
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
  
  function testCreatingProjectCreatesFrontController() {
    $this->markTestIncomplete();
    $cli = $this->_testCreatingAFile(
      Asar::constructPath(
        self::getTempDir(), 'mydir', 'web', 'index.php'
      ),
      "<?php\n" .
      "set_include_path(\n" .
      "  realpath(dirname(__FILE__) . '/../apps') . PATH_SEPARATOR .\n" .
      "  realpath(dirname(__FILE__) . '/../lib/vendor') . PATH_SEPARATOR .\n" .
      "  get_include_path()\n" .
      ");\n" .
      "require_once 'Asar.php';\n" .
      "Asar::start('MyApp');\n"
    );
    $cli->taskCreateFrontController('mydir', 'MyApp');
  }
  
  function testCreatingProjectCreatesTaskFile() {
    $this->markTestIncomplete();
    $cli = $this->_testCreatingAFile(
      Asar::constructPath(
        self::getTempDir(), 'project', 'tasks.php'
      ),
      "<?php\n" .
      '$main_app = \'FooApp\';' . "\n"
    );
    $cli->taskCreateTasksFile('project', 'FooApp');
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
  
}
