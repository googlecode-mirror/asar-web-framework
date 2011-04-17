<?php
namespace Asar\Utility\Cli\Executor;

use \Asar\Utility\Cli\CliInterface;
use \Asar\Utility\Cli\Command;
/**
 * @package Asar
 * @subpackage core
 */
interface ExecutorInterface {
  function registerTasks(CliInterface $tasklist, $namespace = null);
  function execute(Command $command);
  function getRegisteredTasks();
}
