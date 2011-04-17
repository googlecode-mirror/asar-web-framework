<?php
namespace Asar\EnvironmentHelper;

use \Asar\Utility\Cli as UtilityCli;
use \Asar\Utility\Cli\TaskFileLoader;

/**
 * Environment Helper for setting up the CLI interface
 *
 * @package Asar
 * @subpackage core
 */
class Cli {

  private $cli, $args, $tasklists = array(), $task_file_loader;

  /**
   * @param Asar_Utility_Cli $cli
   * @param array $args arguments usually passed by the global variable $argv
   * @param array $tasklists an array of tasklists to register to the cli
   * @param Asar_Utility_Cli_TaskFileLoader $task_file_loader
   */
  function __construct(
    UtilityCli $cli, array $args, array $tasklists,
    TaskFileLoader $task_file_loader
  ) {
    $this->cli = $cli;
    $this->args = $args;
    $this->tasklists = $tasklists;
    $this->task_file_loader = $task_file_loader;
  }
  
  /**
   * Registers the tasks from tasklists, loads the task file through
   * $this->task_file_loader, and executes the commands.
   */
  function run() {
    foreach ($this->tasklists as $tasklist) {
      $this->cli->register($tasklist);
    }
    $this->task_file_loader->registerTasks();
    $this->cli->execute($this->args);
  }
  
}
