<?php
require_once realpath(dirname(__FILE__) . '/../../../../../config.php');

use \Asar\Utility\Cli\BaseTasks;

class Asar_Utility_Cli_BaseTasksTest extends PHPUnit_Framework_TestCase {

  function setUp() {
    $this->base_tasks = new BaseTasks;
    $this->controller = $this->getMock(
      'Asar\Utility\Cli', array(), array(), '', false
    );
    $this->base_tasks->setController($this->controller);
  }
  
  function testGettingVersion() {
    $asar = new Asar;
    $this->controller->expects($this->once())
      ->method('out')
      ->with('Asar Web Framework ' . $asar->getVersion());
    $this->base_tasks->flagVersion();
  }
  
  function testGettingListOfCommands() {
    $this->controller->expects($this->once())
      ->method('getRegisteredTasks')
      ->will($this->returnValue(array(
        'task1', 'task2', 'footask'
      )));
    $this->controllerOutsAt(1, 'Available tasks (3):');
    $this->controllerOutsAt(2, ' task1');
    $this->controllerOutsAt(3, ' task2');
    $this->controllerOutsAt(4, ' footask');
    $this->base_tasks->taskList();
  }
  
  private function controllerOutsAt($i, $output) {
    return $this->controller->expects($this->at($i))
      ->method('out')
      ->with($output);
  }
  
}
