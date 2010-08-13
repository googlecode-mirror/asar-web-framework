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
  
  private function getFullPath($path) {
    return $this->dir . DIRECTORY_SEPARATOR . $path;
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
    $path = 'folder' . DIRECTORY_SEPARATOR . 'thefile.txt';
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
      $path = rtrim(
        $this->getFullPath(
          $project_path . DIRECTORY_SEPARATOR . $dir
        ), DIRECTORY_SEPARATOR
      );
      $this->file_helper->expects($this->at($i))
        ->method('createDir')
        ->with($path);
      $i++;
    }
    $this->tasks->taskCreateProjectDirectories('xdir');
  }
  
  function testCreatingProjectDirectoriesCreatesAppDirectoryWhenSpecified() {
    $this->markTestIncomplete();
    $app_path = Asar::constructPath(
      self::getTempDir() . 'adir', 'apps', 'TheApp'
    );
    $directories = array(
      '', 'Resource', 'Representation'
    );
    ob_start();
    $this->tasks->taskCreateProjectDirectories('adir', 'TheApp');
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
    $this->tasks->taskCreateHtaccessFile($project_dir);
    ob_end_clean();
    // The expected file
    return Asar::constructPath(
      self::getTempDir(), $project_dir, 'web', '.htaccess'
    );
  }
  
  function testCreatingHtaccessFileForProject() {
    $this->markTestIncomplete();
    $this->assertFileExists(
      $this->_testHtAccess('directory')
    );
  }
  
  function testCreatingHtaccessFileForProjectWithProperContents() {
    $this->markTestIncomplete();
    $this->assertEquals(
      $this->htaccess_contents,
      file_get_contents($this->_testHtAccess('another-directory'))
    );
  }
  
  function testCreatingHtaccessUsesCreateFileTask() {
    $this->markTestIncomplete();
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
  
  function testCreatingTestConfigFile() {
    $this->markTestIncomplete();
    $cli = $this->mock(array('taskCreateFile'));
    $cli->expects($this->once())
      ->method('taskCreateFile')
      ->with(
        Asar::constructPath(
          self::getTempDir(), 'thedirectory', 'tests', 'config.php'
        ),
        "<?php\n".
        "set_include_path(\n".
        "  realpath(dirname(__FILE__) . '/../apps') . PATH_SEPARATOR .\n".
        "  realpath(dirname(__FILE__) . '/../lib/vendor') . PATH_SEPARATOR .\n".
        "  get_include_path()\n".
        ");\n"
      );
    $cli->taskCreateTestConfigFile('thedirectory');
  }
  
  function testCreatingProject() {
    $this->markTestIncomplete();
    $cli = $this->mock(array(
      'taskCreateProjectDirectories', 'taskCreateApplication',
      'taskCreateResource', 'taskCreateFrontController',
      'taskCreateHtaccessFile', 'taskCreateTasksFile',
      'taskCreateTestConfigFile'
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
      ->method('taskCreateTestConfigFile')
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
    $this->markTestIncomplete();
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
    $this->markTestIncomplete();
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
    $this->markTestIncomplete();
    $this->testCreateResource(array(
      'project' => 'aproject',
      'app' => 'AnApp',
      'url' => '/',
      'expected_resource_name' => 'Index',
      'expected_resource_path' => 'Index'
    ));
  }
  
  function testCreateResourceMultiLevelPath() {
    $this->markTestIncomplete();
    $this->testCreateResource(array(
      'project' => 'foo',
      'app' => 'BarApp',
      'url' => '/foo/bar/baz',
      'expected_resource_name' => 'Foo_Bar_Baz',
      'expected_resource_path' => 'Foo/Bar/Baz'
    ));
  }
  
  function testCreateResourceMultiLevelPathWithWildCard() {
    $this->markTestIncomplete();
    $this->testCreateResource(array(
      'project' => 'foo',
      'app' => 'BarApp',
      'url' => '/foo/*/baz',
      'expected_resource_name' => 'Foo__Item_Baz',
      'expected_resource_path' => 'Foo/_Item/Baz'
    ));
  }
  
  function testCreatingResourceInProjectContext() {
    $this->markTestIncomplete();
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
  
}
