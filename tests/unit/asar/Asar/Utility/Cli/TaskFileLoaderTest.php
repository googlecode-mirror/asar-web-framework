<?php
require_once realpath(dirname(__FILE__) . '/../../../../../config.php');

use \Asar\Utility\Cli\TaskFileLoader;

class Asar_Utility_Cli_TaskFileLoaderTest_TaskFileSample 
  implements \Asar\Utility\Cli\CliInterface
{
  
  function setController(\Asar\Utility\Cli $controller) {}
  function getTaskNamespace() {}
  
}

class Asar_Utility_Cli_TaskFileLoaderTest_TaskFileSample2 
  implements \Asar\Utility\Cli\CliInterface
{
  
  function setController(\Asar\Utility\Cli $controller) {}
  function getTaskNamespace() {}
}

class Asar_Utility_Cli_TaskFileLoaderTest_NotTaskFile {}

class Asar_Utility_Cli_TaskFileLoaderTest extends \PHPUnit_Framework_TestCase {
  
  function setUp() {
    $this->cwd = Asar::getInstance()->getFrameworkTestsDataPath();
    $this->tasks_file = $this->cwd . DIRECTORY_SEPARATOR . 'tasks.php';
    $this->class_file_seek = $this->getMock(
      'Asar\Utility\ClassFilePeek', array('getDefinedClasses')
    );
    $this->cli = $this->getMock(
      'Asar\Utility\Cli', array('register'), array(), '', false
    );
    $this->task_file_loader = new TaskFileLoader(
      $this->cwd, $this->class_file_seek, $this->cli
    );
    
  }
  
  function testTaskFileExists() {
    $this->assertTrue($this->task_file_loader->isFileExists());
  }
  
  private function taskFileLoaderWithNoTaskFile() {
    return new TaskFileLoader(
      dirname(__FILE__), $this->class_file_seek, $this->cli
    );
  }
  
  function testTaskNotFileExists() {
    $this->task_file_loader = $this->taskFileLoaderWithNoTaskFile();
    $this->assertFalse($this->task_file_loader->isFileExists());
  }
  
  function testRegisterUsesFileSeek() {
    $this->class_file_seek->expects($this->once())
      ->method('getDefinedClasses')
      ->with($this->tasks_file);
    $this->task_file_loader->registerTasks();
  }
  
  function testRegisterDoesNotUseFileSeekWhenFileDoesnNotExist() {
    $this->task_file_loader = $this->taskFileLoaderWithNoTaskFile();
    $this->class_file_seek->expects($this->never())
      ->method('getDefinedClasses');
    $this->task_file_loader->registerTasks();
  }
  
  function testRegisterUsesValuesFromFileSeek() {
    $task_classes = array(
      'Asar_Utility_Cli_TaskFileLoaderTest_TaskFileSample',
      'Asar_Utility_Cli_TaskFileLoaderTest_TaskFileSample2'
    );
    $this->class_file_seek->expects($this->once())
      ->method('getDefinedClasses')
      ->will($this->returnValue($task_classes));
    $this->cli->expects($this->at(0))
      ->method('register')
      ->with(new Asar_Utility_Cli_TaskFileLoaderTest_TaskFileSample);
    $this->cli->expects($this->at(1))
      ->method('register')
      ->with(new Asar_Utility_Cli_TaskFileLoaderTest_TaskFileSample2);
    $this->task_file_loader->registerTasks();
  }
  
  function testRegisterDoesNotCliRegisterWhenFileDoesnNotExist() {
    $this->task_file_loader = $this->taskFileLoaderWithNoTaskFile();
    $this->cli->expects($this->never())
      ->method('register');
    $this->task_file_loader->registerTasks();
  }
  
  function testRegisterOnlyTaskFiles() {
    $task_classes = array(
      'Asar_Utility_Cli_TaskFileLoaderTest_NotTaskFile'
    );
    $this->class_file_seek->expects($this->once())
      ->method('getDefinedClasses')
      ->will($this->returnValue($task_classes));
    $this->cli->expects($this->never())
      ->method('register');
    $this->task_file_loader->registerTasks();
  }
  
}
